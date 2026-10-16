<?php

declare(strict_types=1);

namespace opal\core\ui;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\player\Player;

class FormManager implements Listener{

    private static ?self $instance = null;
    /** @var Form[] */
    private array $forms = [];
    private int $formIdCounter = 10000;

    public function __construct(){
        self::$instance = $this;
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function sendForm(Player $player, Form $form) : void{
        $id = $this->formIdCounter++;
        $this->forms[$player->getName()][$id] = $form;

        $pk = ModalFormRequestPacket::create($id, json_encode($form->jsonSerialize()));
        $player->getNetworkSession()->sendDataPacket($pk);
    }

    public function onPacketReceive(DataPacketReceiveEvent $event) : void{
        $pk = $event->getPacket();
        if(!$pk instanceof ModalFormResponsePacket){
            return;
        }

        $player = $event->getOrigin()->getPlayer();
        if($player === null){
            return;
        }

        $id = $pk->formId;
        $name = $player->getName();

        if(isset($this->forms[$name][$id])){
            $form = $this->forms[$name][$id];
            unset($this->forms[$name][$id]);
            $data = json_decode($pk->formData, true);
            $form->handleResponse($player, $data);
        }
    }
}
