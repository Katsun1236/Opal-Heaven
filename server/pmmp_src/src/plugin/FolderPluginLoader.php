<?php

declare(strict_types=1);

namespace pocketmine\plugin;

use pocketmine\thread\ThreadSafeClassLoader;
use function file_get_contents;
use function is_dir;
use function is_file;

class FolderPluginLoader implements PluginLoader{

    public function __construct(
        private ThreadSafeClassLoader $loader
    ){}

    public function canLoadPlugin(string $path) : bool{
        return is_dir($path) && is_file($path . DIRECTORY_SEPARATOR . "plugin.yml");
    }

    public function loadPlugin(string $file) : void{
        $description = $this->getPluginDescription($file);
        if($description !== null){
            $this->loader->addPath($description->getSrcNamespacePrefix(), $file . DIRECTORY_SEPARATOR . "src");
        }
    }

    public function getPluginDescription(string $file) : ?PluginDescription{
        $ymlPath = $file . DIRECTORY_SEPARATOR . "plugin.yml";
        if(is_file($ymlPath)){
            return new PluginDescription(file_get_contents($ymlPath));
        }
        return null;
    }

    public function getAccessProtocol() : string{
        return "";
    }
}
