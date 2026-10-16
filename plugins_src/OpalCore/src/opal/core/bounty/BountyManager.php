<?php

declare(strict_types=1);

namespace opal\core\bounty;

use opal\core\database\DatabaseManager;
use opal\core\economy\EconomyManager;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;

class BountyManager implements Listener{

    private static ?self $instance = null;
    private DatabaseManager $database;
    private EconomyManager $economy;

    public function __construct(DatabaseManager $database, EconomyManager $economy){
        self::$instance = $this;
        $this->database = $database;
        $this->economy = $economy;
    }

    public static function getInstance() : self{
        return self::$instance ?? throw new \RuntimeException("BountyManager has not been initialized yet.");
    }

    public function placeBounty(Player $creator, Player $target, int $amount) : bool{
        if(strtolower($creator->getName()) === strtolower($target->getName())){
            $creator->sendMessage("§c❌ Vous ne pouvez pas placer une prime sur votre propre tête !");
            return false;
        }

        if($amount < 500 || $amount > 1_000_000_000){
            $creator->sendMessage("§c❌ La prime doit être comprise entre 500 et 1 000 000 000 Or !");
            return false;
        }

        if(!$this->economy->removeMoney($creator, $amount)){
            $creator->sendMessage("§c❌ Solde insuffisant pour déposer cette prime !");
            return false;
        }

        $creatorKey = !empty($creator->getXuid()) ? $creator->getXuid() : strtolower($creator->getName());
        $targetKey = !empty($target->getXuid()) ? $target->getXuid() : strtolower($target->getName());
        $db = $this->database->getDb();
        $stmt = $db->prepare("INSERT INTO bounty_contracts (creator_xuid, target_xuid, reward) VALUES (:cx, :tx, :rew)");
        if($stmt === false){
            $this->economy->addMoney($creator, $amount); // Remboursement
            $creator->sendMessage("§c❌ Erreur interne lors du dépôt de la prime.");
            return false;
        }

        $stmt->bindValue(":cx", $creatorKey, SQLITE3_TEXT);
        $stmt->bindValue(":tx", $targetKey, SQLITE3_TEXT);
        $stmt->bindValue(":rew", $amount, SQLITE3_INTEGER);
        $stmt->execute();

        $creator->getServer()->broadcastMessage("§c☠ CONTRAT DE PRIME : §e" . $creator->getName() . " §fa mis à prix la tête de §c" . $target->getName() . " §fpour §6" . number_format($amount) . " Or §f!");
        return true;
    }

    public function getActiveBountiesOn(string $targetXuid) : int{
        $db = $this->database->getDb();
        $stmt = $db->prepare("SELECT SUM(reward) as total FROM bounty_contracts WHERE target_xuid = :tx AND is_claimed = 0");
        if($stmt === false) return 0;

        $stmt->bindValue(":tx", $targetXuid, SQLITE3_TEXT);
        $res = $stmt->execute();
        if($res === false) return 0;

        $row = $res->fetchArray(SQLITE3_ASSOC);
        return (int)($row["total"] ?? 0);
    }

    public function onDeath(PlayerDeathEvent $event) : void{
        $victim = $event->getPlayer();
        $cause = $victim->getLastDamageCause();

        if($cause instanceof EntityDamageByEntityEvent){
            $damager = $cause->getDamager();
            // Prise en charge des tirs à distance (arc, arbalète, trident)
            if($damager instanceof Projectile){
                $damager = $damager->getOwningEntity();
            }

            if($damager instanceof Player && strtolower($damager->getName()) !== strtolower($victim->getName())){
                $targetKey = !empty($victim->getXuid()) ? $victim->getXuid() : strtolower($victim->getName());
                $bounty = $this->getActiveBountiesOn($targetKey);

                if($bounty > 0){
                    $db = $this->database->getDb();
                    $stmt = $db->prepare("UPDATE bounty_contracts SET is_claimed = 1 WHERE target_xuid = :tx AND is_claimed = 0");
                    if($stmt !== false){
                        $stmt->bindValue(":tx", $targetKey, SQLITE3_TEXT);
                        $stmt->execute();
                    }

                    $this->economy->addMoney($damager, $bounty);
                    $damager->getServer()->broadcastMessage("§6🏆 PRIME RÉCLAMÉE : §e" . $damager->getName() . " §fa éliminé §c" . $victim->getName() . " §fet empoche §6" . number_format($bounty) . " Or §f!");
                }
            }
        }
    }
}
