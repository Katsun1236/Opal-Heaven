<?php

declare(strict_types=1);

namespace opal\core\ah;

use opal\core\database\DatabaseManager;
use opal\core\economy\EconomyManager;
use opal\core\ui\CustomForm;
use opal\core\ui\FormManager;
use opal\core\ui\SimpleForm;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
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
        return self::$instance ?? throw new \RuntimeException("AuctionHouseManager has not been initialized yet.");
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
        if($stmt === false){
            $player->sendMessage("§c❌ Erreur lors du chargement des annonces.");
            return;
        }

        $stmt->bindValue(":cat", $category, SQLITE3_TEXT);
        $stmt->bindValue(":now", time(), SQLITE3_INTEGER);
        $res = $stmt->execute();

        $listings = [];
        if($res !== false){
            while($row = $res->fetchArray(SQLITE3_ASSOC)){
                $listings[] = $row;
            }
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
                $form->addButton("§f" . $item["item_name"] . "\n§6" . number_format((int)$item["price"]) . " Or §8- §e" . $item["seller_name"]);
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

        // Clone figé de l'item à vendre au moment de l'ouverture
        $targetItem = clone $item;

        $form = new CustomForm(function(Player $player, $data) use ($targetItem) : void{
            if($data === null || !isset($data[1])) return;
            $catIndex = (int)$data[0];

            if(!is_numeric($data[1])){
                $player->sendMessage("§c❌ Le prix doit être un nombre valide !");
                return;
            }

            $price = (int)$data[1];
            if($price < 10 || $price > 1_000_000_000){
                $player->sendMessage("§c❌ Le prix de vente doit être compris entre 10 et 1 000 000 000 Or !");
                return;
            }

            // ANTI-DUPE : Vérifier que le joueur tient TOUJOURS exactement le même objet dans sa main
            $currentItem = $player->getInventory()->getItemInHand();
            if($currentItem->isNull() || !$currentItem->equalsExact($targetItem) || $currentItem->getCount() < $targetItem->getCount()){
                $player->sendMessage("§c❌ L'objet dans votre main a été modifié, déplacé ou retiré ! Mise en vente annulée.");
                return;
            }

            $categories = ["ARMES", "MINERAIS", "AGRICULTURE", "PARCHEMINS", "BLOCS"];
            $cat = $categories[$catIndex] ?? "ARMES";

            $fee = (int)ceil($price * 0.02);
            if(!$this->economy->removeMoney($player, $fee)){
                $player->sendMessage("§c❌ Vous n'avez pas assez d'or pour payer les frais de mise en vente (2% = {$fee} Or) !");
                return;
            }

            // Sérialisation NBT PocketMine standard
            $serializer = new \pocketmine\nbt\LittleEndianNbtSerializer();
            $nbtData = base64_encode($serializer->write(new \pocketmine\nbt\TreeRoot($targetItem->nbtSerialize())));
            $now = time();
            $expires = $now + (48 * 3600);
            $sellerKey = !empty($player->getXuid()) ? $player->getXuid() : strtolower($player->getName());

            $db = $this->database->getDb();
            $stmt = $db->prepare("INSERT INTO ah_listings (seller_xuid, seller_name, item_nbt, item_name, category, price, created_at, expires_at)
                VALUES (:xuid, :name, :nbt, :iname, :cat, :p, :c, :e)");
            if($stmt === false){
                $this->economy->addMoney($player, $fee); // Remboursement en cas d'erreur DB
                $player->sendMessage("§c❌ Erreur base de données lors de la mise en vente.");
                return;
            }

            $stmt->bindValue(":xuid", $sellerKey, SQLITE3_TEXT);
            $stmt->bindValue(":name", $player->getName(), SQLITE3_TEXT);
            $stmt->bindValue(":nbt", $nbtData, SQLITE3_TEXT);
            $stmt->bindValue(":iname", $targetItem->getName() . " x" . $targetItem->getCount(), SQLITE3_TEXT);
            $stmt->bindValue(":cat", $cat, SQLITE3_TEXT);
            $stmt->bindValue(":p", $price, SQLITE3_INTEGER);
            $stmt->bindValue(":c", $now, SQLITE3_INTEGER);
            $stmt->bindValue(":e", $expires, SQLITE3_INTEGER);
            $stmt->execute();

            // Retrait propre de l'item de la main
            $player->getInventory()->setItemInHand(VanillaItems::AIR());
            $player->sendMessage("§a✔ Objet mis en vente pour §e" . number_format($price) . " Or §a(Frais: §c" . number_format($fee) . " Or§a) !");
        });

        $form->setTitle("§6Mettre en vente à l'HDV");
        $form->addDropdown("Catégorie de l'objet :", ["Armes & Outils", "Minerais & Gemmes", "Agriculture", "Parchemins", "Blocs"]);
        $form->addInput("Prix de vente en Or :", "Ex: 2500");

        FormManager::getInstance()->sendForm($player, $form);
    }

    public function buyItem(Player $player, array $listing) : void{
        $id = (int)$listing["id"];
        $price = (int)$listing["price"];
        $buyerKey = !empty($player->getXuid()) ? $player->getXuid() : strtolower($player->getName());

        if($buyerKey === $listing["seller_xuid"]){
            $player->sendMessage("§c❌ Vous ne pouvez pas acheter votre propre offre !");
            return;
        }

        $db = $this->database->getDb();

        // 1. Vérification préalable de disponibilité
        $checkStmt = $db->prepare("SELECT * FROM ah_listings WHERE id = :id AND is_sold = 0 AND expires_at > :now");
        if($checkStmt === false){
            $player->sendMessage("§c❌ Erreur interne lors de la vérification de l'offre.");
            return;
        }
        $checkStmt->bindValue(":id", $id, SQLITE3_INTEGER);
        $checkStmt->bindValue(":now", time(), SQLITE3_INTEGER);
        $resCheck = $checkStmt->execute();
        $freshListing = $resCheck !== false ? $resCheck->fetchArray(SQLITE3_ASSOC) : false;
        if($freshListing === false){
            $player->sendMessage("§c❌ Cette offre n'est plus disponible ou a déjà été vendue !");
            return;
        }

        // 2. Débit de l'acheteur
        if(!$this->economy->removeMoney($player, $price)){
            $player->sendMessage("§c❌ Solde insuffisant ! (Prix: " . number_format($price) . " Or)");
            return;
        }

        // 3. Verrouillage atomique de la vente
        $updateStmt = $db->prepare("UPDATE ah_listings SET is_sold = 1 WHERE id = :id AND is_sold = 0");
        if($updateStmt === false){
            $this->economy->addMoney($player, $price); // Remboursement
            $player->sendMessage("§c❌ Erreur interne.");
            return;
        }
        $updateStmt->bindValue(":id", $id, SQLITE3_INTEGER);
        $updateStmt->execute();

        if($db->changes() === 0){
            // Vente concurrente simultanée : quelqu'un vient de l'acheter
            $this->economy->addMoney($player, $price); // Remboursement immédiat
            $player->sendMessage("§c❌ Cette offre vient tout juste d'être achetée par un autre joueur !");
            return;
        }

        // 4. Désérialisation NBT sécurisée (sans unserialize)
        try{
            $decoded = base64_decode($listing["item_nbt"], true);
            if($decoded === false){
                throw new \RuntimeException("Données NBT invalides");
            }
            $tag = (new \pocketmine\nbt\LittleEndianNbtSerializer())->read($decoded)->mustGetCompoundTag();
            $item = Item::nbtDeserialize($tag);
        }catch(\Throwable){
            // Échec critique : remboursement de l'acheteur et annulation
            $rollbackStmt = $db->prepare("UPDATE ah_listings SET is_sold = 0 WHERE id = :id");
            if($rollbackStmt !== false){
                $rollbackStmt->bindValue(":id", $id, SQLITE3_INTEGER);
                $rollbackStmt->execute();
            }
            $this->economy->addMoney($player, $price);
            $player->sendMessage("§c❌ Erreur lors du décodage de l'objet (NBT invalide). Achat annulé et or remboursé.");
            return;
        }

        // 5. Crédit du vendeur
        $this->economy->addMoney($listing["seller_xuid"], $price);
        $sellerPlayer = $player->getServer()->getPlayerExact($listing["seller_name"]);
        if($sellerPlayer !== null && $sellerPlayer->isOnline()){
            $sellerPlayer->sendMessage("§a💰 Votre offre pour §e" . $listing["item_name"] . " §aa été achetée par §e" . $player->getName() . " §apour §6" . number_format($price) . " Or §a!");
        }

        // 6. Donner l'item au joueur (gestion sécurisée de l'inventaire plein)
        $leftovers = $player->getInventory()->addItem($item);
        foreach($leftovers as $leftover){
            $player->getWorld()->dropItem($player->getPosition(), $leftover);
        }
        if(!empty($leftovers)){
            $player->sendMessage("§e⚠ Votre inventaire était plein, certains objets sont tombés au sol !");
        }

        $player->sendMessage("§a✔ Achat réussi de §e" . $listing["item_name"] . " §apour §6" . number_format($price) . " Or §a!");
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
