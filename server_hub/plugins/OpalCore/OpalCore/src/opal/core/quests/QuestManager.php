<?php

declare(strict_types=1);

namespace opal\core\quests;

use opal\core\database\DatabaseManager;
use opal\core\economy\EconomyManager;
use opal\core\orders\OrderManager;
use opal\core\ui\FormManager;
use opal\core\ui\SimpleForm;
use pocketmine\player\Player;

class QuestManager{

    private static ?self $instance = null;
    private DatabaseManager $database;
    private EconomyManager $economy;
    private OrderManager $orderManager;

    private array $chapters = [
        1 => ["title" => "Chapitre 1 — Le Choix du Destin", "desc" => "Sélectionner son Ordre d'Ascension via /order", "reward_money" => 1000],
        2 => ["title" => "Chapitre 2 — L'Écho des Failles", "desc" => "Miner 32 blocs de Cobalt ou Titane", "reward_money" => 2500],
        3 => ["title" => "Chapitre 3 — Le Premier Sanctuaire", "desc" => "Fonder ou rejoindre une Faction (/f create ou /f join)", "reward_money" => 3000],
        4 => ["title" => "Chapitre 4 — L'Art du Commerce", "desc" => "Effectuer un échange sécurisé via /trade ou l'HDV /ah", "reward_money" => 2500],
        5 => ["title" => "Chapitre 5 — La Domestication", "desc" => "Élever ou dresser une bête céleste avec un catalyseur", "reward_money" => 4000],
        6 => ["title" => "Chapitre 6 — L'Appel des Moissons", "desc" => "Produire de la Poussière Fertile et arroser vos cultures", "reward_money" => 3500],
        7 => ["title" => "Chapitre 7 — La Forge Arcanique", "desc" => "Appliquer un parchemin d'enchantement sur votre arme", "reward_money" => 5000],
        8 => ["title" => "Chapitre 8 — L'Épreuve du Donjon", "desc" => "Vaincre un boss de donjon avec votre escouade", "reward_money" => 7500],
        9 => ["title" => "Chapitre 9 — Le Sang de la Warzone", "desc" => "Réussir 5 parades parfaites et participer à un KOTH", "reward_money" => 10000],
        10 => ["title" => "Chapitre 10 — L'Apogée Céleste", "desc" => "Atteindre le niveau 20 de votre Ordre Céleste", "reward_money" => 25000],
    ];

    public function __construct(DatabaseManager $database, EconomyManager $economy, OrderManager $orderManager){
        self::$instance = $this;
        $this->database = $database;
        $this->economy = $economy;
        $this->orderManager = $orderManager;
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function getQuestProgress(string $xuid, string $questId) : array{
        $db = $this->database->getDb();
        $stmt = $db->prepare("SELECT * FROM player_quests WHERE xuid = :xuid AND quest_id = :qid");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $stmt->bindValue(":qid", $questId, SQLITE3_TEXT);
        $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        if($res !== false){
            return $res;
        }
        return ["progress" => 0, "completed" => 0, "claimed" => 0];
    }

    public function setQuestCompleted(string $xuid, string $questId) : void{
        $db = $this->database->getDb();
        $stmt = $db->prepare("INSERT INTO player_quests (xuid, quest_id, progress, completed, claimed) 
            VALUES (:xuid, :qid, 1, 1, 0)
            ON CONFLICT(xuid, quest_id) DO UPDATE SET completed = 1");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $stmt->bindValue(":qid", $questId, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function claimReward(Player $player, int $chapter) : void{
        $xuid = $player->getXuid();
        $qid = "chap_" . $chapter;
        $q = $this->getQuestProgress($xuid, $qid);

        if((int)$q["claimed"] === 1){
            $player->sendMessage("§c❌ Vous avez déjà récupéré cette récompense !");
            return;
        }

        $reward = $this->chapters[$chapter]["reward_money"] ?? 1000;
        $this->economy->addMoney($xuid, $reward);

        $db = $this->database->getDb();
        $stmt = $db->prepare("UPDATE player_quests SET claimed = 1 WHERE xuid = :xuid AND quest_id = :qid");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $stmt->bindValue(":qid", $qid, SQLITE3_TEXT);
        $stmt->execute();

        $player->sendTitle("§6⭐ QUÊTE ACCOMPLIE ⭐", "§a+{$reward} Or Céleste !", 10, 40, 10);
    }

    public function openQuestsMenu(Player $player) : void{
        $xuid = $player->getXuid();

        $form = new SimpleForm(function(Player $player, $data) : void{
            if($data === null) return;
            $chapter = $data + 1;
            if(isset($this->chapters[$chapter])){
                $this->openChapterDetail($player, $chapter);
            }
        });

        $form->setTitle("§l§d📜 CARNET DE QUÊTES SCÉNARISÉES");
        $form->setContent("§7Accomplissez les chapitres de la Voie Initiatique pour obtenir des trésors divins :");

        foreach($this->chapters as $num => $info){
            $q = $this->getQuestProgress($xuid, "chap_" . $num);
            $status = ((int)$q["claimed"] === 1) ? "§a[COMPLÉTÉ]" : (((int)$q["completed"] === 1) ? "§e[À RÉCLAMER]" : "§7[EN COURS]");
            $form->addButton($info["title"] . "\n" . $status);
        }

        FormManager::getInstance()->sendForm($player, $form);
    }

    private function openChapterDetail(Player $player, int $chapter) : void{
        $xuid = $player->getXuid();
        $info = $this->chapters[$chapter];
        $q = $this->getQuestProgress($xuid, "chap_" . $chapter);

        $form = new SimpleForm(function(Player $player, $data) use ($chapter, $q) : void{
            if($data === 0){
                if((int)$q["completed"] === 1 && (int)$q["claimed"] === 0){
                    $this->claimReward($player, $chapter);
                }else{
                    $this->openQuestsMenu($player);
                }
            }else{
                $this->openQuestsMenu($player);
            }
        });

        $form->setTitle($info["title"]);
        $text = "§6Objectif : §f" . $info["desc"] . "\n\n" .
            "§6Récompense : §e" . number_format($info["reward_money"]) . " Or Céleste\n\n" .
            "§7État : " . ((int)$q["claimed"] === 1 ? "§aRécompense déjà perçue" : ((int)$q["completed"] === 1 ? "§ePrêt à être validé !" : "§cObjectif non encore validé"));

        $form->setContent($text);
        if((int)$q["completed"] === 1 && (int)$q["claimed"] === 0){
            $form->addButton("§a🎁 RÉCLAMER LA RÉCOMPENSE");
        }else{
            $form->addButton("§aValider (Si complété)");
        }
        $form->addButton("§cRetour");

        FormManager::getInstance()->sendForm($player, $form);
    }
}
