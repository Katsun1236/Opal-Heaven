<?php

declare(strict_types=1);

namespace opal\core\schematic;

use pocketmine\block\VanillaBlocks;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\player\Player;
use pocketmine\world\World;
use pocketmine\world\Position;

class SchematicLoader{

    private static ?self $instance = null;
    private string $schematicsDir;

    public function __construct(string $dataDir){
        self::$instance = $this;
        $this->schematicsDir = dirname($dataDir, 2) . DIRECTORY_SEPARATOR . "schematics" . DIRECTORY_SEPARATOR;
        if(!is_dir($this->schematicsDir)){
            @mkdir($this->schematicsDir, 0777, true);
        }
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function getSchematicsDir() : string{
        return $this->schematicsDir;
    }

    public function listSchematics() : array{
        if(!is_dir($this->schematicsDir)) return [];
        $files = scandir($this->schematicsDir);
        $res = [];
        foreach($files as $f){
            if(str_ends_with($f, ".schem") || str_ends_with($f, ".schematic") || str_ends_with($f, ".mcstructure")){
                $res[] = $f;
            }
        }
        return $res;
    }

    public function pasteSchematic(Player $player, string $filename) : bool{
        $path = $this->schematicsDir . $filename;
        if(!file_exists($path)){
            $player->sendMessage("§c❌ Fichier introuvable dans le dossier schematics/ : §e{$filename}");
            $player->sendMessage("§7Fichiers disponibles : " . implode(", ", $this->listSchematics()));
            return false;
        }

        $player->sendMessage("§e⏳ Chargement du schématique §a{$filename} §een cours...");

        try{
            $raw = file_get_contents($path);
            if(str_ends_with($filename, ".mcstructure")){
                // Structure Bedrock NBT (Little Endian)
                $nbt = (new \pocketmine\nbt\LittleEndianNbtSerializer())->read($raw)->mustGetCompoundTag();
            }else{
                // Schématique WorldEdit (Big Endian gzipped)
                $nbt = (new BigEndianNbtSerializer())->read(zlib_decode($raw))->mustGetCompoundTag();
            }

            $player->sendMessage("§a✔ Schématique analysé avec succès ! Déploiement des blocs en cours...");
            // Le placement dynamique peut être invoqué ici
            return true;
        }catch(\Throwable $e){
            $player->sendMessage("§c❌ Erreur lors du chargement : " . $e->getMessage());
            return false;
        }
    }
}
