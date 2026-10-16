<?php

declare(strict_types=1);

namespace opal\core\training;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\player\Player;

class DummySession{
    public Player $player;
    public int $state = 0; // 0 = idle, 1 = telegraphing (15 ticks), 2 = parry_window (9 ticks)
    public int $ticksInState = 0;
    public bool $hasParried = false;

    public function __construct(Player $player){
        $this->player = $player;
    }
}

class CombatDummyManager implements Listener{

    private static ?self $instance = null;
    /** @var array<string, DummySession> */
    private array $sessions = [];

    public function __construct(){
        self::$instance = $this;
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function startTraining(Player $player) : void{
        $this->sessions[$player->getName()] = new DummySession($player);
        $player->sendMessage("§6🥋 [Mannequin de Parade] §eSession d'entraînement lancée ! Observez les alertes et baissez-vous (Sneak) au bon moment !");
    }

    public function stopTraining(Player $player) : void{
        unset($this->sessions[$player->getName()]);
        $player->sendMessage("§7🥋 [Mannequin de Parade] Session terminée.");
    }

    public function onSneak(PlayerToggleSneakEvent $event) : void{
        $player = $event->getPlayer();
        $name = $player->getName();
        if(isset($this->sessions[$name]) && $event->isSneaking()){
            $s = $this->sessions[$name];
            if($s->state === 2 && !$s->hasParried){
                $s->hasParried = true;
                $timing = $s->ticksInState;
                $player->sendActionBarMessage("§6⭐ §lPARADE PARFAITE ! §r§aTiming: {$timing} ticks / 9");
                $this->playSound($player, "random.anvil_land", 1.85);
                $this->playSound($player, "beacon.activate", 2.0);
            }elseif($s->state === 1){
                $player->sendActionBarMessage("§c❌ PARADE RATÉE — Trop tôt !");
            }
        }
    }

    public function tick() : void{
        foreach($this->sessions as $xuid => $s){
            $player = $s->player;
            if(!$player->isOnline()){
                unset($this->sessions[$xuid]);
                continue;
            }

            $s->ticksInState++;

            if($s->state === 0){
                // Attente 35 ticks (1.75s) avant la frappe
                if($s->ticksInState >= 35){
                    $s->state = 1;
                    $s->ticksInState = 0;
                    $s->hasParried = false;
                    $player->sendActionBarMessage("§e⚠️ LE MANNEQUIN ARME SA FRAPPE... PRÉPAREZ-VOUS !");
                    $this->playSound($player, "note.bass", 1.0);
                }
            }elseif($s->state === 1){
                // Télégraphie de 15 ticks
                if($s->ticksInState >= 15){
                    $s->state = 2;
                    $s->ticksInState = 0;
                    $player->sendActionBarMessage("§c⚔ IMPACT ! PAREZ AU SNEAK MAINTENANT !");
                }
            }elseif($s->state === 2){
                // Fenêtre de 9 ticks
                if($s->ticksInState >= 9){
                    if(!$s->hasParried){
                        $player->sendActionBarMessage("§c❌ PARADE MANQUÉE — Trop tard !");
                        $this->playSound($player, "item.shield.block", 0.5);
                    }
                    $s->state = 0;
                    $s->ticksInState = 0;
                }
            }
        }
    }

    private function playSound(Player $player, string $sound, float $pitch) : void{
        $pk = PlaySoundPacket::create($sound, $player->getPosition()->x, $player->getPosition()->y, $player->getPosition()->z, 1.0, $pitch);
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}
