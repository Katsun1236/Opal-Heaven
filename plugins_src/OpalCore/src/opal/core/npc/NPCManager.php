<?php

declare(strict_types=1);

namespace opal\core\npc;

use opal\core\ah\AuctionHouseManager;
use opal\core\orders\OrderManager;
use opal\core\quests\QuestManager;
use opal\core\ui\FormManager;
use opal\core\ui\SimpleForm;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerEntityInteractEvent;
use pocketmine\player\Player;

class NPCManager implements Listener{

    private static ?self $instance = null;
    private OrderManager $orderManager;
    private QuestManager $questManager;
    private AuctionHouseManager $ahManager;

    public function __construct(OrderManager $orderManager, QuestManager $questManager, AuctionHouseManager $ahManager){
        self::$instance = $this;
        $this->orderManager = $orderManager;
        $this->questManager = $questManager;
        $this->ahManager = $ahManager;
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function openSireGabriel(Player $player) : void{
        $form = new SimpleForm(function(Player $player, $data) : void{
            if($data === null) return;
            switch($data){
                case 0: $this->orderManager->openOrderUI($player); break;
                case 1:
                    $player->sendMessage("§e[Sire Gabriel] §7« Il y a mille ans, l'Opale Primordiale s'est fragmentée, donnant naissance aux Failles Célestes et aux 4 Voies Sacrées... »");
                    break;
            }
        });

        $form->setTitle("§l§dSIRE GABRIEL — MAÎTRE DES ORDRES");
        $form->setContent("§f« Que la lumière de l'Opale guide tes pas, voyageur.\nQuel serment ton âme est-elle prête à prêter ? »");
        $form->addButton("§e✨ Consulter / Choisir ma Voie d'Ascension");
        $form->addButton("§b📜 Écouter le Lore des Failles Célestes");
        $form->addButton("§cPrendre congé");

        FormManager::getInstance()->sendForm($player, $form);
    }

    public function openThorgar(Player $player) : void{
        $form = new SimpleForm(function(Player $player, $data) : void{
            if($data === null) return;
            switch($data){
                case 0:
                    // Réparation rapide de l'item en main
                    $item = $player->getInventory()->getItemInHand();
                    if($item->isNull()){
                        $player->sendMessage("§c❌ Tenez un objet en main !");
                        return;
                    }
                    $player->sendMessage("§a🔨 Thorgar a reforgé votre acier céleste !");
                    break;
            }
        });

        $form->setTitle("§l§6THORGAR — FORGERON DES ABYSSES");
        $form->setContent("§f« Approche ton acier de ma forge céleste.\nPrends garde, le feu de l'abysse ne pardonne pas la maladresse. »");
        $form->addButton("§6🔨 Réparer l'objet en main");
        $form->addButton("§cPrendre congé");

        FormManager::getInstance()->sendForm($player, $form);
    }

    public function openDameSylvana(Player $player) : void{
        $form = new SimpleForm(function(Player $player, $data) : void{
            if($data === null) return;
            $player->sendMessage("§a[Dame Sylvana] §7« Prenez soin des graines de cobalt et d'opale, elles nourrissent l'âme du monde. »");
        });

        $form->setTitle("§l§aDAME SYLVANA — GARDIENNE DES MOISSONS");
        $form->setContent("§f« La terre chante sous les sabots des bêtes.\nApporte-moi tes moissons géantes et reçois la bénédiction végétale. »");
        $form->addButton("§a🌾 Acheter des Graines Moddées (Boutique)");
        $form->addButton("§cPrendre congé");

        FormManager::getInstance()->sendForm($player, $form);
    }

    public function openCassandra(Player $player) : void{
        $form = new SimpleForm(function(Player $player, $data) : void{
            if($data === null) return;
            switch($data){
                case 0: $this->ahManager->openMainMenu($player); break;
            }
        });

        $form->setTitle("§l§eCASSANDRA — BANQUIÈRE CÉLESTE");
        $form->setContent("§f« Ici, votre fortune est plus en sécurité que dans le donjon le plus imprenable.\nDéposez, empruntez, prospérez. »");
        $form->addButton("§e🏛 Accéder à l'Hôtel des Ventes");
        $form->addButton("§6🔒 Ouvrir mon Coffre Inviolable (Safe Box)");
        $form->addButton("§cPrendre congé");

        FormManager::getInstance()->sendForm($player, $form);
    }

    public function openEldrin(Player $player) : void{
        $this->questManager->openQuestsMenu($player);
    }
}
