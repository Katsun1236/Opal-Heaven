<?php

declare(strict_types=1);

namespace opalheaven\mod\forms;

use pocketmine\form\Form;
use pocketmine\player\Player;

class SimpleForm implements Form {

    private string $title = "";
    private string $content = "";
    /** @var array<int, array{text: string, image?: array{type: string, data: string}}> */
    private array $buttons = [];
    /** @var array<int, \Closure> */
    private array $callbacks = [];
    private ?\Closure $closeCallback = null;

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

    public function addButton(string $text, ?string $imageType = null, ?string $imagePath = null, ?\Closure $callback = null) : self {
        $button = ["text" => $text];
        if ($imageType !== null && $imagePath !== null) {
            $button["image"] = [
                "type" => $imageType, // "path" ou "url"
                "data" => $imagePath
            ];
        }
        $index = count($this->buttons);
        $this->buttons[$index] = $button;
        if ($callback !== null) {
            $this->callbacks[$index] = $callback;
        }
        return $this;
    }

    public function setCloseCallback(?\Closure $callback) : self {
        $this->closeCallback = $callback;
        return $this;
    }

    public function handleResponse(Player $player, $data) : void {
        if ($data === null) {
            if ($this->closeCallback !== null) {
                ($this->closeCallback)($player);
            }
            return;
        }

        if (is_int($data) && isset($this->callbacks[$data])) {
            ($this->callbacks[$data])($player, $data);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize() : array {
        return [
            "type" => "form",
            "title" => $this->title,
            "content" => $this->content,
            "buttons" => $this->buttons
        ];
    }
}
