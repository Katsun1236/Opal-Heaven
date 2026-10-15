<?php

declare(strict_types=1);

namespace opalheaven\mod;

use opalheaven\mod\database\ModDatabase;
use opalheaven\mod\listener\FreezeListener;
use opalheaven\mod\listener\ChatAutoModListener;
use opalheaven\mod\listener\ModListener;
use opalheaven\mod\commands\StaffCommand;
use opalheaven\mod\commands\BanCommand;
use opalheaven\mod\commands\UnbanCommand;
use opalheaven\mod\commands\MuteCommand;
use opalheaven\mod\commands\UnmuteCommand;
use opalheaven\mod\commands\WarnCommand;
use opalheaven\mod\commands\FreezeCommand;
use opalheaven\mod\commands\UnfreezeCommand;
use opalheaven\mod\commands\VanishCommand;
use opalheaven\mod\commands\ReportCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

class OpalMod extends PluginBase {

    private static self $instance;
    private ModDatabase $database;

    /** @var array<string, string> Frozen players: XUID => StaffName */
    private array $frozen = [];

    /** @var array<string, Player> Vanished players: XUID => Player */
    private array $vanished = [];

    public static function getInstance() : self {
        return self::$instance;
    }

    protected function onEnable() : void {
        self::$instance = $this;

        require_once __DIR__ . "/commands/ModCommands.php";

        @mkdir($this->getDataFolder());
        $dbPath = $this->getDataFolder() . "moderation.db";
        $this->database = new ModDatabase($dbPath);

        // Enregistrement des Listeners
        $pm = $this->getServer()->getPluginManager();
        $pm->registerEvents(new ModListener($this), $this);
        $pm->registerEvents(new FreezeListener($this), $this);
        $pm->registerEvents(new ChatAutoModListener($this), $this);

        // Enregistrement des Commandes
        $cmdMap = $this->getServer()->getCommandMap();
        $cmdMap->register("opalmod", new StaffCommand($this));
        $cmdMap->register("opalmod", new BanCommand($this));
        $cmdMap->register("opalmod", new UnbanCommand($this));
        $cmdMap->register("opalmod", new MuteCommand($this));
        $cmdMap->register("opalmod", new UnmuteCommand($this));
        $cmdMap->register("opalmod", new WarnCommand($this));
        $cmdMap->register("opalmod", new FreezeCommand($this));
        $cmdMap->register("opalmod", new UnfreezeCommand($this));
        $cmdMap->register("opalmod", new VanishCommand($this));
        $cmdMap->register("opalmod", new ReportCommand($this));

        // Tâche périodique (1 seconde) : affichage du titre de freeze
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void {
            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                if ($this->isFrozen($player)) {
                    $player->sendTitle("§c§lVOUS ÊTES FREEZE", "§eNe quittez pas ! Rejoignez discord.gg/opalheaven", 5, 25, 5);
                }
            }
        }), 20);

        $this->getLogger()->info("§d[OpalMod] §fSuite de modération active avec succès (SQLite, AutoMod, FormUI, Freeze, Vanish) !");
    }

    public function getDatabase() : ModDatabase {
        return $this->database;
    }

    public function isFrozen(Player $player) : bool {
        return isset($this->frozen[$player->getXuid()]);
    }

    public function freeze(Player $player, string $staffName) : void {
        $this->frozen[$player->getXuid()] = $staffName;
        $player->sendTitle("§c§lVOUS ÊTES FREEZE", "§eNe quittez pas sous peine de bannissement", 10, 60, 10);
        $player->sendMessage("§c§l[FREEZE] §r§cVous avez été figé par le modérateur §e{$staffName} §c!\n§7Vous disposez de 5 minutes pour rejoindre le vocal de vérification.");
        $this->database->logStaffAction($staffName, "FREEZE", $player->getName(), "Joueur placé sous vérification");
    }

    public function unfreeze(Player $player) : void {
        unset($this->frozen[$player->getXuid()]);
        $player->sendMessage("§a§l[FREEZE] §r§aVous avez été libéré. Bon jeu sur Opal Heaven !");
    }

    public function isVanished(Player $player) : bool {
        return isset($this->vanished[$player->getXuid()]);
    }

    public function setVanished(Player $player, bool $vanished) : void {
        $xuid = $player->getXuid();
        if ($vanished) {
            $this->vanished[$xuid] = $player;
            foreach ($this->getServer()->getOnlinePlayers() as $other) {
                if ($other !== $player && !$other->hasPermission("opalmod.vanish")) {
                    $other->hidePlayer($player);
                }
            }
            $player->setSilent(true);
        } else {
            unset($this->vanished[$xuid]);
            foreach ($this->getServer()->getOnlinePlayers() as $other) {
                if ($other !== $player) {
                    $other->showPlayer($player);
                }
            }
            $player->setSilent(false);
        }
    }

    /**
     * @return Player[]
     */
    public function getVanishedPlayers() : array {
        return array_values($this->vanished);
    }
}
