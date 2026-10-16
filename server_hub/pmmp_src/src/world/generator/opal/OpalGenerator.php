<?php

declare(strict_types=1);

namespace pocketmine\world\generator\opal;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\noise\Simplex;
use function abs;
use function cos;
use function floor;
use function max;
use function min;
use function sin;

/**
 * OpalGenerator - Moteur de génération de monde fantastique pour Opal Heaven.
 * Génère des reliefs grandioses, des archipels célestes flottants,
 * des pics cristallins, des failles volcaniques et des forêts mystiques
 * adaptés aux 40 biomes d'Opal Heaven.
 */
class OpalGenerator extends Generator{

	public const WATER_LEVEL = 60;
	public const CLOUD_ISLAND_MIN_Y = 110;
	public const CLOUD_ISLAND_MAX_Y = 145;

	private Simplex $baseNoise;
	private Simplex $cliffNoise;
	private Simplex $islandNoise;
	private Simplex $chasmNoise;
	private Simplex $biomeNoise;
	private Simplex $caveNoise;

	// Blocs pré-calculés pour performances maximales
	private int $bedrock;
	private int $stone;
	private int $deepslate;
	private int $water;
	private int $lava;
	private int $magma;
	private int $blackstone;
	private int $basalt;
	private int $amethyst;
	private int $calcite;
	private int $smoothBasalt;
	private int $cryingObsidian;
	private int $obsidian;
	private int $glowstone;
	private int $seaLantern;
	private int $shroomlight;
	private int $grassBlock;
	private int $dirt;
	private int $podzol;
	private int $mycelium;
	private int $crimsonNylium;
	private int $warpedNylium;
	private int $sand;
	private int $redSand;
	private int $prismarine;
	private int $purpurBlock;
	private int $endStone;
	private int $emeraldOre;
	private int $diamondOre;
	private int $ancientDebris;
	private int $goldBlock;

	public function __construct(int $seed, string $preset){
		parent::__construct($seed, $preset);

		$this->baseNoise = new Simplex($this->random, 4, 0.45, 1 / 110);
		$this->cliffNoise = new Simplex($this->random, 3, 0.60, 1 / 45);
		$this->islandNoise = new Simplex($this->random, 3, 0.50, 1 / 30);
		$this->chasmNoise = new Simplex($this->random, 2, 0.50, 1 / 70);
		$this->biomeNoise = new Simplex($this->random, 3, 0.50, 1 / 200);
		$this->caveNoise = new Simplex($this->random, 2, 0.50, 1 / 25);

		// Cache des State IDs
		$this->bedrock = VanillaBlocks::BEDROCK()->getStateId();
		$this->stone = VanillaBlocks::STONE()->getStateId();
		$this->deepslate = VanillaBlocks::DEEPSLATE()->getStateId();
		$this->water = VanillaBlocks::WATER()->getStateId();
		$this->lava = VanillaBlocks::LAVA()->getStateId();
		$this->magma = VanillaBlocks::MAGMA()->getStateId();
		$this->blackstone = VanillaBlocks::BLACKSTONE()->getStateId();
		$this->basalt = VanillaBlocks::BASALT()->getStateId();
		$this->amethyst = VanillaBlocks::AMETHYST()->getStateId();
		$this->calcite = VanillaBlocks::CALCITE()->getStateId();
		$this->smoothBasalt = VanillaBlocks::SMOOTH_BASALT()->getStateId();
		$this->cryingObsidian = VanillaBlocks::CRYING_OBSIDIAN()->getStateId();
		$this->obsidian = VanillaBlocks::OBSIDIAN()->getStateId();
		$this->glowstone = VanillaBlocks::GLOWSTONE()->getStateId();
		$this->seaLantern = VanillaBlocks::SEA_LANTERN()->getStateId();
		$this->shroomlight = VanillaBlocks::SHROOMLIGHT()->getStateId();
		$this->grassBlock = VanillaBlocks::GRASS()->getStateId();
		$this->dirt = VanillaBlocks::DIRT()->getStateId();
		$this->podzol = VanillaBlocks::PODZOL()->getStateId();
		$this->mycelium = VanillaBlocks::MYCELIUM()->getStateId();
		$this->crimsonNylium = VanillaBlocks::CRIMSON_NYLIUM()->getStateId();
		$this->warpedNylium = VanillaBlocks::WARPED_NYLIUM()->getStateId();
		$this->sand = VanillaBlocks::SAND()->getStateId();
		$this->redSand = VanillaBlocks::RED_SAND()->getStateId();
		$this->prismarine = VanillaBlocks::PRISMARINE()->getStateId();
		$this->purpurBlock = VanillaBlocks::PURPUR()->getStateId();
		$this->endStone = VanillaBlocks::END_STONE()->getStateId();
		$this->emeraldOre = VanillaBlocks::EMERALD_ORE()->getStateId();
		$this->diamondOre = VanillaBlocks::DIAMOND_ORE()->getStateId();
		$this->ancientDebris = VanillaBlocks::ANCIENT_DEBRIS()->getStateId();
		$this->goldBlock = VanillaBlocks::GOLD()->getStateId();
	}

