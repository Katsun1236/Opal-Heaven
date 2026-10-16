<?php

declare(strict_types=1);

namespace opal\core\ah;

use opal\core\database\DatabaseManager;
use opal\core\economy\EconomyManager;
use opal\core\ui\CustomForm;
use opal\core\ui\FormManager;
use opal\core\ui\SimpleForm;
use pocketmine\item\Item;
use pocketmine\player\Player;

class AuctionHouseManager{

    private static ?self $instance = null;
    private DatabaseManager $database;
    private EconomyManager $economy;

    public function __construct(DatabaseManager $database, EconomyManager $economy){
        self::$instance = $this;
        $this->database = $database;
        $this->economy = $economy;
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function openMainMenu(Player $player) : void{
        $form = new SimpleForm(function(Player $player, $data) : void{
            if($data === null) return;
            switch($data){
                case 0: $this->openCategoryMenu($player, "ARMES"); break;
                case 1: $this->openCategoryMenu($player, "MINERAIS"); break;
                case 2: $this->openCategoryMenu($player, "AGRICULTURE"); break;
                case 3: $this->openCategoryMenu($player, "PARCHEMINS"); break;
                case 4: $this->openCategoryMenu($player, "BLOCS"); break;
                case 5: $this->openSellMenu($player); break;
                case 6: $this->openBuyOrdersMenu($player); break;
            }
        });

        $form->setTitle("§l§d🏛 HÔTEL DES VENTES CÉLESTE");
        $form->setContent("§7Consultez les offres du marché ou mettez en vente vos précieux butins.\n§eFrais de dépôt : §f2% §8| §eDurée : §f48 heures");

        $form->addButton("🗡️ Armes, Armures & Outils");
        $form->addButton("⛏️ Minerais, Lingots & Gemmes");
        $form->addButton("🌾 Agriculture, Semences & Festins");
        $form->addButton("📜 Parchemins & Livres d'Arcanes");
        $form->addButton("📦 Matériaux & Blocs Fortifiés");
        $form->addButton("§6💰 Mettre en Vente un Objet");
        $form->addButton("§b📈 Ordres d'Achat Boursiers");

        FormManager::getInstance()->sendForm($player, $form);
    }

    public function openCategoryMenu(Player $player, string $category) : void{
        $db = $this->database->getDb();
        $stmt = $db->prepare("SELECT * FROM ah_listings WHERE category = :cat AND is_sold = 0 AND expires_at > :now ORDER BY id DESC LIMIT 20");
        $stmt->bindValue(":cat", $category, SQLITE3_TEXT);
        $stmt->bindValue(":now", time(), SQLITE3_INTEGER);
        $res = $stmt->execute();

        $listings = [];
        while($row = $res->fetchArray(SQLITE3_ASSOC)){
            $listings[] = $row;
        }

        $form = new SimpleForm(function(Player $player, $data) use ($listings, $category) : void{
            if($data === null){
                $this->openMainMenu($player);
                return;
            }
            if(isset($listings[$data])){
                $this->buyItem($player, $listings[$data]);
            }
        });

        $form->setTitle("§dHDV — §e" . $category);
        if(empty($listings)){
            $form->setContent("§cAucun objet en vente dans cette catégorie pour le moment.");
        }else{
            $form->setContent("§7Cliquez sur un objet pour l'acheter directement :");
            foreach($listings as $item){
                $form->addButton("§f" . $item["item_name"] . "\n§6" . number_format($item["price"]) . " Or §8- §e" . $item["seller_name"]);
            }
        }

        FormManager::getInstance()->sendForm($player, $form);
    }

    public function openSellMenu(Player $player) : void{
        $item = $player->getInventory()->getItemInHand();
        if($item->isNull()){
            $player->sendMessage("§c❌ Tenez l'objet à vendre dans votre main !");
            return;
        }

        $form = new CustomForm(function(Player $player, $data) use ($item) : void{
            if($data === null || !isset($data[1])) return;
            $catIndex = (int)$data[0];
            $price = (int)$data[1];

            $categories = ["ARMES", "MINERAIS", "AGRICULTURE", "PARCHEMINS", "BLOCS"];
            $cat = $categories[$catIndex] ?? "ARMES";

            if($price < 10){
                $player->sendMessage("§c❌ Le prix minimum est de 10 Or !");
                return;
            }

            $fee = (int)ceil($price * 0.02);
            if(!$this->economy->removeMoney($player, $fee)){
                $player->sendMessage("§c❌ Vous n'avez pas assez d'or pour payer les frais de mise en vente (2% = {$fee} Or) !");
                return;
            }

            // Sérialisation NBT PocketMine standard
            $serializer = new \pocketmine\nbt\LittleEndianNbtSerializer();
            $nbtData = base64_encode($serializer->write(new \pocketmine\nbt\TreeRoot($item->nbtSerialize())));
            $now = time();
            $expires = $now + (48 * 3600);
            $sellerKey = !empty($player->getXuid()) ? $player->getXuid() : strtolower($player->getName());

            $db = $this->database->getDb();
            $stmt = $db->prepare("INSERT INTO ah_listings (seller_xuid, seller_name, item_nbt, item_name, category, price, created_at, expires_at)
                VALUES (:xuid, :name, :nbt, :iname, :cat, :p, :c, :e)");
            $stmt->bindValue(":xuid", $sellerKey, SQLITE3_TEXT);
            $stmt->bindValue(":name", $player->getName(), SQLITE3_TEXT);
            $stmt->bindValue(":nbt", $nbtData, SQLITE3_TEXT);
            $stmt->bindValue(":iname", $item->getName() . " x" . $item->getCount(), SQLITE3_TEXT);
            $stmt->bindValue(":cat", $cat, SQLITE3_TEXT);
            $stmt->bindValue(":p", $price, SQLITE3_INTEGER);
            $stmt->bindValue(":c", $now, SQLITE3_INTEGER);
            $stmt->bindValue(":e", $expires, SQLITE3_INTEGER);
            $stmt->execute();

            $player->getInventory()->setItemInHand($player->getInventory()->getItemInHand()->setCount(0));
            $player->sendMessage("§a✔ Objet mis en vente pour §e{$price} Or §a(Frais: §c{$fee} Or§a) !");
        });

        $form->setTitle("§6Mettre en vente à l'HDV");
        $form->addDropdown("Catégorie de l'objet :", ["Armes & Outils", "Minerais & Gemmes", "Agriculture", "Parchemins", "Blocs"]);
        $form->addInput("Prix de vente en Or :", "Ex: 2500");

        FormManager::getInstance()->sendForm($player, $form);
    }

    public function buyItem(Player $player, array $listing) : void{
        $price = (int)$listing["price"];
        $buyerKey = !empty($player->getXuid()) ? $player->getXuid() : strtolower($player->getName());

        if($buyerKey === $listing["seller_xuid"]){
            $player->sendMessage("§c❌ Vous ne pouvez pas acheter votre propre offre !");
            return;
        }

        if(!$this->economy->removeMoney($player, $price)){
            $player->sendMessage("§c❌ Solde insuffisant ! (Prix: {$price} Or)");
            return;
        }

        $db = $this->database->getDb();
        $id = $listing["id"];
        $db->exec("UPDATE ah_listings SET is_sold = 1 WHERE id = $id");

        // Verser l'argent au vendeur
        $this->economy->addMoney($listing["seller_xuid"], $price);

        // Donner l'item
        try{
            $tag = (new \pocketmine\nbt\LittleEndianNbtSerializer())->read(base64_decode($listing["item_nbt"]))->mustGetCompoundTag();
            $item = Item::nbtDeserialize($tag);
        }catch(\Throwable){
            $item = @unserialize(base64_decode($listing["item_nbt"]));
        }

        if($item instanceof Item){
            $player->getInventory()->addItem($item);
        }

        $player->sendMessage("§a✔ Achat réussi de §e" . $listing["item_name"] . " §apour §6{$price} Or §a!");
    }

    public function openBuyOrdersMenu(Player $player) : void{
        $form = new SimpleForm(function(Player $player, $data) : void{
            // Fermer
        });
        $form->setTitle("§b📈 Ordres d'Achat Boursiers");
        $form->setContent("§7Les ordres d'achat permettent de réserver des ressources automatiquement.\n§eStatut : Bourse active.");
        $form->addButton("§aFermer");
        FormManager::getInstance()->sendForm($player, $form);
    }
}
