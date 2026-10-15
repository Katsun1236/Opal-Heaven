<?php

declare(strict_types=1);

namespace opalheaven\core\orders;

use pocketmine\player\Player;
use pocketmine\world\sound\XpLevelUpSound;

class OrderManager {

    /** @var array<string, ?OrderType> Active order per XUID */
    private array $activeOrder = [];

    /** @var array<string, array<string, int>> Levels per XUID and OrderType name */
    private array $levels = [];

    /** @var array<string, array<string, int>> Experience per XUID and OrderType name */
    private array $experience = [];

    /** @var array<string, int> Marques de Sang per XUID */
    private array $bloodMarks = [];

    private \SQLite3 $db;
    private SpecializationManager $specManager;

    public function __construct(string $dbPath) {
        $this->db = new \SQLite3($dbPath);
        $this->db->exec("CREATE TABLE IF NOT EXISTS player_orders (
            xuid TEXT NOT NULL,
            order_name TEXT NOT NULL,
            level INTEGER DEFAULT 1,
            xp INTEGER DEFAULT 0,
            PRIMARY KEY (xuid, order_name)
        )");
        $this->db->exec("CREATE TABLE IF NOT EXISTS active_orders (
            xuid TEXT PRIMARY KEY,
            order_name TEXT NOT NULL
        )");
        $this->db->exec("CREATE TABLE IF NOT EXISTS player_blood_marks (
            xuid TEXT PRIMARY KEY,
            marks INTEGER DEFAULT 0
        )");

        $this->specManager = new SpecializationManager($this->db);
    }

    public function getDatabase() : \SQLite3 {
        return $this->db;
    }

    public function getSpecializationManager() : SpecializationManager {
        return $this->specManager;
    }

    public function loadPlayer(Player $player) : void {
        $xuid = $player->getXuid();
        
        // Charger l'ordre actif
        $stmt = $this->db->prepare("SELECT order_name FROM active_orders WHERE xuid = :xuid");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $res = $stmt->execute();
        if ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $this->activeOrder[$xuid] = OrderType::tryFrom($row["order_name"]);
        } else {
            $this->activeOrder[$xuid] = null;
        }

