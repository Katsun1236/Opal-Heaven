<?php

declare(strict_types=1);

namespace opal\core\combat;

use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class CombatTagManager implements Listener{

    private static ?self $instance = null;
    /** @var array<string, int> player_xuid => expiry_timestamp */
    private array $tagged = [];

    public function __construct(){
        self::$instance = $this;
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function tag(Player $player, int $durationSeconds = 15) : void{
        $xuid = $player->getXuid();
        $this->tagged[$xuid] = time() + $durationSeconds;
        $player->sendActionBarMessage("§c⚔ EN COMBAT ⚔ §7(15s)");
    }

    public function isTagged(Player $player) : bool{
        $xuid = $player->getXuid();
        if(isset($this->tagged[$xuid])){
            if(time() < $this->tagged[$xuid]){
                return true;
            }
            unset($this->tagged[$xuid]);
        }
        return false;
    }

    public function getRemainingSeconds(Player $player) : int{
        $xuid = $player->getXuid();
        if(isset($this->tagged[$xuid])){
            $remaining = $this->tagged[$xuid] - time();
            return max(0, $remaining);
        }
        return 0;
    }

    public function untag(Player $player) : void{
        unset($this->tagged[$player->getXuid()]);
    }

    public function onDamage(EntityDamageByEntityEvent $event) : void{
        $victim = $event->getEntity();
        $damager = $event->getDamager();

        if($victim instanceof Player && $damager instanceof Player){
            $this->tag($victim);
            $this->tag($damager);
        }
    }

    public function onQuit(PlayerQuitEvent $event) : void{
        $player = $event->getPlayer();
        if($this->isTagged($player)){
            // Combat Logger kill
            $player->kill();
            $player->getServer()->broadcastMessage("§c☠ §e" . $player->getName() . " §cs'est déconnecté en plein combat et a été exécuté !");
            $this->untag($player);
        }
    }
}
