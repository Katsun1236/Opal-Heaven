<?php

declare(strict_types=1);

namespace opalheaven\core\forms;

use pocketmine\form\Form;
use pocketmine\player\Player;

class ModalForm implements Form {

    private string $title = "";
    private string $content = "";
    private string $button1 = "Confirmer";
    private string $button2 = "Annuler";
    private ?\Closure $callback = null;

    public function __construct(string $title = "", string $content = "") {
        $this->title = $title;
        $this->content = $content;
    }

    public function setTitle(string $title) : self {
        $this->title = $title;
        return $this;
    }

    public function setContent(string $content) : self {
        $this->content = $content;
        return $this;
    }

    public function setButton1(string $text) : self {
        $this->button1 = $text;
        return $this;
    }

    public function setButton2(string $text) : self {
        $this->button2 = $text;
        return $this;
    }

    public function setCallback(?\Closure $callback) : self {
        $this->callback = $callback;
        return $this;
    }

    public function handleResponse(Player $player, $data) : void {
        if ($this->callback !== null && is_bool($data)) {
            ($this->callback)($player, $data);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize() : array {
        return [
            "type" => "modal",
            "title" => $this->title,
            "content" => $this->content,
            "button1" => $this->button1,
            "button2" => $this->button2
        ];
    }
}
