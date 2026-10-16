<?php

declare(strict_types=1);

namespace opal\core\sticks;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Living;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\player\Player;

class TacticalSticksManager implements Listener{

    private static ?self $instance = null;
    /** @var array<string, array<string, int>> player_key => stick_type => cooldown_timestamp */
    private array $cooldowns = [];

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

    public function onUse(PlayerItemUseEvent $event) : void{
        $player = $event->getPlayer();
        $item = $event->getItem();
        $tag = $item->getNamedTag();
        $opalId = $tag->getString("opal_id", "");
        $customName = $item->getCustomName();

        if($opalId === "opal:stick_heal" || str_contains($customName, "Bâton de Guérison")){
            $event->cancel();
            $this->useHealStick($player, $item);
        }elseif($opalId === "opal:stick_speed" || str_contains($customName, "Bâton de Vitesse")){
            $event->cancel();
            $this->useSpeedStick($player, $item);
        }elseif($opalId === "opal:stick_force" || str_contains($customName, "Bâton de Répulsion")){
            $event->cancel();
            $this->useForceStick($player, $item);
        }elseif($opalId === "opal:stick_breach" || str_contains($customName, "Bâton de Brèche")){
            $event->cancel();
            $this->useBreachStick($player, $item);
        }
    }

    private function useHealStick(Player $player, Item $item) : void{
        $now = time();
        $key = $this->getPlayerKey($player);
        if(($this->cooldowns[$key]["heal"] ?? 0) > $now){
            $wait = $this->cooldowns[$key]["heal"] - $now;
            $player->sendActionBarMessage("§c⏳ Cooldown Bâton de Guérison: {$wait}s");
            return;
        }

        $this->cooldowns[$key]["heal"] = $now + 45;
        $player->setHealth(min($player->getMaxHealth(), $player->getHealth() + 8.0)); // 4 cœurs
        $player->sendActionBarMessage("§a💖 +4 Cœurs de Vie restaurés !");
        $this->playSound($player, "random.potion.brew", 1.2);

        $partPk = SpawnParticleEffectPacket::create(0, -1, $player->getPosition()->add(0, 1.0, 0), "minecraft:sparkler_emitter", null);
        $player->getNetworkSession()->sendDataPacket($partPk);

        $this->consumeStickCharge($player, $item, "heal", 3, 3, "§a💖 Bâton de Guérison");
    }

    private function useSpeedStick(Player $player, Item $item) : void{
        $now = time();
        $key = $this->getPlayerKey($player);
        if(($this->cooldowns[$key]["speed"] ?? 0) > $now){
            $wait = $this->cooldowns[$key]["speed"] - $now;
            $player->sendActionBarMessage("§c⏳ Cooldown Bâton de Vitesse: {$wait}s");
            return;
        }

        $this->cooldowns[$key]["speed"] = $now + 30;
        $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 15 * 20, 1, false));
        $player->sendActionBarMessage("§b⚡ Vitesse Céleste II activée (15s) !");
        $this->playSound($player, "beacon.activate", 1.5);

        $this->consumeStickCharge($player, $item, "speed", 5, 5, "§b⚡ Bâton de Vitesse");
    }

    private function useForceStick(Player $player, Item $item) : void{
        $now = time();
        $key = $this->getPlayerKey($player);
        if(($this->cooldowns[$key]["force"] ?? 0) > $now){
            $wait = $this->cooldowns[$key]["force"] - $now;
            $player->sendActionBarMessage("§c⏳ Cooldown Bâton de Répulsion: {$wait}s");
            return;
        }

        $this->cooldowns[$key]["force"] = $now + 30;
        $pos = $player->getPosition();

        // Repousser les joueurs et entités vivantes alentour dans un rayon de 6 blocs
        foreach($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy(6.0, 3.0, 6.0)) as $entity){
            if($entity instanceof Living && $entity !== $player){
                $diff = $entity->getPosition()->subtractVector($pos);
                $dir = $diff->lengthSquared() > 0.0001 ? $diff->normalize() : $player->getDirectionVector();
                $entity->setMotion($dir->multiply(1.5)->add(0, 0.4, 0));
                if($entity instanceof Player){
                    $entity->sendActionBarMessage("§c💥 ONDE DE CHOC DE RÉPULSION !");
                }
            }
        }

        $player->sendActionBarMessage("§6💥 Onde de Répulsion libérée !");
        $this->playSound($player, "random.explode", 1.2);

        $partPk = SpawnParticleEffectPacket::create(0, -1, $pos->add(0, 0.5, 0), "minecraft:sparkler_emitter", null);
        $player->getNetworkSession()->sendDataPacket($partPk);

        $this->consumeStickCharge($player, $item, "force", 3, 3, "§6💥 Bâton de Répulsion");
    }

    private function useBreachStick(Player $player, Item $item) : void{
        $hour = (int)date("G");
        if($hour < 18 || $hour >= 23){
            $player->sendActionBarMessage("§c⏳ Bâton de Brèche : Utilisable uniquement en heure de siège (18h-23h) !");
            return;
        }

        $now = time();
        $key = $this->getPlayerKey($player);
        if(($this->cooldowns[$key]["breach"] ?? 0) > $now){
            $wait = $this->cooldowns[$key]["breach"] - $now;
            $player->sendActionBarMessage("§c⏳ Cooldown Bâton de Brèche: {$wait}s");
            return;
        }

        $this->cooldowns[$key]["breach"] = $now + 60;
        $pos = $player->getPosition();

        $player->sendActionBarMessage("§c💣 IMPULSION DE BRÈCHE DE SIÈGE DÉPLOYÉE !");
        $this->playSound($player, "random.anvil_land", 1.2);
        $this->playSound($player, "random.explode", 0.8);

        $partPk = SpawnParticleEffectPacket::create(0, -1, $pos->add(0, 1.0, 0), "minecraft:sparkler_emitter", null);
        $player->getNetworkSession()->sendDataPacket($partPk);

        $this->consumeStickCharge($player, $item, "breach", 2, 2, "§c💣 Bâton de Brèche (Siège)");
    }

    private function consumeStickCharge(Player $player, Item $item, string $type, int $defaultCharges, int $maxCharges, string $label) : void{
        if($player->isCreative()){
            return;
        }

        $tag = $item->getNamedTag();
        $charges = $tag->getInt("opal_charges", $defaultCharges);
        $charges--;

        if($charges <= 0){
            $item->pop();
            $player->getInventory()->setItemInHand($item);
            $player->sendMessage("§c💥 Votre {$label} §cs'est brisé après avoir épuisé toutes ses charges !");
            $this->playSound($player, "random.break", 1.0);
        }else{
            $item->getNamedTag()->setInt("opal_charges", $charges);
            $lore = $item->getLore();
            if(isset($lore[0])){
                $lore[0] = "§7Charges : §e{$charges}/{$maxCharges}";
                $item->setLore($lore);
            }
            $player->getInventory()->setItemInHand($item);
            $player->sendTip("§e⚡ {$label} §7: §e{$charges}/{$maxCharges} §7charges restantes");
        }
    }

    public function onQuit(PlayerQuitEvent $event) : void{
        $key = $this->getPlayerKey($event->getPlayer());
        unset($this->cooldowns[$key]);
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
