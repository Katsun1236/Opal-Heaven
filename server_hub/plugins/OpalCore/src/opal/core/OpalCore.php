<?php

declare(strict_types=1);

namespace opal\core;

use opal\core\ah\AuctionHouseManager;
use opal\core\bounty\BountyManager;
use opal\core\chat\ChatManager;
use opal\core\combat\CombatTagManager;
use opal\core\combat\PostureManager;
use opal\core\database\DatabaseManager;
use opal\core\economy\EconomyManager;
use opal\core\faction\FactionClaim;
use opal\core\faction\FactionManager;
use opal\core\npc\NPCManager;
use opal\core\orders\OrderManager;
use opal\core\quests\QuestManager;
use opal\core\sticks\TacticalSticksManager;
use opal\core\teleport\TeleportManager;
use opal\core\training\CombatDummyManager;
use opal\core\ui\FormManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

class OpalCore extends PluginBase implements Listener{

    private static self $instance;

    private DatabaseManager $database;
    private EconomyManager $economy;
    private FormManager $formManager;
    private CombatTagManager $combatTag;
    private PostureManager $posture;
    private OrderManager $orders;
    private FactionManager $factions;
    private FactionClaim $claims;
    private BountyManager $bounties;
    private ChatManager $chat;
    private AuctionHouseManager $ah;
    private QuestManager $quests;
    private TacticalSticksManager $sticks;
    private TeleportManager $teleport;
    private NPCManager $npc;
    private CombatDummyManager $dummy;

    private string $serverRole = "faction"; // "hub" ou "faction"

    public static function getInstance() : self{
        return self::$instance;
    }

