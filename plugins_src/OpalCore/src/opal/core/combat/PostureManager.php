<?php

declare(strict_types=1);

namespace opal\core\combat;

use opal\core\database\DatabaseManager;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\network\mcpe\protocol\CameraShakePacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\player\Player;
use pocketmine\world\particle\CriticalParticle;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;

class PostureManager implements Listener{

    private static ?self $instance = null;
    private DatabaseManager $database;

    /** @var array<string, float> key => 0..100 */
    private array $posture = [];

    /** @var array<string, int> key => server tick of sneak start */
    private array $sneakStartTick = [];

    /** @var array<string, int> key => stagger end tick */
    private array $staggerEndTick = [];

    public function __construct(DatabaseManager $database){
        self::$instance = $this;
        $this->database = $database;
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    private function getPlayerKey(Player $player) : string{
        $xuid = $player->getXuid();
        return !empty($xuid) ? $xuid : strtolower($player->getName());
    }

    public function getPosture(Player $player) : float{
        return $this->posture[$this->getPlayerKey($player)] ?? 0.0;
    }

    public function setPosture(Player $player, float $amount) : void{
        $amount = max(0.0, min(100.0, $amount));
        $key = $this->getPlayerKey($player);
        $this->posture[$key] = $amount;
        $this->updateActionBar($player);

        if($amount >= 100.0){
            $this->triggerStagger($player);
        }
    }

    public function isStaggered(Player $player) : bool{
        $key = $this->getPlayerKey($player);
        if(isset($this->staggerEndTick[$key])){
            $currentTick = $player->getServer()->getTick();
            if($currentTick < $this->staggerEndTick[$key]){
                return true;
            }
            unset($this->staggerEndTick[$key]);
        }
        return false;
    }

    public function triggerStagger(Player $player) : void{
        $currentTick = $player->getServer()->getTick();
        $key = $this->getPlayerKey($player);
        $this->staggerEndTick[$key] = $currentTick + 40; // 40 ticks = 2s
        $this->posture[$key] = 100.0;

        // Effets visuels & sonores de rupture
        $player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 40, 2, false));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::WEAKNESS(), 40, 2, false));

        // Camera Shake Packet (constants defined on CameraShakePacket)
        $shakePk = CameraShakePacket::create(0.3, 1.0, CameraShakePacket::TYPE_POSITIONAL, CameraShakePacket::ACTION_ADD);
        $player->getNetworkSession()->sendDataPacket($shakePk);

        // Sons de rupture
        $this->sendCustomSound($player, "block.anvil.destroy", 1.1);
        $this->sendCustomSound($player, "entity.iron_golem.death", 0.75);

        $player->sendTitle("§c§lRUPTURE DE POSTURE", "§eTu es étourdi pendant 2 secondes !", 5, 30, 5);
    }

    public function onSneak(PlayerToggleSneakEvent $event) : void{
        $player = $event->getPlayer();
        $key = $this->getPlayerKey($player);
        if($event->isSneaking()){
            $this->sneakStartTick[$key] = $player->getServer()->getTick();
        }else{
            unset($this->sneakStartTick[$key]);
        }
    }

    public function onDamage(EntityDamageByEntityEvent $event) : void{
        if($event->isCancelled()){
            return;
        }

        $victim = $event->getEntity();
        $damager = $event->getDamager();

        if(!$victim instanceof Player || !$damager instanceof Player){
            return;
        }

        if($victim === $damager){
            return;
        }

        $serverTick = $victim->getServer()->getTick();
        $victimKey = $this->getPlayerKey($victim);
        $damagerKey = $this->getPlayerKey($damager);

        // 1. Vérifier si la victime est en Stagger -> Coup critique d'exécution !
        if($this->isStaggered($victim)){
            $event->setBaseDamage($event->getBaseDamage() * 2.0);
            $victim->getWorld()->addParticle($victim->getPosition()->add(0, 1, 0), new CriticalParticle());
            $this->sendCustomSound($damager, "random.explode", 1.2);
            $damager->sendMessage("§6⚡ §lEXÉCUTION CRITIQUE ! §r§eDégâts doublés !");
            return;
        }

        // 2. Vérifier si l'attaquant est en Stagger -> Attaque impossible
        if($this->isStaggered($damager)){
            $event->cancel();
            $damager->sendActionBarMessage("§c❌ Tu es étourdi (Stagger), impossible d'attaquer !");
            return;
        }

        // 3. Vérifier Parade au Sneak
        if($victim->isSneaking() && isset($this->sneakStartTick[$victimKey])){
            $sneakTicks = $serverTick - $this->sneakStartTick[$victimKey];

            // PARADE PARFAITE (Timing fenêtre de 1 à 9 ticks = 0.05s à 0.45s)
            if($sneakTicks >= 1 && $sneakTicks <= 9){
                $event->cancel();

                // Sons gratifiants
                $this->sendCustomSound($victim, "random.anvil_land", 1.85);
                $this->sendCustomSound($victim, "beacon.activate", 2.0);
                $this->sendCustomSound($damager, "random.anvil_land", 1.85);

                // Particules étincelles
                $pk = SpawnParticleEffectPacket::create(
                    dimensionId: 0,
                    actorUniqueId: -1,
                    position: $victim->getPosition()->add(0, 1.2, 0),
                    particleName: "minecraft:sparkler_emitter",
                    molangVariablesJson: null
                );
                $victim->getNetworkSession()->sendDataPacket($pk);
                $damager->getNetworkSession()->sendDataPacket($pk);

                // Dégâts de posture infligés à l'attaquant
                $this->setPosture($damager, $this->getPosture($damager) + 20.0);
                $this->setPosture($victim, max(0.0, $this->getPosture($victim) - 10.0));

                // Recul de l'attaquant
                $diff = $damager->getPosition()->subtractVector($victim->getPosition());
                $direction = $diff->lengthSquared() > 0.0001 ? $diff->normalize() : $victim->getDirectionVector();
                $damager->setMotion($direction->multiply(0.6)->add(0, 0.25, 0));

                $victim->sendActionBarMessage("§6⭐ §lPARADE PARFAITE ! §r§aTiming: {$sneakTicks} ticks/9");
                $damager->sendActionBarMessage("§c⚡ PARADE SUBIE ! §7Votre posture a augmenté de +20%");

                // Incrémenter stats de parade réussie
                $escapedXuid = \SQLite3::escapeString($victimKey);
                $this->database->getDb()->exec("
                    INSERT INTO combat_stats (xuid, parries_success) 
                    VALUES ('$escapedXuid', 1) 
                    ON CONFLICT(xuid) DO UPDATE SET parries_success = parries_success + 1;
                ");
                return;
            }else{
                // Garde normale (après 9 ticks) : 50% de réduction de dégâts, mais prend 25% de posture
                $event->setBaseDamage($event->getBaseDamage() * 0.5);
                $this->setPosture($victim, $this->getPosture($victim) + 25.0);
                $victim->sendActionBarMessage("§e🛡 Garde ordinaire : -50% dégâts, +25% posture");
                $this->sendCustomSound($victim, "item.shield.block", 1.0);
                return;
            }
        }

        // 4. Coup ordinaire reçu -> Augmentation passive de posture (12% par coup)
        $this->setPosture($victim, $this->getPosture($victim) + 12.0);
    }

    private function updateActionBar(Player $player) : void{
        $val = (int)$this->getPosture($player);
        $bar = $this->renderBar($val);
        $player->sendActionBarMessage("§fPosture: [{$bar}§f] §e{$val}%");
    }

    private function renderBar(int $val) : string{
        $totalBars = 10;
        $filled = (int)round(($val / 100) * $totalBars);
        $color = $val > 80 ? "§c" : ($val > 50 ? "§6" : "§a");
        $str = $color;
        for($i = 0; $i < $filled; $i++){
            $str .= "█";
        }
        $str .= "§7";
        for($i = $filled; $i < $totalBars; $i++){
            $str .= "░";
        }
        return $str;
    }

    public function decayTick() : void{
        // Décroissance naturelle de posture (1% par seconde quand non attaqué)
        foreach($this->posture as $key => $val){
            if($val > 0.0){
                $this->posture[$key] = max(0.0, $val - 1.0);
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event) : void{
        $key = $this->getPlayerKey($event->getPlayer());
        unset($this->posture[$key], $this->sneakStartTick[$key], $this->staggerEndTick[$key]);
    }

    private function sendCustomSound(Player $player, string $soundName, float $pitch) : void{
        $pk = new PlaySoundPacket();
        $pk->soundName = $soundName;
        $pk->x = (float)$player->getPosition()->x;
        $pk->y = (float)$player->getPosition()->y;
        $pk->z = (float)$player->getPosition()->z;
        $pk->volume = 1.0;
        $pk->pitch = $pitch;
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}
