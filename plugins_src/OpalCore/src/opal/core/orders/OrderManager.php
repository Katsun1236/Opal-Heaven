<?php

declare(strict_types=1);

namespace opal\core\orders;

use opal\core\database\DatabaseManager;
use opal\core\ui\FormManager;
use opal\core\ui\SimpleForm;
use pocketmine\player\Player;

class OrderManager{

    private static ?self $instance = null;
    private DatabaseManager $database;

    public const ORDER_SANG = "SANG";
    public const ORDER_TERRE = "TERRE";
    public const ORDER_ABYSSES = "ABYSSES";
    public const ORDER_FORGE = "FORGE";

    public function __construct(DatabaseManager $database){
        self::$instance = $this;
        $this->database = $database;
    }

    public static function getInstance() : self{
        return self::$instance ?? throw new \RuntimeException("OrderManager has not been initialized yet.");
    }

    public function getPlayerOrder(Player|string $player) : ?array{
        $key = $player instanceof Player
            ? (!empty($player->getXuid()) ? $player->getXuid() : strtolower($player->getName()))
            : $player;

        $db = $this->database->getDb();
        $stmt = $db->prepare("SELECT order_name, level, experience, specialization FROM player_orders WHERE xuid = :key LIMIT 1");
        if($stmt === false) return null;

        $stmt->bindValue(":key", $key, SQLITE3_TEXT);
        $res = $stmt->execute();
        if($res === false) return null;

        $row = $res->fetchArray(SQLITE3_ASSOC);
        return $row !== false ? $row : null;
    }

