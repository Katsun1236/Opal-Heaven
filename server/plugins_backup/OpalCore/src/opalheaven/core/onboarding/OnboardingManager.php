<?php

declare(strict_types=1);

namespace opalheaven\core\onboarding;

use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\item\Item;

class OnboardingManager {

    /** @var array<string, int> Blessing shield expiration timestamp */
    private array $blessingShieldUntil = [];

    private \SQLite3 $db;

    public function __construct(\SQLite3 $db) {
        $this->db = $db;
        $this->db->exec("CREATE TABLE IF NOT EXISTS player_onboarding (
            xuid TEXT PRIMARY KEY,
            joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            blessing_expires_at INTEGER NOT NULL
        )");
    }

    public function handleJoin(Player $player) : void {
        $xuid = $player->getXuid();
        $stmt = $this->db->prepare("SELECT blessing_expires_at FROM player_onboarding WHERE xuid = :xuid");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $res = $stmt->execute();

        if ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $this->blessingShieldUntil[$xuid] = (int)$row["blessing_expires_at"];
        } else {
            // Nouveau joueur : Activer la Bénédiction de 15 minutes (900s)
            $expires = time() + 900;
            $this->blessingShieldUntil[$xuid] = $expires;

            $stmt = $this->db->prepare("INSERT INTO player_onboarding (xuid, blessing_expires_at) VALUES (:xuid, :exp)");
            $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
            $stmt->bindValue(":exp", $expires, SQLITE3_INTEGER);
            $stmt->execute();

            // Starter Pack d'initiation
            $inv = $player->getInventory();
            $inv->addItem(VanillaItems::STONE_SWORD()->setCustomName("§fGlaive d'Initiation"));
            $inv->addItem(VanillaItems::STONE_PICKAXE()->setCustomName("§fPioche de Calcite"));
            $inv->addItem(VanillaItems::BREAD()->setCount(16)->setCustomName("§fPain de Route"));

            $player->sendTitle("§l§dOPAL HEAVEN", "§eBénédiction de 15 min active !", 10, 50, 10);
            $player->sendMessage("§d[Éveil de l'Opale] §fBienvenue ! Votre §bBouclier de Bénédiction §fest actif pendant 15 minutes.");
            $player->sendMessage("§7Vous êtes immunisé contre les agressions PvP pendant que vous explorez les royaumes.");
        }
    }

    public function hasBlessingShield(Player $player) : bool {
        $xuid = $player->getXuid();
        return isset($this->blessingShieldUntil[$xuid]) && $this->blessingShieldUntil[$xuid] > time();
    }

    public function getBlessingRemaining(Player $player) : int {
        $xuid = $player->getXuid();
        return max(0, ($this->blessingShieldUntil[$xuid] ?? 0) - time());
    }
}
