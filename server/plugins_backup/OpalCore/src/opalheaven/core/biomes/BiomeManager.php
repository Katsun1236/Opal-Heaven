<?php

declare(strict_types=1);

namespace opalheaven\core\biomes;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\StringToEffectParser;
use pocketmine\player\Player;
use pocketmine\world\particle\EnchantmentTableParticle;
use pocketmine\world\particle\FlameParticle;
use pocketmine\world\particle\LavaParticle;
use pocketmine\world\particle\PortalParticle;
use pocketmine\world\particle\SporeParticle;
use pocketmine\world\particle\RedstoneParticle;
use pocketmine\world\particle\Particle;

class BiomeManager {

    /** @var array<string, string> playerName => lastBiomeId */
    private array $playerBiomes = [];

    private BiomeRegistry $registry;

    public function __construct(BiomeRegistry $registry) {
        $this->registry = $registry;
    }

    public function tickPlayer(Player $player) : void {
        if (!$player->isOnline() || !$player->isAlive() || !$player->spawned) {
            return;
        }

        $pos = $player->getPosition();
        $world = $player->getWorld();
        $seed = $world->getSeed();

        $biome = $this->registry->pickBiomeAt((int) floor($pos->x), (int) floor($pos->z), $seed);
        $playerName = $player->getName();
        $lastBiomeId = $this->playerBiomes[$playerName] ?? null;

        // Détection de transition de biome
        if ($lastBiomeId !== $biome->getId()) {
            $this->playerBiomes[$playerName] = $biome->getId();

            // Notification visuelle native Bedrock
            $player->sendActionBarMessage("§l§d[BIOME] §r" . $biome->getName() . " §7(" . $biome->getCategory() . ")");
            $player->sendTitle($biome->getName(), "§f" . $biome->getDescription(), 10, 45, 10);
        }

        // 1. Application de l'Aura du biome
        $auraName = $biome->getAuraEffect();
        if ($auraName !== null) {
            $effect = StringToEffectParser::getInstance()->parse(strtolower($auraName));
            if ($effect !== null) {
                // Effet appliqué pour 3 secondes (60 ticks), renouvelé tant que le joueur est dans le biome
                $player->getEffects()->add(new EffectInstance($effect, 60, 0, false, false));
            }
        }

        // 2. Particules ambiantes immersives autour du joueur
        $particle = $this->resolveParticle($biome->getParticle(), $biome->getCategory());
        if ($particle !== null) {
            $spawnPos = $pos->add(
                (mt_rand(-20, 20) / 10),
                (mt_rand(2, 18) / 10),
                (mt_rand(-20, 20) / 10)
            );
            $world->addParticle($spawnPos, $particle, [$player]);
        }
    }

    private function resolveParticle(string $name, string $category) : ?Particle {
        return match ($category) {
            'CELESTE', 'ETHER' => new EnchantmentTableParticle(),
            'VOLCANIQUE' => new LavaParticle(),
            'BIOLOGIQUE' => new SporeParticle(),
            'CORROMPU' => new PortalParticle(),
            'ELECTRIQUE' => new FlameParticle(),
            'GLACIAIRE' => new RedstoneParticle(),
            default => new EnchantmentTableParticle()
        };
    }

    public function getPlayerCurrentBiome(Player $player) : OpalBiome {
        $pos = $player->getPosition();
        $seed = $player->getWorld()->getSeed();
        return $this->registry->pickBiomeAt((int) floor($pos->x), (int) floor($pos->z), $seed);
    }

    public function removePlayer(Player $player) : void {
        unset($this->playerBiomes[$player->getName()]);
    }
}
