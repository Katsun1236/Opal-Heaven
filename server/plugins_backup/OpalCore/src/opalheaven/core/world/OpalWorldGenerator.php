<?php

declare(strict_types=1);

namespace opalheaven\core\world;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\PalettedBlockArray;
use pocketmine\world\format\SubChunk;
use pocketmine\world\generator\Generator;

use opalheaven\core\biomes\BiomeRegistry;
use opalheaven\core\biomes\OpalBiome;

class OpalWorldGenerator extends Generator {

    public const SEA_LEVEL = 62;

    private int $bedrockStateId;
    private int $waterStateId;
    private int $lavaStateId;
    private int $stoneStateId;
    private int $deepslateStateId;
    private int $calciteStateId;
    private int $dioriteStateId;
    private int $amethystStateId;
    private int $magmaStateId;
    private int $obsidianStateId;
    private int $cryingObsidianStateId;
    private int $basaltStateId;
    private int $packedIceStateId;
    private int $blueIceStateId;
    private int $seaLanternStateId;
    private int $shroomlightStateId;

    public function __construct(int $seed, string $preset) {
        parent::__construct($seed, $preset);
        $this->bedrockStateId = VanillaBlocks::BEDROCK()->getStateId();
        $this->waterStateId = VanillaBlocks::WATER()->getStateId();
        $this->lavaStateId = VanillaBlocks::LAVA()->getStateId();
        $this->stoneStateId = VanillaBlocks::STONE()->getStateId();
        $this->deepslateStateId = VanillaBlocks::DEEPSLATE()->getStateId();
        $this->calciteStateId = VanillaBlocks::CALCITE()->getStateId();
        $this->dioriteStateId = VanillaBlocks::DIORITE()->getStateId();
        $this->amethystStateId = VanillaBlocks::AMETHYST()->getStateId();
        $this->magmaStateId = VanillaBlocks::MAGMA()->getStateId();
        $this->obsidianStateId = VanillaBlocks::OBSIDIAN()->getStateId();
        $this->cryingObsidianStateId = VanillaBlocks::CRYING_OBSIDIAN()->getStateId();
        $this->basaltStateId = VanillaBlocks::BASALT()->getStateId();
        $this->packedIceStateId = VanillaBlocks::PACKED_ICE()->getStateId();
        $this->blueIceStateId = VanillaBlocks::BLUE_ICE()->getStateId();
        $this->seaLanternStateId = VanillaBlocks::SEA_LANTERN()->getStateId();
        $this->shroomlightStateId = VanillaBlocks::SHROOMLIGHT()->getStateId();
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void {
        $chunk = $world->getChunk($chunkX, $chunkZ);
        if ($chunk === null) {
            return;
        }

        $biomeRegistry = BiomeRegistry::getInstance();

        // Initialisation propre de tous les subchunks
        $biomeArray = new PalettedBlockArray(BiomeIds::PLAINS);
        foreach ($chunk->getSubChunks() as $sy => $subChunk) {
            $chunk->setSubChunk($sy, new SubChunk(Block::EMPTY_STATE_ID, [], clone $biomeArray));
        }

        // Échantillonnage colonne par colonne (16x16)
        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                $worldX = ($chunkX * 16) + $x;
                $worldZ = ($chunkZ * 16) + $z;

                $biome = $biomeRegistry->pickBiomeAt($worldX, $worldZ, $this->seed);

                $topBlockId = $biome->getResolvedTopBlock()->getStateId();
                $subBlockId = $biome->getResolvedSubBlock()->getStateId();
                $deepBlockId = $biome->getResolvedDeepBlock()->getStateId();

                // 1. Calcul du relief géologique principal
                $height = $this->calculateHeight($worldX, $worldZ, $biome);

                // 2. Vérification de gouffres / failles (Chasms pour catégories volcaniques/corrompues)
                $isChasm = false;
                if ($biome->getCategory() === 'VOLCANIQUE' || $biome->getCategory() === 'CORROMPU') {
                    $chasmNoise = abs(sin($worldX * 0.04) * cos($worldZ * 0.04));
                    if ($chasmNoise < 0.07) {
                        $isChasm = true;
                        $height = min($height, 42);
                    }
                }

                // 3. Pose de la Bedrock
                $chunk->setBlockStateId($x, 0, $z, $this->bedrockStateId);
                if (($worldX + $worldZ) % 3 === 0) {
                    $chunk->setBlockStateId($x, 1, $z, $this->bedrockStateId);
                }

                // 4. Couches souterraines profondes (Deepslate sous Y=15, DeepBlock ensuite)
                $deepTop = max(1, $height - 4);
                for ($y = 1; $y < $deepTop; ++$y) {
                    if ($y === 1 && $chunk->getBlockStateId($x, 1, $z) === $this->bedrockStateId) {
                        continue;
                    }
                    if ($y <= 16) {
                        $chunk->setBlockStateId($x, $y, $z, $this->deepslateStateId);
                    } else {
                        $chunk->setBlockStateId($x, $y, $z, $deepBlockId);
                    }
                }

                // 5. Couche sous-jacente (SubBlock)
                for ($y = $deepTop; $y < $height; ++$y) {
                    $chunk->setBlockStateId($x, $y, $z, $subBlockId);
                }

                // 6. Bloc de surface (TopBlock)
                if ($height > 0) {
                    $chunk->setBlockStateId($x, $height, $z, $topBlockId);
                }

                // 7. Remplissage hydrographique ou magmatique
                if ($isChasm) {
                    for ($y = $height + 1; $y <= 40; ++$y) {
                        $chunk->setBlockStateId($x, $y, $z, $this->lavaStateId);
                    }
                } elseif ($height < self::SEA_LEVEL) {
                    for ($y = $height + 1; $y <= self::SEA_LEVEL; ++$y) {
                        $chunk->setBlockStateId($x, $y, $z, $this->waterStateId);
                    }
                }

                // 8. Génération des Îles Célestes Suspendues (Floating Islands)
                if ($biome->getCategory() === 'CELESTE' || $biome->getCategory() === 'ETHER') {
                    $this->generateFloatingIslands($chunk, $x, $z, $worldX, $worldZ, $topBlockId, $subBlockId);
                }
            }
        }
    }

    private function calculateHeight(int $x, int $z, OpalBiome $biome) : int {
        // Ondulations continentales harmoniques
        $c1 = sin($x * 0.0035) * cos($z * 0.0035) * 18.0;
        $c2 = sin($x * 0.011 + 1.7) * cos($z * 0.010 + 0.8) * 9.0;
        $c3 = sin($x * 0.032 + $z * 0.028) * 4.5;
        $c4 = cos($x * 0.065 - $z * 0.055) * 2.0;

        $mod = match ($biome->getCategory()) {
            'CELESTE', 'ETHER' => 16.0,
            'GLACIAIRE' => 22.0,
            'VOLCANIQUE' => 8.0,
            'ELECTRIQUE' => 10.0,
            'BIOLOGIQUE' => 4.0,
            'CORROMPU' => -6.0,
            'ABYSSAL' => -26.0,
            default => 2.0
        };

        $base = 66.0 + $c1 + $c2 + $c3 + $c4 + $mod;
        return (int) max(18, min(138, round($base)));
    }

    private function generateFloatingIslands(Chunk $chunk, int $lx, int $lz, int $wx, int $wz, int $topBlockId, int $subBlockId) : void {
        // Bruit 3D paramétrique pour les îles flottantes
        $islandNoise = sin($wx * 0.035) * cos($wz * 0.035) + 0.45 * sin($wx * 0.075 + 1.2) * cos($wz * 0.070 + 0.9);
        if ($islandNoise > 0.48) {
            $islandHeight = (int)(118 + ($islandNoise - 0.48) * 35.0);
            $thickness = (int)(($islandNoise - 0.48) * 22.0) + 3;

            $topY = min(148, $islandHeight);
            $bottomY = max(98, $topY - $thickness);

            for ($y = $bottomY; $y <= $topY; ++$y) {
                if ($y === $topY) {
                    $chunk->setBlockStateId($lx, $y, $lz, $topBlockId);
                } elseif ($y >= $topY - 2) {
                    $chunk->setBlockStateId($lx, $y, $lz, $subBlockId);
                } elseif ($y === $bottomY) {
                    $chunk->setBlockStateId($lx, $y, $lz, $this->calciteStateId);
                } else {
                    $chunk->setBlockStateId($lx, $y, $lz, $this->dioriteStateId);
                }
            }
        }
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void {
        $chunk = $world->getChunk($chunkX, $chunkZ);
        if ($chunk === null) {
            return;
        }

        $biomeRegistry = BiomeRegistry::getInstance();
        $sampleBiome = $biomeRegistry->pickBiomeAt(($chunkX * 16) + 8, ($chunkZ * 16) + 8, $this->seed);

        // 1. Minerais configurés dans le biome
        foreach ($sampleBiome->getOres() as $oreDef) {
            $chance = (int)($oreDef['chance_per_chunk'] ?? 4);
            $minY = (int)($oreDef['min_y'] ?? 10);
            $maxY = (int)($oreDef['max_y'] ?? 60);
            $oreName = (string)($oreDef['ore'] ?? 'coal_ore');

            $oreBlock = OpalBiome::resolveBlock($oreName);
            $oreStateId = $oreBlock->getStateId();

            for ($i = 0; $i < $chance; ++$i) {
                $ox = $this->random->nextBoundedInt(16);
                $oz = $this->random->nextBoundedInt(16);
                $oy = $this->random->nextRange($minY, $maxY);

                if ($oy > 1 && $oy < 150) {
                    for ($dx = 0; $dx <= 1; ++$dx) {
                        for ($dy = 0; $dy <= 1; ++$dy) {
                            $rx = $ox + $dx;
                            $ry = $oy + $dy;
                            $rz = $oz;
                            if ($rx < 16 && $ry > 0 && $ry < 150) {
                                $existing = $chunk->getBlockStateId($rx, $ry, $rz);
                                if ($existing !== Block::EMPTY_STATE_ID && $existing !== $this->bedrockStateId && $existing !== $this->waterStateId && $existing !== $this->lavaStateId) {
                                    $chunk->setBlockStateId($rx, $ry, $rz, $oreStateId);
                                }
                            }
                        }
                    }
                }
            }
        }

        // 2. Décorations et mégalithes thématiques
        $this->decorateChunk($chunk, $chunkX, $chunkZ, $sampleBiome);
    }

    private function decorateChunk(Chunk $chunk, int $chunkX, int $chunkZ, OpalBiome $biome) : void {
        $category = $biome->getCategory();

        switch ($category) {
            case 'CELESTE':
            case 'ETHER':
                // Aiguilles de cristal géantes & lanternes célestes
                if ($this->random->nextBoundedInt(2) === 0) {
                    $sx = $this->random->nextBoundedInt(12) + 2;
                    $sz = $this->random->nextBoundedInt(12) + 2;
                    $sy = $chunk->getHighestBlockAt($sx, $sz);
                    if ($sy !== null && $sy > 50 && $sy < 140) {
                        $height = $this->random->nextRange(5, 12);
                        for ($h = 1; $h <= $height; ++$h) {
                            $block = ($h === $height) ? $this->seaLanternStateId : $this->amethystStateId;
                            $chunk->setBlockStateId($sx, $sy + $h, $sz, $block);
                        }
                    }
                }
                break;

            case 'VOLCANIQUE':
                // Cheminées volcaniques, colonnes de basalte & magma
                $count = $this->random->nextRange(2, 4);
                for ($v = 0; $v < $count; ++$v) {
                    $sx = $this->random->nextBoundedInt(14) + 1;
                    $sz = $this->random->nextBoundedInt(14) + 1;
                    $sy = $chunk->getHighestBlockAt($sx, $sz);
                    if ($sy !== null && $sy > 20 && $sy < 130) {
                        $chunk->setBlockStateId($sx, $sy, $sz, $this->magmaStateId);
                        if ($this->random->nextBoundedInt(3) === 0) {
                            for ($h = 1; $h <= 3; ++$h) {
                                $chunk->setBlockStateId($sx, $sy + $h, $sz, $this->basaltStateId);
                            }
                        }
                    }
                }
                break;

            case 'GLACIAIRE':
                // Stalagmites et pics de glace éternelle
                if ($this->random->nextBoundedInt(2) === 0) {
                    $sx = $this->random->nextBoundedInt(12) + 2;
                    $sz = $this->random->nextBoundedInt(12) + 2;
                    $sy = $chunk->getHighestBlockAt($sx, $sz);
                    if ($sy !== null && $sy > 50 && $sy < 135) {
                        $height = $this->random->nextRange(6, 14);
                        for ($h = 1; $h <= $height; ++$h) {
                            $block = ($h % 3 === 0) ? $this->blueIceStateId : $this->packedIceStateId;
                            $chunk->setBlockStateId($sx, $sy + $h, $sz, $block);
                        }
                    }
                }
                break;

            case 'BIOLOGIQUE':
            case 'MYSTIQUE':
                // Champignons bioluminescents géants avec shroomlight
                if ($this->random->nextBoundedInt(3) === 0) {
                    $sx = $this->random->nextBoundedInt(10) + 3;
                    $sz = $this->random->nextBoundedInt(10) + 3;
                    $sy = $chunk->getHighestBlockAt($sx, $sz);
                    if ($sy !== null && $sy > 45 && $sy < 130) {
                        $stemHeight = $this->random->nextRange(4, 7);
                        for ($h = 1; $h <= $stemHeight; ++$h) {
                            $chunk->setBlockStateId($sx, $sy + $h, $sz, $this->calciteStateId);
                        }
                        // Chapeau de champignon
                        $capY = $sy + $stemHeight + 1;
                        $chunk->setBlockStateId($sx, $capY, $sz, $this->shroomlightStateId);
                        for ($dx = -1; $dx <= 1; ++$dx) {
                            for ($dz = -1; $dz <= 1; ++$dz) {
                                if (abs($dx) + abs($dz) <= 1) {
                                    $chunk->setBlockStateId($sx + $dx, $capY, $sz + $dz, $this->shroomlightStateId);
                                }
                            }
                        }
                    }
                }
                break;

            case 'ELECTRIQUE':
                // Obélisques cinétiques en obsidienne pleureuse
                if ($this->random->nextBoundedInt(2) === 0) {
                    $sx = $this->random->nextBoundedInt(12) + 2;
                    $sz = $this->random->nextBoundedInt(12) + 2;
                    $sy = $chunk->getHighestBlockAt($sx, $sz);
                    if ($sy !== null && $sy > 40 && $sy < 130) {
                        $height = $this->random->nextRange(4, 8);
                        for ($h = 1; $h <= $height; ++$h) {
                            $block = ($h === $height) ? $this->seaLanternStateId : $this->cryingObsidianStateId;
                            $chunk->setBlockStateId($sx, $sy + $h, $sz, $block);
                        }
                    }
                }
                break;
        }
    }
}
