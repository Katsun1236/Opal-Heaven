<?php

declare(strict_types=1);

namespace opal\core\ui;

use pocketmine\player\Player;

class SimpleForm implements Form{

    private string $title = "";
    private string $content = "";
    private array $buttons = [];
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

    public function addButton(string $text, int $imageType = -1, string $imagePath = "", ?string $label = null) : self{
        $button = ["text" => $text];
        if($imageType !== -1 && $imagePath !== ""){
            $button["image"] = [
                "type" => $imageType === 0 ? "path" : "url",
                "data" => $imagePath
            ];
        }
        if($label !== null){
            $this->buttons[$label] = $button;
        }else{
            $this->buttons[] = $button;
        }
        return $this;
    }

    public function handleResponse(Player $player, $data) : void{
        if($this->callable !== null){
            ($this->callable)($player, $data);
        }
    }

    public function jsonSerialize() : array{
        return [
            "type" => "form",
            "title" => $this->title,
            "content" => $this->content,
            "buttons" => array_values($this->buttons)
        ];
    }
}
