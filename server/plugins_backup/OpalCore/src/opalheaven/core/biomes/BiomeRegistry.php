<?php

declare(strict_types=1);

namespace opalheaven\core\biomes;

use pocketmine\utils\Config;

class BiomeRegistry {

    private static ?self $instance = null;

    /** @var array<string, OpalBiome> id => OpalBiome */
    private array $biomes = [];

    /** @var array<string, array<OpalBiome>> category => OpalBiome[] */
    private array $categories = [];

    private string $dataDir;

    public function __construct(string $dataDir) {
        self::$instance = $this;
        $this->dataDir = rtrim($dataDir, "/\\") . DIRECTORY_SEPARATOR;
        $this->loadAll();
    }

    public static function getInstance() : self {
        if (self::$instance === null) {
            throw new \RuntimeException("BiomeRegistry non initialise !");
        }
        return self::$instance;
    }

    public function loadAll() : void {
        $this->biomes = [];
        $this->categories = [];

        if (!is_dir($this->dataDir)) {
            @mkdir($this->dataDir, 0777, true);
        }

        $files = glob($this->dataDir . "*.yml");
        if ($files === false || count($files) === 0) {
            return;
        }

        foreach ($files as $file) {
            try {
                $yaml = function_exists('yaml_parse_file') ? @yaml_parse_file($file) : (new \pocketmine\utils\Config($file, \pocketmine\utils\Config::YAML))->getAll();
                if (is_array($yaml) && isset($yaml['id'])) {
                    $biome = OpalBiome::fromArray($yaml);
                    $this->biomes[$biome->getId()] = $biome;
                    $this->categories[$biome->getCategory()][] = $biome;
                }
            } catch (\Throwable $e) {
                // Erreur de parsing sur un fichier de biome
            }
        }
    }

    public function getBiome(string $id) : ?OpalBiome {
        return $this->biomes[$id] ?? null;
    }

    /**
     * @return array<string, OpalBiome>
     */
    public function getAll() : array {
        return $this->biomes;
    }

    /**
     * @return array<OpalBiome>
     */
    public function getByCategory(string $category) : array {
        return $this->categories[strtoupper($category)] ?? [];
    }

    public function getCount() : int {
        return count($this->biomes);
    }

    /**
     * Sélectionne de manière déterministe et continue le biome pour une coordonnée monde (X, Z).
     * Utilise un bruit harmonique multi-octave simulant Température, Humidité et Weirdness.
     */
    public function pickBiomeAt(int $x, int $z, int $seed) : OpalBiome {
        if (empty($this->biomes)) {
            // Biome fallback si aucun n'est chargé
            return new OpalBiome(
                "opal:fallback", "Plaines d'Opale", "MYSTIQUE", 5, "Plaines par défaut",
                0.5, 0.5, 0.5, "#ffffff", "#ffffff", "#3f76e4", "#79c05a",
                "grass", "dirt", "stone", null, "minecraft:enchantment_table", 1.0, []
            );
        }

        // Échelle spatiale des biomes (~350 blocs par biome en moyenne)
        $scale = 0.0028;
        $seedFloat = ($seed % 10000) * 0.1337;

        // Bruit de température normalisé entre 0.0 et 1.0
        $t = sin($x * $scale + $seedFloat) * cos($z * $scale * 0.85 + 1.23) 
           + 0.4 * sin($x * $scale * 2.3 + $z * $scale * 1.9 + $seedFloat * 0.5);
        $temp = ($t + 1.4) / 2.8;
        $temp = max(0.0, min(1.0, $temp));

        // Bruit d'humidité normalisé entre 0.0 et 1.0
        $h = cos($x * $scale * 0.95 + 2.51 + $seedFloat) * sin($z * $scale * 1.05 + 0.82)
           + 0.4 * cos($x * $scale * 2.1 - $z * $scale * 2.4);
        $humid = ($h + 1.4) / 2.8;
        $humid = max(0.0, min(1.0, $humid));

        // Bruit de bizarrerie / altitude normalisé
        $w = sin(($x + $z) * $scale * 1.15 + $seedFloat * 0.7);
        $weird = ($w + 1.0) / 2.0;

        $bestBiome = null;
        $bestScore = PHP_FLOAT_MAX;

        foreach ($this->biomes as $biome) {
            // Distance euclidienne pondérée dans l'espace climatologique (Température, Humidité, Downfall)
            $dt = $temp - $biome->getTemperature();
            $dh = $humid - $biome->getHumidity();
            $dw = $weird - $biome->getDownfall();

            $dist = ($dt * $dt) * 1.2 + ($dh * $dh) * 1.2 + ($dw * $dw) * 0.6;

            // Facteur de rareté : les biomes rares (ex: Rareté 9-10) demandent un alignement climatique plus précis
            $rarityMultiplier = 1.0 + (10 - $biome->getRarity()) * 0.08;
            $score = $dist * $rarityMultiplier;

            if ($score < $bestScore) {
                $bestScore = $score;
                $bestBiome = $biome;
            }
        }

        return $bestBiome ?? reset($this->biomes);
    }
}
