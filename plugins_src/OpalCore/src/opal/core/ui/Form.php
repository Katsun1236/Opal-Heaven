<?php

declare(strict_types=1);

namespace opal\core\ui;

use pocketmine\player\Player;

interface Form{
    public function handleResponse(Player $player, $data) : void;
    public function jsonSerialize() : array;
}
