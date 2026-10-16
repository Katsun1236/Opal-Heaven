<?php

declare(strict_types=1);

namespace opal\core\bounty;

use opal\core\database\DatabaseManager;
use opal\core\economy\EconomyManager;
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
        return self::$instance;
    }

    public function placeBounty(Player $creator, Player $target, int $amount) : bool{
        if($amount < 500){
            $creator->sendMessage("§c❌ La prime minimale est de 500 Or !");
            return false;
        }

        $creatorXuid = $creator->getXuid();
        if(!$this->economy->removeMoney($creatorXuid, $amount)){
            $creator->sendMessage("§c❌ Solde insuffisant pour déposer cette prime !");
            return false;
        }

        $targetXuid = $target->getXuid();
        $db = $this->database->getDb();
        $stmt = $db->prepare("INSERT INTO bounty_contracts (creator_xuid, target_xuid, reward) VALUES (:cx, :tx, :rew)");
        $stmt->bindValue(":cx", $creatorXuid, SQLITE3_TEXT);
        $stmt->bindValue(":tx", $targetXuid, SQLITE3_TEXT);
        $stmt->bindValue(":rew", $amount, SQLITE3_INTEGER);
        $stmt->execute();

        $creator->getServer()->broadcastMessage("§c☠ CONTRAT DE PRIME : §e" . $creator->getName() . " §fa mis à prix la tête de §c" . $target->getName() . " §fpour §6{$amount} Or §f!");
        return true;
    }

    public function getActiveBountiesOn(string $targetXuid) : int{
        $db = $this->database->getDb();
        $stmt = $db->prepare("SELECT SUM(reward) as total FROM bounty_contracts WHERE target_xuid = :tx AND is_claimed = 0");
        $stmt->bindValue(":tx", $targetXuid, SQLITE3_TEXT);
        $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        return (int)($res["total"] ?? 0);
    }

    public function onDeath(PlayerDeathEvent $event) : void{
        $victim = $event->getPlayer();
        $cause = $victim->getLastDamageCause();

        if($cause instanceof EntityDamageByEntityEvent){
            $killer = $cause->getDamager();
            if($killer instanceof Player && $killer->getName() !== $victim->getName()){
                $targetXuid = $victim->getXuid();
                $bounty = $this->getActiveBountiesOn($targetXuid);

                if($bounty > 0){
                    $db = $this->database->getDb();
                    $stmt = $db->prepare("UPDATE bounty_contracts SET is_claimed = 1 WHERE target_xuid = :tx AND is_claimed = 0");
                    $stmt->bindValue(":tx", $targetXuid, SQLITE3_TEXT);
                    $stmt->execute();

                    $this->economy->addMoney($killer->getXuid(), $bounty);
                    $killer->getServer()->broadcastMessage("§6🏆 PRIME RÉCLAMÉE : §e" . $killer->getName() . " §fa éliminé §c" . $victim->getName() . " §fet empoche §6{$bounty} Or §f!");
                }
            }
        }
    }
}
