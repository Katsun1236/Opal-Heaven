<?php

declare(strict_types=1);

namespace opalheaven\core\combat;

use pocketmine\player\Player;
use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\item\Axe;
use pocketmine\world\particle\CriticalParticle;
use pocketmine\world\sound\AnvilBreakSound;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use opalheaven\core\orders\SpecializationType;

class PostureManager {

    /** @var array<string, float> Current posture per player XUID (0.0 to 100.0) */
    private array $posture = [];

    /** @var array<string, int> Last hit timestamp per player */
    private array $lastHitTime = [];

    /** @var array<string, int> Staggered expiration timestamp */
    private array $staggeredUntil = [];

    /** @var array<string, float> Sneak start timestamp for parry */
    private array $sneakStartTime = [];

    public function handleSneakToggle(Player $player, bool $sneaking) : void {
        if ($sneaking) {
            $this->sneakStartTime[$player->getXuid()] = microtime(true);
        } else {
            unset($this->sneakStartTime[$player->getXuid()]);
        }
    }

    /**
     * Calcule le multiplicateur d'impact d'arme (Livre Blanc Section 2.A)
     */
    public function getWeaponMultiplier(Item $item) : float {
        $name = strtolower($item->getName());
        $customName = strtolower($item->getCustomName());

        if (str_contains($name, "dagger") || str_contains($customName, "dague") || str_contains($customName, "rapière")) {
            return 0.75;
        }
        if ($item instanceof Axe || str_contains($name, "axe") || str_contains($customName, "hache")) {
            return 1.45;
        }
        if (str_contains($name, "hammer") || str_contains($name, "mace") || str_contains($customName, "marteau") || str_contains($customName, "masse")) {
            return 1.80;
        }
        if ($item instanceof Sword || str_contains($name, "sword") || str_contains($customName, "épée")) {
            return 1.00;
        }

        return 1.00;
    }

    /**
     * Calcule la résistance d'armure plafonnée à 0.35 (Livre Blanc Section 2.A)
     */
    public function getArmorResistance(Player $player) : float {
        $armorPoints = 0;
        foreach ($player->getArmorInventory()->getContents() as $armor) {
            $armorPoints += $armor->getDefensePoints();
        }
        $rawReduction = ($armorPoints / 20.0) * 0.35;
        return min(0.35, max(0.0, $rawReduction));
    }

    /**
     * Formule de Gain de Posture par Impact (Livre Blanc Section 2.A)
     * Delta Posture = (Degats Bruts * M_arme) * (1 - R_armure)
     */
    public function calculatePostureGain(float $damage, Item $weapon, Player $victim) : float {
        $mWeapon = $this->getWeaponMultiplier($weapon);
        $rArmor = $this->getArmorResistance($victim);
        $delta = ($damage * $mWeapon) * (1.0 - $rArmor);
        return max(1.0, $delta);
    }

    /**
     * Tentative de parade parfaite (Fenêtre temporelle : 0.050s <= T_impact - T_sneak <= 0.450s)
     */
    public function attemptParry(Player $victim, Player $attacker, ?SpecializationType $victimSpec = null) : bool {
        $xuid = $victim->getXuid();
        if (!$victim->isSneaking() || !isset($this->sneakStartTime[$xuid])) {
            return false;
        }

        $elapsed = microtime(true) - $this->sneakStartTime[$xuid];
        if ($elapsed >= 0.050 && $elapsed <= 0.450) {
            $world = $victim->getWorld();
            $pos = $victim->getPosition();

            // Effet visuel et sonore de parade
            $world->addSound($pos, new AnvilBreakSound());
            for ($i = 0; $i < 15; $i++) {
                $world->addParticle($pos->add(0, 1.2, 0), new CriticalParticle());
            }

            // Posture renvoyée à l'assaillant : 28.0% standard, 39.2% pour le Bretteur (+40%)
            $reflectedPosture = 28.0;
            if ($victimSpec === SpecializationType::BRETTEUR) {
                $reflectedPosture = 39.2;
                // Restauration de 10% de faim/stamina
                $victim->getHungerManager()->addFood(2.0);
            }
            $this->addPosture($attacker, $reflectedPosture, $victim);

            // Répulsion vectorielle forcée : Normalize(Pos_Attaquant - Pos_Defenseur) * 0.70 + (0, 0.25, 0)
            $diff = $attacker->getPosition()->subtractVector($victim->getPosition())->normalize();
            $attacker->setMotion($diff->multiply(0.70)->add(0, 0.25, 0));

            // Préservation de la durabilité de l'arme du défenseur (déjà assuré en annulant les dégâts)
            $victim->sendActionBarMessage("§a§l⚔ PARADE PARFAITE ! §r§f+" . (int)$reflectedPosture . "% posture renvoyée !");
            $attacker->sendActionBarMessage("§c§l⚔ REPOUSSÉ ! §r§7Votre assaut a été paré au millimètre !");

            return true;
        }

        return false;
    }

