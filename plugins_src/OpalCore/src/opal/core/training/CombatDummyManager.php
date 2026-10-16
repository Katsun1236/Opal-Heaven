<?php

declare(strict_types=1);

namespace opal\core\training;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\player\Player;

class DummySession{
    public Player $player;
    public int $state = 0; // 0 = idle, 1 = telegraphing (15 ticks), 2 = parry_window (9 ticks)
    public int $ticksInState = 0;
    public bool $hasParried = false;
    public int $successfulParries = 0;
    public int $totalAttacks = 0;

    public function __construct(Player $player){
        $this->player = $player;
    }
}

class CombatDummyManager implements Listener{

    private static ?self $instance = null;
    /** @var array<string, DummySession> player_name => DummySession */
    private array $sessions = [];

    public function __construct(){
        self::$instance = $this;
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function isTraining(Player $player) : bool{
        return isset($this->sessions[strtolower($player->getName())]);
    }

    public function toggleTraining(Player $player) : void{
        if($this->isTraining($player)){
            $this->stopTraining($player);
        }else{
            $this->startTraining($player);
        }
    }

    public function startTraining(Player $player) : void{
        $key = strtolower($player->getName());
        $this->sessions[$key] = new DummySession($player);
        $player->sendMessage("§6🥋 [Mannequin de Parade] §eSession d'entraînement lancée !");
        $player->sendMessage("§7Observez l'alerte d'impact et accroupissez-vous (Sneak) dans la fenêtre de §e9 ticks §7(0.45s) !");
        $player->sendTitle("§6🥋 ENTRAÎNEMENT PARADE", "§eAccroupissez-vous au timing de l'impact !", 10, 40, 10);
        $this->playSound($player, "random.orb", 1.2);
    }

    public function stopTraining(Player $player) : void{
        $key = strtolower($player->getName());
        if(isset($this->sessions[$key])){
            $s = $this->sessions[$key];
            $player->sendMessage("§7🥋 [Mannequin de Parade] Session terminée. Score: §a{$s->successfulParries} §7/ §e{$s->totalAttacks} §7parades réussies.");
            unset($this->sessions[$key]);
        }
    }

    public function onSneak(PlayerToggleSneakEvent $event) : void{
        $player = $event->getPlayer();
        $key = strtolower($player->getName());

        if(isset($this->sessions[$key]) && $event->isSneaking()){
            $s = $this->sessions[$key];
            if($s->state === 2 && !$s->hasParried){
                $s->hasParried = true;
                $s->successfulParries++;
                $timing = $s->ticksInState;
                $player->sendActionBarMessage("§6⭐ §lPARADE PARFAITE ! §r§aTiming: {$timing} ticks / 9");
                $this->playSound($player, "random.anvil_land", 1.85);
                $this->playSound($player, "beacon.activate", 2.0);

                // Étincelles visuelles
                $pk = SpawnParticleEffectPacket::create(
                    dimensionId: 0,
                    actorUniqueId: -1,
                    position: $player->getPosition()->add(0, 1.2, 0),
                    particleName: "minecraft:sparkler_emitter",
                    molangVariablesJson: null
                );
                $player->getNetworkSession()->sendDataPacket($pk);
            }elseif($s->state === 1){
                $player->sendActionBarMessage("§c❌ PARADE RATÉE — Trop tôt !");
                $this->playSound($player, "note.bass", 0.6);
            }
        }
    }

    public function tick() : void{
        if(empty($this->sessions)){
            return;
        }

        foreach($this->sessions as $key => $s){
            $player = $s->player;
            if(!$player->isOnline()){
                unset($this->sessions[$key]);
                continue;
            }

            $s->ticksInState++;

            if($s->state === 0){
                // Attente 35 ticks (1.75s) avant la frappe
                if($s->ticksInState >= 35){
                    $s->state = 1;
                    $s->ticksInState = 0;
                    $s->hasParried = false;
                    $s->totalAttacks++;
                    $player->sendActionBarMessage("§e⚠️ LE MANNEQUIN ARME SA FRAPPE... PRÉPAREZ-VOUS !");
                    $this->playSound($player, "note.bass", 1.0);
                }
            }elseif($s->state === 1){
                // Télégraphie de 15 ticks (0.75s)
                if($s->ticksInState >= 15){
                    $s->state = 2;
                    $s->ticksInState = 0;
                    $player->sendActionBarMessage("§c⚔ IMPACT ! PAREZ AU SNEAK MAINTENANT !");
                }
            }elseif($s->state === 2){
                // Fenêtre de parade parfaite de 9 ticks (0.45s)
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

    public function onQuit(PlayerQuitEvent $event) : void{
        $key = strtolower($event->getPlayer()->getName());
        unset($this->sessions[$key]);
    }

    private function playSound(Player $player, string $sound, float $pitch = 1.0) : void{
        $pk = new PlaySoundPacket();
        $pk->soundName = $sound;
        $pk->x = (float)$player->getPosition()->x;
        $pk->y = (float)$player->getPosition()->y;
        $pk->z = (float)$player->getPosition()->z;
        $pk->volume = 1.0;
        $pk->pitch = $pitch;
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}
