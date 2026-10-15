<?php

declare(strict_types=1);

namespace opalheaven\core\mining;

use pocketmine\player\Player;
use pocketmine\world\particle\EnchantParticle;
use pocketmine\world\particle\HugeExplodeParticle;
use pocketmine\world\sound\NoteSound;
use pocketmine\world\sound\ExplodeSound;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;

class ResonanceManager {

    /** @var array<string, int> Strike counts for anti-bot subterranean detection */
    private array $strikeCount = [];

    /** @var array<string, float> Last strike timestamp */
    private array $lastStrikeTime = [];

    /** @var array<string, int> Resonance charge percentage (0 - 100) */
    private array $resonanceCharge = [];

    public function getCharge(Player $player) : int {
        return $this->resonanceCharge[$player->getXuid()] ?? 0;
    }

    public function handleBlockBreak(Player $player, \pocketmine\block\Block $block, array &$drops) : void {
        $xuid = $player->getXuid();
        $now = microtime(true);
        $last = $this->lastStrikeTime[$xuid] ?? 0.0;
        $interval = $now - $last;
        $this->lastStrikeTime[$xuid] = $now;

        $isHarmonic = ($interval >= 0.32 && $interval <= 0.65);

        // Détection minerais
        $isOre = str_contains(strtolower($block->getName()), "ore") || $block->hasSameTypeId(VanillaBlocks::ANCIENT_DEBRIS());

        if ($isOre) {
            $currentCharge = $this->getCharge($player);

            if ($currentCharge >= 100) {
                // SURCHARGE SÉISMIQUE (SEISMIC OVERLOAD)
                $this->triggerSeismicOverload($player, $block, $drops);
                $this->resonanceCharge[$xuid] = 0;
                return;
            }

            // Progression de la charge
            $gain = $isHarmonic ? 25 : 15;
            $newCharge = min(100, $currentCharge + $gain);
            $this->resonanceCharge[$xuid] = $newCharge;

            $world = $player->getWorld();
            $pos = $block->getPosition();

            if ($isHarmonic) {
                $world->addSound($pos, new NoteSound(NoteSound::INSTRUMENT_BELL, 12));
                for ($i = 0; $i < 6; $i++) {
                    $world->addParticle($pos->add(0.5, 0.5, 0.5), new EnchantParticle());
                }
                if (!empty($drops)) {
                    $extra = clone $drops[0];
                    $extra->setCount(1);
                    $drops[] = $extra;
                }
            }

            $this->sendResonanceActionBar($player);
        }

        // Surveillance anti-bot
        $this->strikeCount[$xuid] = ($this->strikeCount[$xuid] ?? 0) + 1;
        if ($this->strikeCount[$xuid] >= 50) {
            $player->sendTitle("§c§lGRONDEMENT SOUTERRAIN !", "§eFaites une pause, les créatures fouisseuses rôdent...", 10, 30, 10);
            $this->strikeCount[$xuid] = 0;
        }
    }

    private function triggerSeismicOverload(Player $player, \pocketmine\block\Block $centerBlock, array &$drops) : void {
        $world = $player->getWorld();
        $centerPos = $centerBlock->getPosition();

        // Effets visuels et sonores massifs
        $world->addSound($centerPos, new ExplodeSound());
        $world->addParticle($centerPos->add(0.5, 0.5, 0.5), new HugeExplodeParticle());

        // Drop d'une Gemme Primordiale
        $gemType = mt_rand(1, 3);
        $primordialGem = match($gemType) {
            1 => VanillaItems::DIAMOND()->setCustomName("§l§dOpale Pure d'Aether")->setLore(["§7Gemme sacrée des profondeurs d'Opal Heaven.", "§eUtilisée dans les forges d'Ascension."]),
            2 => VanillaItems::AMETHYST_SHARD()->setCustomName("§l§bFragment Céleste d'Opal")->setLore(["§7Éclat astral vibrant d'énergie tellurique.", "§ePermet d'enchanter les reliques d'ordre."]),
            default => VanillaItems::ECHO_SHARD()->setCustomName("§l§5Cristal du Néant Éthéré")->setLore(["§7Cristal résonnant absorbant la lumière.", "§eComposant d'alchimie divine."])
        };

        $drops[] = $primordialGem;

        // Brise les blocs rocheux adjacents (sphère 3x3x3)
        for ($dx = -1; $dx <= 1; ++$dx) {
            for ($dy = -1; $dy <= 1; ++$dy) {
                for ($dz = -1; $dz <= 1; ++$dz) {
                    if ($dx === 0 && $dy === 0 && $dz === 0) continue;
                    $targetPos = $centerPos->add($dx, $dy, $dz);
                    $targetBlock = $world->getBlock($targetPos);
                    if ($targetBlock->hasSameTypeId(VanillaBlocks::STONE()) || $targetBlock->hasSameTypeId(VanillaBlocks::DEEPSLATE()) || $targetBlock->hasSameTypeId(VanillaBlocks::NETHERRACK())) {
                        $world->setBlock($targetPos, VanillaBlocks::AIR());
                    }
                }
            }
        }

        $player->sendTitle("§d§lSURCHARGE SÉISMIQUE !", "§eGemme Primordiale Déterrée !", 10, 40, 10);
        $player->sendActionBarMessage("§d§l[SÉISME] §r§fÉruption tellurique ! Gemme primordiale découverte !");
    }

    public function sendResonanceActionBar(Player $player) : void {
        $charge = $this->getCharge($player);
        $bars = 10;
        $filled = (int) round(($charge / 100) * $bars);
        $empty = $bars - $filled;

        $color = $charge >= 100 ? "§d§l" : ($charge >= 50 ? "§b" : "§7");
        $bar = $color . str_repeat("█", $filled) . "§8" . str_repeat("░", $empty);

        $status = $charge >= 100 ? "§d§l[SURCHARGE PRÊTE !]" : "{$charge}%";
        $player->sendActionBarMessage("§7Résonance: [{$bar}§7] {$status}");
    }

    public function tickDecay(Player $player) : void {
        $xuid = $player->getXuid();
        $last = $this->lastStrikeTime[$xuid] ?? 0.0;
        if (microtime(true) - $last > 8.0) {
            $this->strikeCount[$xuid] = 0;
            if (isset($this->resonanceCharge[$xuid]) && $this->resonanceCharge[$xuid] > 0) {
                $this->resonanceCharge[$xuid] = max(0, $this->resonanceCharge[$xuid] - 5);
            }
        }
    }
}