        // Charger les niveaux
        $stmt = $this->db->prepare("SELECT order_name, level, xp FROM player_orders WHERE xuid = :xuid");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $res = $stmt->execute();
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $name = $row["order_name"];
            $this->levels[$xuid][$name] = (int) $row["level"];
            $this->experience[$xuid][$name] = (int) $row["xp"];
        }

        // Charger les Marques de Sang
        $stmt = $this->db->prepare("SELECT marks FROM player_blood_marks WHERE xuid = :xuid");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $res = $stmt->execute();
        if ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $this->bloodMarks[$xuid] = (int) $row["marks"];
        } else {
            $this->bloodMarks[$xuid] = 0;
        }

        $this->specManager->loadPlayer($player);
    }

    public function getActiveOrder(Player $player) : ?OrderType {
        return $this->activeOrder[$player->getXuid()] ?? null;
    }

    public function setActiveOrder(Player $player, OrderType $order) : void {
        $xuid = $player->getXuid();
        $this->activeOrder[$xuid] = $order;

        $stmt = $this->db->prepare("INSERT OR REPLACE INTO active_orders (xuid, order_name) VALUES (:xuid, :order_name)");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $stmt->bindValue(":order_name", $order->value, SQLITE3_TEXT);
        $stmt->execute();

        $player->sendMessage("§d[Opal Heaven] §fVous avez rejoint l'§l{$order->getDisplayName()} §r§f!");
    }

    public function getLevel(Player $player, OrderType $order) : int {
        return $this->levels[$player->getXuid()][$order->value] ?? 1;
    }

    public function getXp(Player $player, OrderType $order) : int {
        return $this->experience[$player->getXuid()][$order->value] ?? 0;
    }

    /**
     * Courbe polynomiale mathématique par morceaux (Livre Blanc Section 5.A)
     */
    public function getRequiredXp(int $level) : int {
        if ($level <= 10) {
            return (int)(500 + 100 * $level);
        }
        if ($level <= 25) {
            return (int)(1500 + 250 * pow($level - 10, 1.35));
        }
        if ($level <= 40) {
            return (int)(5500 + 400 * pow($level - 25, 1.50));
        }
        if ($level <= 50) {
            return (int)(10500 + 850 * pow($level - 40, 1.70));
        }
        // Parangon (50+) : fixe linéaire 15 000 XP
        return 15000;
    }

    public function addXp(Player $player, OrderType $order, int $amount) : void {
        $xuid = $player->getXuid();
        $currentLevel = $this->getLevel($player, $order);
        $currentXp = $this->getXp($player, $order);
        $newXp = $currentXp + $amount;

        $needed = $this->getRequiredXp($currentLevel);
        if ($newXp >= $needed) {
            $currentLevel++;
            $newXp -= $needed;
            $this->levels[$xuid][$order->value] = $currentLevel;
            $this->experience[$xuid][$order->value] = $newXp;

            // Notification de montée de rang
            $player->getWorld()->addSound($player->getPosition(), new XpLevelUpSound(100));
            $player->sendTitle("§6§lNIVEAU SUPÉRIEUR !", "{$order->getDisplayName()} §fNiveau §e{$currentLevel}", 10, 50, 10);
            $player->sendMessage("§6§l[ASCENSION] §r§fFélicitations ! Vous atteignez le niveau §e{$currentLevel} §fdans l'{$order->getDisplayName()} §f!");

            // Déblocage de la Spécialisation au niveau 10
            if ($currentLevel === 10) {
                $player->sendMessage("§d§l[NOUVELLE MAÎTRISE] §r§fVous avez atteint le niveau 10 ! Choisissez votre spécialisation avec §e/ordre §f!");
            }
        } else {
            $this->experience[$xuid][$order->value] = $newXp;
        }

        $this->savePlayerOrder($xuid, $order->value, $currentLevel, $newXp);
    }

    public function getBloodMarks(Player $player) : int {
        return $this->bloodMarks[$player->getXuid()] ?? 0;
    }

    public function addBloodMarks(Player $player, int $amount) : void {
        $xuid = $player->getXuid();
        $this->bloodMarks[$xuid] = ($this->bloodMarks[$xuid] ?? 0) + $amount;

        $stmt = $this->db->prepare("INSERT OR REPLACE INTO player_blood_marks (xuid, marks) VALUES (:xuid, :marks)");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $stmt->bindValue(":marks", $this->bloodMarks[$xuid], SQLITE3_INTEGER);
        $stmt->execute();

        $player->sendActionBarMessage("§c§l+{$amount} Marques de Sang §r§7(Total: {$this->bloodMarks[$xuid]})");
    }

    public function removeBloodMarks(Player $player, int $amount) : bool {
        $xuid = $player->getXuid();
        $current = $this->bloodMarks[$xuid] ?? 0;
        if ($current < $amount) {
            return false;
        }

        $this->bloodMarks[$xuid] = $current - $amount;
        $stmt = $this->db->prepare("INSERT OR REPLACE INTO player_blood_marks (xuid, marks) VALUES (:xuid, :marks)");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $stmt->bindValue(":marks", $this->bloodMarks[$xuid], SQLITE3_INTEGER);
        $stmt->execute();
        return true;
    }

    private function savePlayerOrder(string $xuid, string $orderName, int $level, int $xp) : void {
        $stmt = $this->db->prepare("INSERT OR REPLACE INTO player_orders (xuid, order_name, level, xp) VALUES (:xuid, :order_name, :level, :xp)");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $stmt->bindValue(":order_name", $orderName, SQLITE3_TEXT);
        $stmt->bindValue(":level", $level, SQLITE3_INTEGER);
        $stmt->bindValue(":xp", $xp, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public function savePlayer(Player $player) : void {
        $xuid = $player->getXuid();
        if (isset($this->levels[$xuid])) {
            foreach ($this->levels[$xuid] as $orderName => $level) {
                $xp = $this->experience[$xuid][$orderName] ?? 0;
                $this->savePlayerOrder($xuid, $orderName, $level, $xp);
            }
        }
    }
}
