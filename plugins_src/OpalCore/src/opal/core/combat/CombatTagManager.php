<?php

declare(strict_types=1);

namespace opal\core\combat;

use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class CombatTagManager implements Listener{

    private static ?self $instance = null;
    /** @var array<string, int> player_key => expiry_timestamp */
    private array $tagged = [];

    public function __construct(){
        self::$instance = $this;
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    private function getPlayerKey(Player $player) : string{
        $xuid = $player->getXuid();
        return !empty($xuid) ? $xuid : strtolower($player->getName());
    }

    public function tag(Player $player, int $durationSeconds = 15) : void{
        $key = $this->getPlayerKey($player);
        $this->tagged[$key] = time() + $durationSeconds;
        $player->sendActionBarMessage("§c⚔ EN COMBAT ⚔ §7({$durationSeconds}s)");
    }

    public function isTagged(Player $player) : bool{
        $key = $this->getPlayerKey($player);
        if(isset($this->tagged[$key])){
            if(time() < $this->tagged[$key]){
                return true;
            }
            unset($this->tagged[$key]);
        }
        return false;
    }

    public function getRemainingSeconds(Player $player) : int{
        $key = $this->getPlayerKey($player);
        if(isset($this->tagged[$key])){
            $remaining = $this->tagged[$key] - time();
            return max(0, $remaining);
        }
        return 0;
    }

    public function untag(Player $player) : void{
        unset($this->tagged[$this->getPlayerKey($player)]);
    }

    public function onDamage(EntityDamageByEntityEvent $event) : void{
        if($event->isCancelled()){
            return;
        }

        $victim = $event->getEntity();
        $damager = $event->getDamager();

        if($victim instanceof Player && $damager instanceof Player && $victim !== $damager){
            $this->tag($victim);
            $this->tag($damager);
        }
    }

    public function onQuit(PlayerQuitEvent $event) : void{
        $player = $event->getPlayer();
        if($this->isTagged($player)){
            if($player->isAlive()){
                $player->kill();
            }
            $player->getServer()->broadcastMessage("§c☠ §e" . $player->getName() . " §cs'est déconnecté en plein combat et a été exécuté !");
            $this->untag($player);
        }
    }
}
