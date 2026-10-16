<?php

declare(strict_types=1);

namespace opal\core\teleport;

use opal\core\combat\CombatTagManager;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\player\Player;
use pocketmine\world\Position;

class PendingTeleport{
    public Player $player;
    public Position $target;
    public int $remainingSeconds;
    public Position $startPos;
    public ?string $serverHost = null;
    public ?int $serverPort = null;

    public function __construct(Player $p, Position $t, int $seconds, ?string $sh = null, ?int $sp = null){
        $this->player = $p;
        $this->target = $t;
        $this->remainingSeconds = $seconds;
        $this->startPos = $p->getPosition();
        $this->serverHost = $sh;
        $this->serverPort = $sp;
    }
}

class TeleportManager implements Listener{

    private static ?self $instance = null;
    private CombatTagManager $combatTag;

    /** @var array<string, PendingTeleport> */
    private array $pending = [];

    public function __construct(CombatTagManager $combatTag){
        self::$instance = $this;
        $this->combatTag = $combatTag;
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function startTeleport(Player $player, Position $target, int $delaySeconds = 3) : void{
        if($this->combatTag->isTagged($player)){
            $player->sendMessage("§c❌ Impossible de se téléporter en plein combat (Combat-Tag actif) !");
            return;
        }

        $this->pending[$player->getXuid()] = new PendingTeleport($player, $target, $delaySeconds);
        $player->sendActionBarMessage("§e⏳ Téléportation dans {$delaySeconds}s... Restez immobile !");
    }

    public function startServerTransfer(Player $player, string $host, int $port, int $delaySeconds = 1) : void{
        if($this->combatTag->isTagged($player)){
            $player->sendMessage("§c❌ Impossible de changer de serveur en plein combat !");
            return;
        }

        $this->pending[$player->getXuid()] = new PendingTeleport($player, $player->getPosition(), $delaySeconds, $host, $port);
        $player->sendActionBarMessage("§d🌌 Transfert vers le serveur dans {$delaySeconds}s...");
    }

    public function onMove(PlayerMoveEvent $event) : void{
        $player = $event->getPlayer();
        $xuid = $player->getXuid();
        if(isset($this->pending[$xuid])){
            $task = $this->pending[$xuid];
            if($player->getPosition()->distance($task->startPos) > 0.5){
                unset($this->pending[$xuid]);
                $player->sendActionBarMessage("§c❌ Téléportation annulée (Vous avez bougé) !");
            }
        }
    }

    public function onDamage(EntityDamageEvent $event) : void{
        $entity = $event->getEntity();
        if($entity instanceof Player){
            $xuid = $entity->getXuid();
            if(isset($this->pending[$xuid])){
                unset($this->pending[$xuid]);
                $entity->sendActionBarMessage("§c❌ Téléportation annulée (Dégâts subis) !");
            }
        }
    }

    public function tick() : void{
        foreach($this->pending as $xuid => $task){
            $player = $task->player;
            if(!$player->isOnline()){
                unset($this->pending[$xuid]);
                continue;
            }

            $task->remainingSeconds--;
            if($task->remainingSeconds <= 0){
                unset($this->pending[$xuid]);
                if($task->serverHost !== null && $task->serverPort !== null){
                    $player->sendMessage("§a✨ Connexion au serveur {$task->serverHost}:{$task->serverPort}...");
                    $player->transfer($task->serverHost, $task->serverPort);
                }else{
                    $player->teleport($task->target);
                    $player->sendMessage("§a✨ Téléportation céleste effectuée !");
                }
            }else{
                $player->sendActionBarMessage("§e⏳ Téléportation dans {$task->remainingSeconds}s... Restez immobile !");
            }
        }
    }
}
