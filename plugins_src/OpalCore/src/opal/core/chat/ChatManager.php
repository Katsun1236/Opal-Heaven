<?php

declare(strict_types=1);

namespace opal\core\chat;

use opal\core\combat\PostureManager;
use opal\core\database\DatabaseManager;
use opal\core\economy\EconomyManager;
use opal\core\faction\FactionManager;
use opal\core\orders\OrderManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;

class ChatManager implements Listener{

    private static ?self $instance = null;
    private DatabaseManager $database;
    private FactionManager $factionManager;
    private OrderManager $orderManager;
    private PostureManager $postureManager;
    private EconomyManager $economy;

    /** @var array<string, int> player_key => current streak */
    private array $killstreaks = [];

    public function __construct(DatabaseManager $database, FactionManager $factionManager, OrderManager $orderManager, PostureManager $postureManager, EconomyManager $economy){
        self::$instance = $this;
        $this->database = $database;
        $this->factionManager = $factionManager;
        $this->orderManager = $orderManager;
        $this->postureManager = $postureManager;
        $this->economy = $economy;
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    private function getPlayerKey(Player $player) : string{
        $xuid = $player->getXuid();
        return !empty($xuid) ? $xuid : strtolower($player->getName());
    }

    public function onChat(PlayerChatEvent $event) : void{
        $player = $event->getPlayer();
        $msg = trim($event->getMessage());
        if($msg === "") return;

        $event->cancel();
        $prefix = substr($msg, 0, 1);

        if($prefix === "@" || $prefix === "$" || $prefix === "!"){
            $cleanMsg = trim(substr($msg, 1));
        }else{
            $cleanMsg = $msg;
        }

        if($cleanMsg === "") return;

        $orderData = $this->orderManager->getPlayerOrder($player);
        $orderTag = $orderData !== null ? "§d[" . $orderData["order_name"] . "]§r " : "";

        $facData = $this->factionManager->getPlayerFaction($player);
        $facTag = $facData !== null ? "§a[" . $facData["name"] . "]§r " : "";

        $roleKey = !empty($player->getXuid()) ? $player->getXuid() : $player->getName();
        $role = \opal\core\role\RoleManager::getInstance()->getRole($roleKey);
        $roleTag = \opal\core\role\RoleManager::getInstance()->getRolePrefix($role);

        // 1. Canal Faction (@)
        if($prefix === "@"){
            if($facData === null){
                $player->sendMessage("§c❌ Vous n'avez pas de faction pour utiliser le canal @ !");
                return;
            }
            $members = $this->factionManager->getFactionMembers((int)$facData["id"]);
            $memberXuids = array_column($members, "xuid");
            $format = "§2(Faction) §a" . $player->getName() . " §7» §f" . $cleanMsg;
            foreach($player->getServer()->getOnlinePlayers() as $online){
                $onlineKey = !empty($online->getXuid()) ? $online->getXuid() : strtolower($online->getName());
                if(in_array($onlineKey, $memberXuids, true) || in_array($online->getXuid(), $memberXuids, true)){
                    $online->sendMessage($format);
                }
            }
            return;
        }

        // 2. Canal Local ($)
        if($prefix === "$"){
            $format = "§e(Local) §f" . $player->getName() . " §7» §e" . $cleanMsg;
            foreach($player->getWorld()->getPlayers() as $nearby){
                if($nearby->getPosition()->distance($player->getPosition()) <= 30.0){
                    $nearby->sendMessage($format);
                }
            }
            return;
        }

        // 3. Canal Global par défaut (!)
        $format = "{$roleTag}{$orderTag}{$facTag}§f" . $player->getName() . " §7» §f" . $cleanMsg;
        $player->getServer()->broadcastMessage($format);
    }

    public function onDeath(PlayerDeathEvent $event) : void{
        $victim = $event->getPlayer();
        $victimKey = $this->getPlayerKey($victim);
        $this->killstreaks[$victimKey] = 0;

        // Mise à jour de la table combat_stats pour la victime
        $db = $this->database->getDb();
        $safeVictim = \SQLite3::escapeString($victimKey);
        $db->exec("INSERT INTO combat_stats (xuid, deaths, killstreak) VALUES ('$safeVictim', 1, 0) ON CONFLICT(xuid) DO UPDATE SET deaths = deaths + 1, killstreak = 0;");

        $cause = $victim->getLastDamageCause();
        if($cause instanceof EntityDamageByEntityEvent){
            $killer = $cause->getDamager();
            if($killer instanceof Player && $killer !== $victim){
                $killerKey = $this->getPlayerKey($killer);
                $streak = ($this->killstreaks[$killerKey] ?? 0) + 1;
                $this->killstreaks[$killerKey] = $streak;

                // Mise à jour de la table combat_stats pour le vainqueur
                $safeKiller = \SQLite3::escapeString($killerKey);
                $db->exec("INSERT INTO combat_stats (xuid, kills, killstreak, best_killstreak) VALUES ('$safeKiller', 1, $streak, $streak) ON CONFLICT(xuid) DO UPDATE SET kills = kills + 1, killstreak = $streak, best_killstreak = MAX(best_killstreak, $streak);");

                // Message de mort contextualisé
                if($this->postureManager->isStaggered($victim)){
                    $event->setDeathMessage("§c⚔ §e" . $killer->getName() . " §ca brisé la garde de §e" . $victim->getName() . " §cavant de l'exécuter d'un coup impitoyable !");
                }else{
                    $event->setDeathMessage("§c☠ §e" . $victim->getName() . " §ca succombé face à §e" . $killer->getName() . " §c!");
                }

                // Annonces de Killstreaks
                if($streak === 5){
                    $victim->getServer()->broadcastMessage("§6🔥 §e" . $killer->getName() . " §fest en Série Sanglante ! §c(5 éliminations consécutives) !");
                }elseif($streak === 10){
                    $victim->getServer()->broadcastMessage("§c⚡ §l" . strtoupper($killer->getName()) . " EST DEVENU UNE LÉGENDE ! §r§eSa tête est automatiquement mise à prix pour §610 000 Or §e!");
                    $db->exec("INSERT INTO bounty_contracts (creator_xuid, target_xuid, reward) VALUES ('SERVER', '$safeKiller', 10000);");
                }
                return;
            }
        }
        $event->setDeathMessage("§7☠ " . $victim->getName() . " a succombé.");
    }

    public function onQuit(PlayerQuitEvent $event) : void{
        $key = $this->getPlayerKey($event->getPlayer());
        unset($this->killstreaks[$key]);
    }
}
