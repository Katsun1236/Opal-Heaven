<?php

declare(strict_types=1);

namespace opal\core\role;

use opal\core\database\DatabaseManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionAttachment;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class RoleManager implements Listener{

    private static ?self $instance = null;
    private DatabaseManager $database;
    private PluginBase $plugin;

    public const ROLE_ADMIN = "ADMIN";
    public const ROLE_JOUEUR = "JOUEUR";

    /** @var array<string, PermissionAttachment> */
    private array $attachments = [];

    public function __construct(DatabaseManager $database, PluginBase $plugin){
        self::$instance = $this;
        $this->database = $database;
        $this->plugin = $plugin;

        // Enregistrer la permission étoile '*' et les permissions de rôles
        $pm = PermissionManager::getInstance();
        if($pm->getPermission("*") === null){
            $pm->addPermission(new Permission("*", "Toutes les permissions globales (Wildcard)"));
        }
        if($pm->getPermission("opal.admin") === null){
            $pm->addPermission(new Permission("opal.admin", "Permissions d'administrateur Opal"));
        }
        if($pm->getPermission("opal.player") === null){
            $pm->addPermission(new Permission("opal.player", "Permissions de joueur Opal"));
        }
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function getRole(string $xuidOrName) : string{
        $db = $this->database->getDb();
        $stmt = $db->prepare("SELECT role FROM players WHERE xuid = :val OR username = :val COLLATE NOCASE LIMIT 1");
        $stmt->bindValue(":val", $xuidOrName, SQLITE3_TEXT);
        $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        if($res !== false && !empty($res["role"])){
            return strtoupper($res["role"]);
        }
        return self::ROLE_JOUEUR;
    }

    public function setRole(string $username, string $role) : bool{
        $role = strtoupper($role);
        if(!in_array($role, [self::ROLE_ADMIN, self::ROLE_JOUEUR], true)){
            return false;
        }

        $db = $this->database->getDb();
        $stmt = $db->prepare("UPDATE players SET role = :role WHERE username = :name COLLATE NOCASE");
        $stmt->bindValue(":role", $role, SQLITE3_TEXT);
        $stmt->bindValue(":name", $username, SQLITE3_TEXT);
        $stmt->execute();

        // Si le joueur est en ligne, actualiser immédiatement ses permissions
        $player = $this->plugin->getServer()->getPlayerExact($username);
        if($player !== null && $player->isOnline()){
            $this->applyPermissions($player, $role);
            $player->sendMessage("§a✨ Votre rôle a été mis à jour : " . $this->getRolePrefix($role));
        }

        return true;
    }

    public function applyPermissions(Player $player, ?string $role = null) : void{
        $role = $role ?? $this->getRole($player->getXuid());
        $name = $player->getName();

        // Retirer ancien attachement
        if(isset($this->attachments[$name])){
            $player->removeAttachment($this->attachments[$name]);
            unset($this->attachments[$name]);
        }

        $attachment = $player->addAttachment($this->plugin);
        $this->attachments[$name] = $attachment;

        if($role === self::ROLE_ADMIN){
            // Permission Étoile * (Tous les droits absolus)
            $attachment->setPermission("*", true);
            $attachment->setPermission("pocketmine.group.operator", true);
            $attachment->setPermission("pocketmine.group.user", true);
            $attachment->setPermission("opal.admin", true);
            $attachment->setPermission("opal.player", true);

            // Définir comme OP
            if(!$player->getServer()->isOp($name)){
                $player->getServer()->addOp($name);
            }
        }else{
            $attachment->setPermission("opal.player", true);
            $attachment->setPermission("opal.admin", false);
            $attachment->setPermission("*", false);
            if($player->getServer()->isOp($name)){
                $player->getServer()->removeOp($name);
            }
        }

        // Synchroniser immédiatement l'autocomplétion des commandes côté client Bedrock !
        $player->getNetworkSession()->syncAvailableCommands();
    }

    public function onJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $name = $player->getName();

        // Attribution prioritaire automatique pour Katsun1246
        if(strcasecmp($name, "Katsun1246") === 0){
            $this->setRole($name, self::ROLE_ADMIN);
            $this->applyPermissions($player, self::ROLE_ADMIN);
            $player->sendMessage("§c§l[ADMIN] §r§eBienvenue Créateur Katsun1246 ! Permissions étoile (*) accordées.");
            return;
        }

        $this->applyPermissions($player);
    }

    public function getRolePrefix(string $role) : string{
        return match($role){
            self::ROLE_ADMIN => "§c§l[ADMIN]§r ",
            default => "§7[JOUEUR]§r "
        };
    }
}