    public function getPosture(Player $player) : float {
        return $this->posture[$player->getXuid()] ?? 0.0;
    }

    public function isStaggered(Player $player) : bool {
        $xuid = $player->getXuid();
        return isset($this->staggeredUntil[$xuid]) && $this->staggeredUntil[$xuid] > time();
    }

    public function addPosture(Player $player, float $amount, ?Player $attacker = null) : bool {
        $xuid = $player->getXuid();
        $current = $this->getPosture($player);
        $new = min(100.0, $current + $amount);
        $this->posture[$xuid] = $new;
        $this->lastHitTime[$xuid] = time();

        $this->sendPostureActionBar($player);

        // Déclenchement de la Rupture de Garde à 100%
        if ($new >= 100.0) {
            $this->triggerPostureBreak($player, $attacker);
            return true;
        }

        return false;
    }

    /**
     * État de Stagger (Posture Break) : Strictement 40 ticks (2.0s)
     * Slowness VI, Mining Fatigue V, AnvilBreakSound (12 blocs), 15 CriticalParticles, 30% armor bypass
     */
    public function triggerPostureBreak(Player $victim, ?Player $attacker = null) : void {
        $xuid = $victim->getXuid();
        $this->posture[$xuid] = 0.0;
        $this->staggeredUntil[$xuid] = time() + 2;

        $world = $victim->getWorld();
        $pos = $victim->getPosition();
        $world->addSound($pos, new AnvilBreakSound());
        for ($i = 0; $i < 15; $i++) {
            $world->addParticle($pos->add(0, 1.4, 0), new CriticalParticle());
        }

        // Effets appliqués : Slowness VI (ampl 5), Mining Fatigue V (ampl 4)
        $victim->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 40, 5, false));
        $victim->getEffects()->add(new EffectInstance(VanillaEffects::MINING_FATIGUE(), 40, 4, false));

        $victim->sendTitle("§c§lRUPTURE DE GARDE !", "§7Étourdi 2s ! Vulnérabilité aux dégâts !", 5, 20, 5);

        if ($attacker !== null) {
            $attacker->sendTitle("§a§lGARDE ADVERSE BRISEE !", "§eCoup perforant (ignore 30% d'armure) !", 5, 20, 5);
        }
    }

    public function tickDecay(Player $player) : void {
        $xuid = $player->getXuid();
        $lastHit = $this->lastHitTime[$xuid] ?? 0;

        if (time() - $lastHit >= 3) {
            $current = $this->getPosture($player);
            if ($current > 0.0) {
                $new = max(0.0, $current - 5.0);
                $this->posture[$xuid] = $new;
                if ($new > 0.0) {
                    $this->sendPostureActionBar($player);
                }
            }
        }
    }

    public function sendPostureActionBar(Player $player) : void {
        $posture = $this->getPosture($player);
        if ($posture <= 0.0) {
            return;
        }

        $barLength = 10;
        $filled = (int) round(($posture / 100.0) * $barLength);
        $empty = $barLength - $filled;

        $color = "§a";
        if ($posture > 50.0) $color = "§e";
        if ($posture > 75.0) $color = "§c";

        $bar = $color . str_repeat("█", $filled) . "§7" . str_repeat("░", $empty);
        $player->sendActionBarMessage("§7Posture: [{$bar}§7] {$color}" . (int)$posture . "%");
    }

    public function reset(Player $player) : void {
        $xuid = $player->getXuid();
        unset($this->posture[$xuid], $this->lastHitTime[$xuid], $this->staggeredUntil[$xuid], $this->sneakStartTime[$xuid]);
    }
}
