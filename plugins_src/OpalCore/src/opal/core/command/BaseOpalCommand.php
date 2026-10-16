<?php

declare(strict_types=1);

namespace opal\core\command;

use opal\core\OpalCore;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class BaseOpalCommand extends Command{

    /** @var \Closure(Player, array<string>) : void */
    private \Closure $handler;

    /**
     * @param string[] $aliases
     * @param \Closure(Player, array<string>) : void $handler
     */
    public function __construct(
        string $name,
        string $description,
        string $usage,
        array $aliases,
        ?string $permission,
        \Closure $handler
    ){
        parent::__construct($name, $description, $usage, $aliases);
        $perm = $permission ?? \pocketmine\permission\DefaultPermissionNames::GROUP_USER;
        $pm = \pocketmine\permission\PermissionManager::getInstance();
        if($pm->getPermission($perm) === null){
            $pm->addPermission(new \pocketmine\permission\Permission($perm));
        }
        $this->setPermission($perm);
        $this->handler = $handler;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
        if(!$sender instanceof Player){
            $sender->sendMessage("§cCette commande doit être exécutée en jeu.");
            return;
        }

        if(!$this->testPermission($sender)){
            return;
        }

        ($this->handler)($sender, $args);
    }
}
