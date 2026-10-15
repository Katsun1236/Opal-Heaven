<?php

declare(strict_types=1);

namespace opalheaven\core\biomes;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\StringToItemParser;

class OpalBiome {

    private string $id;
    private string $name;
    private string $category;
    private int $rarity;
    private string $description;

    private float $temperature;
    private float $humidity;
    private float $downfall;

    private string $skyColor;
    private string $fogColor;
    private string $waterColor;
    private string $grassColor;

    private string $topBlockId;
    private string $subBlockId;
    private string $deepBlockId;

    private ?string $auraEffect;
    private string $particle;
    private float $gravity;

    /** @var array<array{ore: string, chance_per_chunk: int, min_y: int, max_y: int}> */
    private array $ores;

    private ?Block $cachedTopBlock = null;
    private ?Block $cachedSubBlock = null;
    private ?Block $cachedDeepBlock = null;

    public function __construct(
        string $id,
        string $name,
        string $category,
        int $rarity,
        string $description,
        float $temperature,
        float $humidity,
        float $downfall,
        string $skyColor,
        string $fogColor,
        string $waterColor,
        string $grassColor,
        string $topBlockId,
        string $subBlockId,
        string $deepBlockId,
        ?string $auraEffect,
        string $particle,
        float $gravity,
        array $ores
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
        $this->rarity = $rarity;
        $this->description = $description;
        $this->temperature = $temperature;
        $this->humidity = $humidity;
        $this->downfall = $downfall;
        $this->skyColor = $skyColor;
        $this->fogColor = $fogColor;
        $this->waterColor = $waterColor;
        $this->grassColor = $grassColor;
        $this->topBlockId = $topBlockId;
        $this->subBlockId = $subBlockId;
        $this->deepBlockId = $deepBlockId;
        $this->auraEffect = $auraEffect;
        $this->particle = $particle;
        $this->gravity = $gravity;
        $this->ores = $ores;
    }

    public static function fromArray(array $data) : self {
        return new self(
            (string)($data['id'] ?? 'opal:unknown'),
            (string)($data['name'] ?? 'Biome Inconnu'),
            (string)($data['category'] ?? 'MYSTIQUE'),
            (int)($data['rarity'] ?? 5),
            (string)($data['description'] ?? ''),
            (float)($data['climate']['temperature'] ?? 0.5),
            (float)($data['climate']['humidity'] ?? 0.5),
            (float)($data['climate']['downfall'] ?? 0.5),
            (string)($data['colors']['sky_color'] ?? '#ffffff'),
            (string)($data['colors']['fog_color'] ?? '#ffffff'),
            (string)($data['colors']['water_color'] ?? '#3f76e4'),
            (string)($data['colors']['grass_color'] ?? '#79c05a'),
            (string)($data['blocks']['top_block'] ?? 'grass'),
            (string)($data['blocks']['sub_block'] ?? 'dirt'),
            (string)($data['blocks']['deep_block'] ?? 'stone'),
            isset($data['effects']['aura_effect']) ? (string)$data['effects']['aura_effect'] : null,
            (string)($data['effects']['particle'] ?? 'minecraft:enchantment_table'),
            (float)($data['effects']['gravity'] ?? 1.0),
            (array)($data['ores'] ?? [])
        );
    }

    public function getId() : string { return $this->id; }
    public function getName() : string { return $this->name; }
    public function getCategory() : string { return $this->category; }
    public function getRarity() : int { return $this->rarity; }
    public function getDescription() : string { return $this->description; }
    public function getTemperature() : float { return $this->temperature; }
    public function getHumidity() : float { return $this->humidity; }
    public function getDownfall() : float { return $this->downfall; }
    public function getSkyColor() : string { return $this->skyColor; }
    public function getFogColor() : string { return $this->fogColor; }
    public function getWaterColor() : string { return $this->waterColor; }
    public function getGrassColor() : string { return $this->grassColor; }
    public function getTopBlockId() : string { return $this->topBlockId; }
    public function getSubBlockId() : string { return $this->subBlockId; }
    public function getDeepBlockId() : string { return $this->deepBlockId; }
    public function getAuraEffect() : ?string { return $this->auraEffect; }
    public function getParticle() : string { return $this->particle; }
    public function getGravity() : float { return $this->gravity; }
    public function getOres() : array { return $this->ores; }

    public static function resolveBlock(string $id) : Block {
        // Normaliser les alias connus
        $aliases = [
            'magma_block' => 'magma',
            'moss_block' => 'mossy_stone_bricks',
            'sculk_catalyst' => 'sculk',
            'quartz_block' => 'quartz',
            'white_concrete' => 'concrete',
        ];

        $target = $aliases[$id] ?? $id;

        $item = StringToItemParser::getInstance()->parse($target);
        if ($item !== null) {
            return $item->getBlock();
        }

        // Recherche par nom de méthode VanillaBlocks
        $clean = strtoupper(str_replace(['minecraft:', ' '], ['', '_'], $target));
        if (method_exists(VanillaBlocks::class, $clean)) {
            return VanillaBlocks::$clean();
        }

        return VanillaBlocks::STONE();
    }

    public function getResolvedTopBlock() : Block {
        if ($this->cachedTopBlock === null) {
            $this->cachedTopBlock = self::resolveBlock($this->topBlockId);
        }
        return clone $this->cachedTopBlock;
    }

    public function getResolvedSubBlock() : Block {
        if ($this->cachedSubBlock === null) {
            $this->cachedSubBlock = self::resolveBlock($this->subBlockId);
        }
        return clone $this->cachedSubBlock;
    }

    public function getResolvedDeepBlock() : Block {
        if ($this->cachedDeepBlock === null) {
            $this->cachedDeepBlock = self::resolveBlock($this->deepBlockId);
        }
        return clone $this->cachedDeepBlock;
    }
}