	public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void{
		$chunk = $world->getChunk($chunkX, $chunkZ);
		if($chunk === null){
			return;
		}

		$baseWorldX = $chunkX * 16;
		$baseWorldZ = $chunkZ * 16;

		for($x = 0; $x < 16; ++$x){
			$worldX = $baseWorldX + $x;
			for($z = 0; $z < 16; ++$z){
				$worldZ = $baseWorldZ + $z;

				// 1. Détermination de la famille de biome d'Opal
				$bNoise = $this->biomeNoise->noise2D($worldX, $worldZ, true);
				$biomeType = (int) floor($bNoise * 4.0); // 0 = Céleste, 1 = Mystique, 2 = Volcanique, 3 = Astral

				// 2. Hauteur de base avec collines et montagnes
				$baseH = 68 + ($this->baseNoise->noise2D($worldX, $worldZ, false) * 26);
				$cliff = $this->cliffNoise->noise2D($worldX, $worldZ, false);
				if($cliff > 0.35){
					// Pics vertigineux (spires cristallines)
					$baseH += ($cliff - 0.35) * 60;
				}

				// 3. Faille ou canyon abyssal
				$chasm = abs($this->chasmNoise->noise2D($worldX, $worldZ, false));
				if($chasm < 0.08){
					$canyonDepth = (1.0 - ($chasm / 0.08)) * 38;
					$baseH = max(28, $baseH - $canyonDepth);
				}

				$surfaceHeight = (int) floor($baseH);
				$surfaceHeight = min(220, max(15, $surfaceHeight));

				// 4. Palette de blocs de surface selon l'environnement d'Opal
				[$topBlock, $subBlock, $fluidBlock] = match($biomeType){
					0 => [$this->amethyst, $this->calcite, $this->seaLantern],           // Céleste / Cristal
					1 => [$this->warpedNylium, $this->podzol, $this->water],             // Mystique / Sélène
					2 => [$this->blackstone, $this->magma, $this->lava],                 // Volcanique / Abyssal
					default => [$this->purpurBlock, $this->endStone, $this->glowstone], // Astral / Ruines
				};

				// Bedrock indestructible au fond
				$chunk->setBlockStateId($x, 0, $z, $this->bedrock);
				$chunk->setBlockStateId($x, 1, $z, ($x + $z) % 2 === 0 ? $this->bedrock : $this->deepslate);

				// Remplissage vertical du sous-sol
				for($y = 2; $y <= $surfaceHeight; ++$y){
					if($y < 25){
						$chunk->setBlockStateId($x, $y, $z, $this->deepslate);
					}elseif($y === $surfaceHeight){
						$chunk->setBlockStateId($x, $y, $z, $topBlock);
					}elseif($y > $surfaceHeight - 4){
						$chunk->setBlockStateId($x, $y, $z, $subBlock);
					}else{
						$chunk->setBlockStateId($x, $y, $z, $this->stone);
					}
				}

				// Eau ou lave résiduelle en surface si sous le niveau de la mer
				if($surfaceHeight < self::WATER_LEVEL){
					$fillFluid = $biomeType === 2 ? $this->lava : $this->water;
					for($y = $surfaceHeight + 1; $y <= self::WATER_LEVEL; ++$y){
						$chunk->setBlockStateId($x, $y, $z, $fillFluid);
					}
				}

				// 5. ARCHIPELS CÉLESTES FLOTTANTS (Îles volantes majestueuses - Évaluation 2D ultra-rapide)
				$islandVal = $this->islandNoise->noise2D($worldX, $worldZ, false);
				if($islandVal > 0.42){
					$islandTop = min(self::CLOUD_ISLAND_MAX_Y, (int)(120 + ($islandVal - 0.42) * 45));
					$islandThick = (int)(($islandVal - 0.42) * 26) + 3;
					$islandBot = max(self::CLOUD_ISLAND_MIN_Y, $islandTop - $islandThick);

					for($y = $islandBot; $y <= $islandTop; ++$y){
						if($y === $islandTop){
							$chunk->setBlockStateId($x, $y, $z, $biomeType === 0 ? $this->calcite : $this->grassBlock);
						}elseif($y >= $islandTop - 2){
							$chunk->setBlockStateId($x, $y, $z, $subBlock);
						}elseif($islandVal > 0.65 && ($x + $z) % 4 === 0){
							$chunk->setBlockStateId($x, $y, $z, $this->seaLantern);
						}elseif($islandVal > 0.60){
							$chunk->setBlockStateId($x, $y, $z, $this->amethyst);
						}else{
							$chunk->setBlockStateId($x, $y, $z, $this->stone);
						}
					}
				}
			}
		}
	}

