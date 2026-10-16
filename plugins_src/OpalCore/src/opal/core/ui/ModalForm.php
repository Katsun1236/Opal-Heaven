<?php

declare(strict_types=1);

namespace opal\core\ui;

use pocketmine\player\Player;

class ModalForm implements Form{

    private string $title = "";
    private string $content = "";
    private string $button1 = "";
    private string $button2 = "";
    /** @var callable|null */
    private $callable;

    public function __construct(?callable $callable = null){
        $this->callable = $callable;
    }

    public function setTitle(string $title) : self{
        $this->title = $title;
        return $this;
    }

    public function setContent(string $content) : self{
        $this->content = $content;
        return $this;
    }

    public function setButton1(string $text) : self{
        $this->button1 = $text;
        return $this;
    }

    public function setButton2(string $text) : self{
        $this->button2 = $text;
        return $this;
    }

    public function handleResponse(Player $player, $data) : void{
        if($this->callable !== null){
            ($this->callable)($player, $data);
        }
    }

    public function jsonSerialize() : array{
        return [
            "type" => "modal",
            "title" => $this->title,
            "content" => $this->content,
            "button1" => $this->button1,
            "button2" => $this->button2
        ];
    }
}
