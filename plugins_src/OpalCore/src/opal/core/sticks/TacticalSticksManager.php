<?php

declare(strict_types=1);

namespace opal\core\sticks;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\player\Player;

class TacticalSticksManager implements Listener{

    private static ?self $instance = null;
    /** @var array<string, array<string, int>> xuid => stick_type => cooldown_timestamp */
    private array $cooldowns = [];

    public function __construct(){
        self::$instance = $this;
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function onUse(PlayerItemUseEvent $event) : void{
        $player = $event->getPlayer();
        $item = $event->getItem();
        $customName = $item->getCustomName();

        if(str_contains($customName, "Bâton de Guérison")){
            $this->useHealStick($player);
        }elseif(str_contains($customName, "Bâton de Vitesse")){
            $this->useSpeedStick($player);
        }elseif(str_contains($customName, "Bâton de Répulsion")){
            $this->useForceStick($player);
        }
    }

    private function useHealStick(Player $player) : void{
        $now = time();
        $xuid = $player->getXuid();
        if(($this->cooldowns[$xuid]["heal"] ?? 0) > $now){
            $wait = $this->cooldowns[$xuid]["heal"] - $now;
            $player->sendActionBarMessage("§c⏳ Cooldown Bâton de Guérison: {$wait}s");
            return;
        }

        $this->cooldowns[$xuid]["heal"] = $now + 45;
        $player->setHealth(min($player->getMaxHealth(), $player->getHealth() + 8.0)); // 4 cœurs
        $player->sendActionBarMessage("§a💖 +4 Cœurs de Vie restaurés !");
        $this->playSound($player, "random.potion.brew");
    }

    private function useSpeedStick(Player $player) : void{
        $now = time();
        $xuid = $player->getXuid();
        if(($this->cooldowns[$xuid]["speed"] ?? 0) > $now){
            $wait = $this->cooldowns[$xuid]["speed"] - $now;
            $player->sendActionBarMessage("§c⏳ Cooldown Bâton de Vitesse: {$wait}s");
            return;
        }

        $this->cooldowns[$xuid]["speed"] = $now + 30;
        $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 15 * 20, 1, false));
        $player->sendActionBarMessage("§b⚡ Vitesse Céleste II activée (15s) !");
        $this->playSound($player, "beacon.activate");
    }

    private function useForceStick(Player $player) : void{
        $now = time();
        $xuid = $player->getXuid();
        if(($this->cooldowns[$xuid]["force"] ?? 0) > $now){
            $wait = $this->cooldowns[$xuid]["force"] - $now;
            $player->sendActionBarMessage("§c⏳ Cooldown Bâton de Répulsion: {$wait}s");
            return;
        }

        $this->cooldowns[$xuid]["force"] = $now + 30;
        $pos = $player->getPosition();

        foreach($player->getWorld()->getPlayers() as $nearby){
            if($nearby->getName() !== $player->getName() && $nearby->getPosition()->distance($pos) <= 6.0){
                $dir = $nearby->getPosition()->subtractVector($pos)->normalize();
                $nearby->setMotion($dir->multiply(1.5)->add(0, 0.4, 0));
                $nearby->sendActionBarMessage("§c💥 ONDE DE CHOC DE RÉPULSION !");
            }
        }

        $player->sendActionBarMessage("§6💥 Onde de Répulsion libérée !");
        $this->playSound($player, "random.explode");
    }

    private function playSound(Player $player, string $sound) : void{
        $pk = PlaySoundPacket::create($sound, $player->getPosition()->x, $player->getPosition()->y, $player->getPosition()->z, 1.0, 1.0);
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}