	public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void{
		$baseX = $chunkX * 16;
		$baseZ = $chunkZ * 16;

		// 1. Génération de Monolithes de Glyphes et Cristaux d'Opale
		for($i = 0; $i < 3; ++$i){
			$rx = $this->random->nextRange(2, 13);
			$rz = $this->random->nextRange(2, 13);
			$wx = $baseX + $rx;
			$wz = $baseZ + $rz;

			$chunk = $world->getChunk($chunkX, $chunkZ);
			if($chunk === null){
				continue;
			}

			// Trouver la surface
			for($y = 120; $y >= 30; --$y){
				$b = $chunk->getBlockStateId($rx, $y, $rz);
				if($b !== Block::EMPTY_STATE_ID && $b !== $this->water && $b !== $this->lava){
					// Pilier d'Opale ou Arbre Géant
					$pillarHeight = $this->random->nextRange(4, 9);
					for($h = 1; $h <= $pillarHeight; ++$h){
						$mat = ($h === $pillarHeight) ? $this->seaLantern : ($h % 2 === 0 ? $this->amethyst : $this->cryingObsidian);
						$chunk->setBlockStateId($rx, $y + $h, $rz, $mat);
					}
					break;
				}
			}
		}

		// 2. Veines de Gemmes Primordiales et Débris Antiques (Minage d'Opal Heaven)
		for($v = 0; $v < 6; ++$v){
			$vx = $this->random->nextRange(1, 14);
			$vy = $this->random->nextRange(5, 45);
			$vz = $this->random->nextRange(1, 14);

			$oreBlock = match($v % 4){
				0 => $this->ancientDebris,
				1 => $this->emeraldOre,
				2 => $this->diamondOre,
				default => $this->goldBlock
			};

			$chunk = $world->getChunk($chunkX, $chunkZ);
			if($chunk !== null && $chunk->getBlockStateId($vx, $vy, $vz) === $this->deepslate){
				$chunk->setBlockStateId($vx, $vy, $vz, $oreBlock);
				$chunk->setBlockStateId($vx + 1, $vy, $vz, $oreBlock);
			}
		}
	}
}