    public function onEnable() : void{
        self::$instance = $this;
        $this->saveDefaultConfig();
        $this->serverRole = (string)$this->getConfig()->get("server-role", "faction");

        $this->getLogger()->info("§d🌌 Initialisation d'OpalCore en mode [{$this->serverRole}]...");

        // 1. Initialiser la Base de Données Partagée
        $this->database = new DatabaseManager($this->getDataFolder());

        // 2. Initialiser les Managers
        $this->economy = new EconomyManager($this->database);
        $this->formManager = new FormManager();
        $this->combatTag = new CombatTagManager();
        $this->posture = new PostureManager($this->database);
        $this->orders = new OrderManager($this->database);
        $this->factions = new FactionManager($this->database);
        $this->claims = new FactionClaim($this->database, $this->factions);
        $this->bounties = new BountyManager($this->database, $this->economy);
        $this->chat = new ChatManager($this->database, $this->factions, $this->orders, $this->posture, $this->economy);
        $this->ah = new AuctionHouseManager($this->database, $this->economy);
        $this->quests = new QuestManager($this->database, $this->economy, $this->orders);
        $this->sticks = new TacticalSticksManager();
        $this->teleport = new TeleportManager($this->combatTag);
        $this->npc = new NPCManager($this->orders, $this->quests, $this->ah);
        $this->dummy = new CombatDummyManager();
        new \opal\core\trade\TradeManager($this->economy, $this->combatTag);

        // 3. Enregistrer les Écouteurs d'Événements
        $pm = $this->getServer()->getPluginManager();
        $pm->registerEvents($this, $this);
        $pm->registerEvents($this->formManager, $this);
        $pm->registerEvents($this->combatTag, $this);
        $pm->registerEvents($this->posture, $this);
        $pm->registerEvents($this->claims, $this);
        $pm->registerEvents($this->bounties, $this);
        $pm->registerEvents($this->chat, $this);
        $pm->registerEvents($this->sticks, $this);
        $pm->registerEvents($this->teleport, $this);
        $pm->registerEvents($this->dummy, $this);

        // 4. Tâches Récurrentes (Tick de Posture, Téléportation et Mannequin)
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void{
            $this->posture->decayTick();
            $this->teleport->tick();
            $this->dummy->tick();
        }), 20);

        $this->getLogger()->info("§a✔ OpalCore activé avec succès ! Tous les 15 systèmes sont opérationnels.");
    }

    public function onJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $this->economy->registerPlayer($player);

        // Message de bienvenue immersif
        $player->sendMessage("§d========================================");
        $player->sendMessage("§l§e🌌 BIENVENUE SUR OPAL HEAVEN §r§7(Bedrock)");
        $player->sendMessage("§7Tapez §a/order §7pour choisir votre Voie Céleste !");
        $player->sendMessage("§7Tapez §6/f §7pour créer ou rejoindre une Faction.");
        $player->sendMessage("§7Tapez §b/quests §7pour ouvrir votre carnet de quêtes.");
        $player->sendMessage("§d========================================");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§cCette commande doit être exécutée en jeu.");
            return true;
        }

        switch(strtolower($command->getName())){
            case "money":
            case "bal":
            case "balance":
                $bal = $this->economy->getBalance($sender->getXuid());
                $sender->sendMessage("§e💰 Votre solde : §6" . number_format($bal) . " Or Céleste");
                return true;

            case "pay":
                if(count($args) < 2){
                    $sender->sendMessage("§cUsage: /pay <joueur> <montant>");
                    return true;
                }
                $target = $this->getServer()->getPlayerByPrefix($args[0]);
                if($target === null){
                    $sender->sendMessage("§c❌ Joueur introuvable.");
                    return true;
                }
                $amount = (int)$args[1];
                if($amount <= 0){
                    $sender->sendMessage("§c❌ Montant invalide.");
                    return true;
                }
                if($this->economy->transfer($sender->getXuid(), $target->getXuid(), $amount)){
                    $sender->sendMessage("§a✔ Vous avez envoyé §e{$amount} Or §aà §e" . $target->getName());
                    $target->sendMessage("§a✔ Vous avez reçu §e{$amount} Or §ade la part de §e" . $sender->getName());
                }else{
                    $sender->sendMessage("§c❌ Solde insuffisant !");
                }
                return true;

            case "order":
                $this->orders->openOrderUI($sender);
                return true;

            case "quests":
                $this->quests->openQuestsMenu($sender);
                return true;

            case "ah":
                $this->ah->openMainMenu($sender);
                return true;

            case "bounty":
                if(count($args) < 2){
                    $sender->sendMessage("§cUsage: /bounty <joueur> <montant>");
                    return true;
                }
                $target = $this->getServer()->getPlayerByPrefix($args[0]);
                if($target === null){
                    $sender->sendMessage("§c❌ Joueur introuvable.");
                    return true;
                }
                $amount = (int)$args[1];
                $this->bounties->placeBounty($sender, $target, $amount);
                return true;

            case "dummy":
            case "parry":
                $this->dummy->startTraining($sender);
                return true;

            case "f":
            case "faction":
                $this->handleFactionCommand($sender, $args);
                return true;

            case "trade":
                $tm = \opal\core\trade\TradeManager::getInstance();
                if(isset($args[0]) && strtolower($args[0]) === "accept"){
                    $tm->acceptTrade($sender);
                }elseif(isset($args[0])){
                    $target = $this->getServer()->getPlayerByPrefix($args[0]);
                    if($target !== null){
                        $tm->requestTrade($sender, $target);
                    }else{
                        $sender->sendMessage("§c❌ Joueur introuvable.");
                    }
                }else{
                    $sender->sendMessage("§cUsage: /trade <joueur> ou /trade accept");
                }
                return true;

            case "hub":
                $sender->sendMessage("§d🌌 Téléportation vers le Hub Céleste...");
                $this->teleport->startServerTransfer($sender, "127.0.0.1", 19135, 1);
                return true;

            case "spawn":
                $this->teleport->startTeleport($sender, $this->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn(), 3);
                return true;
        }

        return false;
    }

    private function handleFactionCommand(Player $player, array $args) : void{
        $sub = strtolower($args[0] ?? "help");
        switch($sub){
            case "create":
                if(!isset($args[1])){
                    $player->sendMessage("§cUsage: /f create <nom>");
                    return;
                }
                $this->factions->createFaction($player, $args[1]);
                break;

            case "disband":
                $this->factions->disbandFaction($player);
                break;

            case "claim":
                $this->claims->claimChunk($player);
                break;

            case "unclaim":
                $this->claims->unclaimChunk($player);
                break;

            case "map":
                $this->claims->renderMap($player);
                break;

            case "sethome":
                $fac = $this->factions->getPlayerFaction($player);
                if($fac === null || !in_array($fac["rank"], ["OFFICIER", "CHEF"], true)){
                    $player->sendMessage("§c❌ Seuls les Officiers ou le Chef peuvent définir le Home !");
                    return;
                }
                $this->factions->setFactionHome((int)$fac["id"], $player->getPosition());
                $player->sendMessage("§a✔ Faction Home défini ici !");
                break;

            case "home":
                $fac = $this->factions->getPlayerFaction($player);
                if($fac === null){
                    $player->sendMessage("§c❌ Vous n'avez pas de faction.");
                    return;
                }
                $home = $this->factions->getFactionHome((int)$fac["id"]);
                if($home === null){
                    $player->sendMessage("§c❌ Aucun home défini pour votre faction.");
                    return;
                }
                $w = $this->getServer()->getWorldManager()->getWorldByName($home["heart_world"]);
                if($w !== null){
                    $pos = new \pocketmine\world\Position((float)$home["heart_x"], (float)$home["heart_y"], (float)$home["heart_z"], $w);
                    $this->teleport->startTeleport($player, $pos, 5);
                }
                break;

            case "bank":
                $action = strtolower($args[1] ?? "");
                $amount = (int)($args[2] ?? 0);
                $fac = $this->factions->getPlayerFaction($player);
                if($fac === null){
                    $player->sendMessage("§c❌ Vous n'avez pas de faction.");
                    return;
                }
                $fid = (int)$fac["id"];
                if($action === "deposit" && $amount > 0){
                    if($this->economy->removeMoney($player->getXuid(), $amount)){
                        $this->factions->depositBank($fid, $amount);
                        $player->sendMessage("§a✔ Dépôt de §e{$amount} Or §adans la banque de faction !");
                    }else{
                        $player->sendMessage("§c❌ Solde personnel insuffisant.");
                    }
                }elseif($action === "withdraw" && $amount > 0){
                    if(!in_array($fac["rank"], ["OFFICIER", "CHEF"], true)){
                        $player->sendMessage("§c❌ Seuls les Officiers et le Chef peuvent retirer de l'or !");
                        return;
                    }
                    if($this->factions->withdrawBank($fid, $amount)){
                        $this->economy->addMoney($player->getXuid(), $amount);
                        $player->sendMessage("§a✔ Retrait de §e{$amount} Or §aeffectué avec succès !");
                    }else{
                        $player->sendMessage("§c❌ Solde de banque de faction insuffisant.");
                    }
                }else{
                    $player->sendMessage("§eBanque de faction : §6" . number_format($fac["bank_balance"]) . " Or\n§7Usage: /f bank deposit <montant> ou /f bank withdraw <montant>");
                }
                break;

            case "info":
            default:
                $fac = $this->factions->getPlayerFaction($player);
                if($fac === null){
                    $player->sendMessage("§7Usage: /f create <nom> | /f claim | /f map | /f bank | /f sethome | /f home");
                }else{
                    $player->sendMessage("§6=== Faction : §e" . $fac["name"] . " §6===");
                    $player->sendMessage("§fRang : §a" . $fac["rank"] . " §8| §fPower : §b" . $fac["power"] . "/50");
                    $player->sendMessage("§fBanque : §e" . number_format($fac["bank_balance"]) . " Or");
                    $shieldStatus = $this->factions->isShieldActive($fac) ? "§aACTIF (Invulnérable)" : "§cEN SIÈGE (Vulnérable 18h-23h)";
                    $player->sendMessage("§fBouclier Stellaire : " . $shieldStatus);
                }
                break;
        }
    }
}