    public function setPlayerOrder(Player $player, string $orderName) : bool{
        $validOrders = [self::ORDER_SANG, self::ORDER_TERRE, self::ORDER_ABYSSES, self::ORDER_FORGE];
        if(!in_array($orderName, $validOrders, true)){
            return false;
        }

        $key = !empty($player->getXuid()) ? $player->getXuid() : strtolower($player->getName());
        $db = $this->database->getDb();
        $stmt = $db->prepare("INSERT INTO player_orders (xuid, order_name, level, experience) VALUES (:key, :order, 1, 0)
            ON CONFLICT(xuid, order_name) DO NOTHING");
        if($stmt === false) return false;

        $stmt->bindValue(":key", $key, SQLITE3_TEXT);
        $stmt->bindValue(":order", $orderName, SQLITE3_TEXT);
        $stmt->execute();

        // Validation automatique du Chapitre 1 des quêtes ("Le Choix du Destin")
        try{
            \opal\core\quests\QuestManager::getInstance()->setQuestCompleted($key, "chap_1");
        }catch(\Throwable){}

        return true;
    }

    public function resetPlayerOrder(Player $player) : bool{
        $key = !empty($player->getXuid()) ? $player->getXuid() : strtolower($player->getName());
        $db = $this->database->getDb();
        $stmt = $db->prepare("DELETE FROM player_orders WHERE xuid = :key");
        if($stmt === false) return false;

        $stmt->bindValue(":key", $key, SQLITE3_TEXT);
        $stmt->execute();
        return true;
    }

    public function addExperience(Player $player, int $amount) : void{
        if($amount <= 0) return;

        $data = $this->getPlayerOrder($player);
        if($data === null) return;

        $key = !empty($player->getXuid()) ? $player->getXuid() : strtolower($player->getName());
        $order = $data["order_name"];
        $lvl = (int)$data["level"];
        $xp = (int)$data["experience"] + $amount;

        $leveledUp = false;
        while($lvl < 50 && $xp >= ($lvl * 1000)){
            $xp -= ($lvl * 1000);
            $lvl++;
            $leveledUp = true;
        }

        if($leveledUp){
            $player->sendTitle("§6★ NIVEAU D'ORDRE SUPÉRIEUR ★", "§eTu es maintenant niveau {$lvl} dans l'Ordre {$order} !", 10, 40, 10);
            if($lvl >= 20){
                try{
                    \opal\core\quests\QuestManager::getInstance()->setQuestCompleted($key, "chap_10");
                }catch(\Throwable){}
            }
        }

        $db = $this->database->getDb();
        $stmt = $db->prepare("UPDATE player_orders SET level = :lvl, experience = :xp WHERE xuid = :xuid AND order_name = :order");
        if($stmt === false) return;

        $stmt->bindValue(":lvl", $lvl, SQLITE3_INTEGER);
        $stmt->bindValue(":xp", $xp, SQLITE3_INTEGER);
        $stmt->bindValue(":xuid", $key, SQLITE3_TEXT); // Correction: $key au lieu de $xuid non défini
        $stmt->bindValue(":order", $order, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function openOrderUI(Player $player) : void{
        $orderData = $this->getPlayerOrder($player);

        if($orderData === null){
            // Choisir un Ordre
            $form = new SimpleForm(function(Player $player, $data) : void{
                if($data === null) return;
                $orders = [self::ORDER_SANG, self::ORDER_TERRE, self::ORDER_ABYSSES, self::ORDER_FORGE];
                if(isset($orders[$data])){
                    $chosen = $orders[$data];
                    $this->setPlayerOrder($player, $chosen);
                    $player->sendMessage("§a✨ Félicitations ! Vous avez prêté serment à l'Ordre : §e" . $chosen);
                }
            });

            $form->setTitle("§l§dLES 4 ORDRES D'ASCENSION");
            $form->setContent("§7Bienvenue voyageur céleste. Choisissez votre Voie pour débloquer des passifs divins :\n\n" .
                "§c⚔ Ordre du Sang §7: Dégâts de posture accrus (+15%) & Vampirisme mêlée.\n" .
                "§a🌿 Ordre de la Terre §7: Régénération accélérée & Croissance végétale rapide.\n" .
                "§b⛏ Ordre des Abysses §7: Minage rapide, télékinésie & gemmes rares.\n" .
                "§6🛡 Ordre de la Forge §7: Solidité d'armures, résistance au recul & maîtrise des enclumes.");

            $form->addButton("§cOrdre du Sang\n§8[Guerriers & Duellistes]");
            $form->addButton("§aOrdre de la Terre\n§8[Druides & Agriculteurs]");
            $form->addButton("§bOrdre des Abysses\n§8[Mineurs & Explorateurs]");
            $form->addButton("§6Ordre de la Forge\n§8[Artisans & Forgerons]");

            FormManager::getInstance()->sendForm($player, $form);
        }else{
            // Voir son Ordre actuel
            $form = new SimpleForm(function(Player $player, $data) : void{
                // Fermer
            });

            $order = $orderData["order_name"];
            $lvl = $orderData["level"];
            $xp = $orderData["experience"];
            $needed = $lvl * 1000;

            $form->setTitle("§l§dORDRE : §e" . $order);
            $form->setContent(
                "§fNiveau d'Ordre : §a{$lvl} / 50\n" .
                "§fProgression XP : §b{$xp} / {$needed} XP\n\n" .
                "§ePassifs débloqués :\n" .
                $this->getOrderPassiveDescription($order, (int)$lvl) . "\n\n" .
                "§7Parlez à Sire Gabriel au Hub pour réinitialiser votre serment."
            );
            $form->addButton("§aFermer");

            FormManager::getInstance()->sendForm($player, $form);
        }
    }

    private function getOrderPassiveDescription(string $order, int $level) : string{
        return match($order){
            self::ORDER_SANG => "§c• Vol de vie: +" . min(20, $level * 0.4) . "%\n§c• Dégâts de posture: +" . min(30, $level * 0.6) . "%",
            self::ORDER_TERRE => "§a• Régénération PV: +" . min(25, $level * 0.5) . "%\n§a• Rendement agricole: +" . min(50, $level * 1.0) . "%",
            self::ORDER_ABYSSES => "§b• Vitesse de minage: +" . min(30, $level * 0.6) . "%\n§b• Chance de gemmes: +" . min(15, $level * 0.3) . "%",
            self::ORDER_FORGE => "§6• Réduction usure: +" . min(40, $level * 0.8) . "%\n§6• Résistance aux chocs: +" . min(25, $level * 0.5) . "%",
            default => "§7Aucun passif"
        };
    }
}
