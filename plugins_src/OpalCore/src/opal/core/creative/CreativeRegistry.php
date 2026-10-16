<?php

declare(strict_types=1);

namespace opal\core\creative;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\entity\Zombie;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\inventory\CreativeCategory;
use pocketmine\inventory\CreativeGroup;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\player\Player;
use opal\core\ui\FormManager;
use opal\core\ui\SimpleForm;

class CreativeRegistry implements Listener{

    private static ?self $instance = null;

    /** @var Item[] */
    private array $customBlocks = [];

    /** @var Item[] */
    private array $customMobs = [];

    /** @var Item[] */
    private array $customItems = [];

    public function __construct(){
        self::$instance = $this;
        $this->initOpalBlocks();
        $this->initOpalMobs();
        $this->initOpalTacticalItems();
        $this->registerToBedrockCreativeMenu();
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    private function initOpalBlocks() : void{
        // 1. Minerais & Matériaux Célestes (sans modèles 3D -> Barrière Block)
        $this->customBlocks["cobalt_ore"] = $this->createUnimplementedBlock(
            "Minerai de Cobalt",
            ["Niveau T2 - Outil requis: Pioche Acier+", "Génère dans les failles rocheuses"],
            "opal:cobalt_ore"
        );

        $this->customBlocks["titanium_ore"] = $this->createUnimplementedBlock(
            "Minerai de Titane",
            ["Niveau T3 - Outil requis: Pioche Titane+", "Génère sous y = 0"],
            "opal:titanium_ore"
        );

        $this->customBlocks["aetherite_ore"] = $this->createUnimplementedBlock(
            "Minerai d'Aetherite",
            ["Niveau T4 - Outil requis: Pioche Aetherite+", "Génère dans les îles célestes (y > 180)"],
            "opal:aetherite_ore"
        );

        $this->customBlocks["celestial_opal_ore"] = $this->createUnimplementedBlock(
            "Minerai d'Opale Céleste",
            ["Légendaire T5 - Outil requis: Pioche d'Opale", "Minerai d'élite ultra-rare"],
            "opal:celestial_opal_ore"
        );

        $this->customBlocks["stellar_amethyst"] = $this->createUnimplementedBlock(
            "Amas d'Améthyste Stellaire",
            ["Cristal résonnant indispensable sous la terre fertile", "pour la pousse de l'Opale"],
            "opal:stellar_amethyst"
        );

        $this->customBlocks["block_cobalt"] = $this->createUnimplementedBlock(
            "Bloc de Cobalt",
            ["Bloc de construction dense T2"],
            "opal:block_cobalt"
        );

        $this->customBlocks["block_titanium"] = $this->createUnimplementedBlock(
            "Bloc de Titane",
            ["Blindage pour coffres de faction T3"],
            "opal:block_titanium"
        );

        $this->customBlocks["block_pure_opal"] = $this->createUnimplementedBlock(
            "Bloc d'Opale Pure",
            ["Bloc de prestige céleste ultime", "Émet une aura stellaire continue"],
            "opal:block_pure_opal"
        );

        // 2. Machinerie & Fortifications
        $this->customBlocks["acceleration_composter"] = $this->createUnimplementedBlock(
            "Composteur d'Accélération",
            ["Transforme 64 déchets végétaux en Poussière Fertile"],
            "opal:acceleration_composter"
        );

        $this->customBlocks["faction_heart"] = $this->createUnimplementedBlock(
            "Cœur de Faction (Nexus)",
            ["1 000 Points de Structure", "Protège le claim et active le bouclier stellaire"],
            "opal:faction_heart"
        );

        $this->customBlocks["fortified_stone"] = $this->createUnimplementedBlock(
            "Pierre Fortifiée",
            ["Résiste à 3 détonations de TNT directes"],
            "opal:fortified_stone"
        );

        $this->customBlocks["dense_void_obsidian"] = $this->createUnimplementedBlock(
            "Obsidienne Dense de Faille",
            ["Résiste à 5 détonations de TNT directes"],
            "opal:dense_void_obsidian"
        );

        $this->customBlocks["ground_spikes"] = $this->createUnimplementedBlock(
            "Piques d'Acier au Sol",
            ["Inflige 3 cœurs de dégâts directs et saignement aux intrus"],
            "opal:ground_spikes"
        );

        $this->customBlocks["shock_plate"] = $this->createUnimplementedBlock(
            "Dalle d'Électrocution",
            ["Augmente la jauge de posture de l'intrus de +40%"],
            "opal:shock_plate"
        );

        $this->customBlocks["ender_disruptor"] = $this->createUnimplementedBlock(
            "Balise Anti-Perles (Ender Disruptor)",
            ["Annule toutes les perles d'ender dans un rayon de 16 blocs"],
            "opal:ender_disruptor"
        );
    }

    private function initOpalMobs() : void{
        // 1. Les Boss Majeurs
        $this->customMobs["boss_vampire"] = $this->createCustomItem(
            VanillaItems::NETHER_STAR(),
            "§l§c👑 Œuf de Boss : Seigneur Sanglant",
            ["§4Boss de Donjon — 500 HP", "§cAttaque circulaire 360° et Dash d'Ombre", "§eNécessite la parade au sneak !"],
            "opal:boss_vampire_lord"
        );

        $this->customMobs["boss_monarch"] = $this->createCustomItem(
            VanillaItems::NETHER_STAR(),
            "§l§5👑 Œuf de Boss : Monarque Déchu",
            ["§5Boss Primordial — 750 HP", "§dMagie des failles célestes"],
            "opal:boss_fallen_monarch"
        );

        $this->customMobs["boss_golem"] = $this->createCustomItem(
            VanillaItems::NETHER_STAR(),
            "§l§6👑 Œuf de Boss : Golem de Basalte",
            ["§6Boss de la Forge — 800 HP", "§eÉcrasement tellurique et éruptions de lave"],
            "opal:boss_basalt_golem"
        );

        $this->customMobs["boss_queen"] = $this->createCustomItem(
            VanillaItems::NETHER_STAR(),
            "§l§a👑 Œuf de Boss : Reine des Ronces",
            ["§aBoss de la Canopée — 650 HP", "§2Prison végétale et salves d'épines"],
            "opal:boss_bramble_queen"
        );

        $this->customMobs["boss_leviathan"] = $this->createCustomItem(
            VanillaItems::HEART_OF_THE_SEA(),
            "§l§b👑 Œuf de Boss : Léviathan des Abysses",
            ["§bBoss Aquatique Légendaire — 350 HP", "§3Déclenché à la pêche abyssale T4"],
            "opal:abyssal_leviathan"
        );

        // 2. Les 20 Monstres du Bestiaire
        $mobs = [
            1 => ["Scarabée du Sable", "opal:mob_scarabee_sable"],
            2 => ["Serpent des Braises", "opal:mob_serpent_braises"],
            3 => ["Scorpion de Cristal", "opal:mob_scorpion_cristal"],
            4 => ["Golem Gréseux", "opal:mob_golem_greseux"],
            5 => ["Guerrier des Cendres", "opal:mob_guerrier_cendres"],
            6 => ["Momie aux Vipères", "opal:mob_momie_viperes"],
            7 => ["Esprit du Mirage", "opal:mob_esprit_mirage"],
            8 => ["Gargouille de Basalte", "opal:mob_gargouille_basalte"],
            9 => ["Spectre des Failles", "opal:mob_spectre_failles"],
            10 => ["Élémentaliste de Flammes", "opal:mob_elementaliste_flammes"],
            11 => ["Garde du Pharaon", "opal:mob_garde_pharaon"],
            12 => ["Titan de Magma", "opal:mob_titan_magma"],
            13 => ["Ombre du Sable", "opal:mob_ombre_sable"],
            14 => ["Nomade Corrompu", "opal:mob_nomade_corrompu"],
            15 => ["Dromadaire Squelette", "opal:mob_dromadaire_squelette"],
            16 => ["Chacal d'Anubis", "opal:mob_chacal_anubis"],
            17 => ["Colosse des Pyramides", "opal:mob_colosse_pyramides"],
            18 => ["Seigneur des Dunes", "opal:mob_seigneur_dunes"],
            19 => ["Hydre de Lave", "opal:mob_hydre_lave"],
            20 => ["Dragon d'Obsidienne", "opal:mob_dragon_obsidienne"]
        ];

        foreach($mobs as $num => [$name, $id]){
            $this->customMobs["mob_" . $num] = $this->createCustomItem(
                VanillaItems::FIREWORK_STAR(),
                "§e🥚 Œuf de Monstre : " . $name,
                ["§7Monstre d'Élite d'Opal Heaven", "§8ID: " . $id],
                $id
            );
        }

        // 3. Mannequin d'entraînement
        $this->customMobs["combat_dummy"] = $this->createCustomItem(
            VanillaItems::VILLAGER_SPAWN_EGG(),
            "§l§6🥋 Mannequin de Parade Céleste",
            ["§7Automate d'entraînement à la parade", "§ePermet de s'entraîner au timing 9 ticks"],
            "opal:combat_dummy"
        );
    }

    private function initOpalTacticalItems() : void{
        $this->customItems["stick_heal"] = $this->createCustomItem(
            VanillaItems::BLAZE_ROD(),
            "§l§a💖 Bâton de Guérison",
            ["§73 Charges — Cooldown 45s", "§aClic droit: Restaure 4 cœurs de vie"],
            "opal:stick_heal"
        );

        $this->customItems["stick_speed"] = $this->createCustomItem(
            VanillaItems::FEATHER(),
            "§l§b⚡ Bâton de Vitesse",
            ["§75 Charges — Cooldown 30s", "§bClic droit: Octroie Vitesse II pendant 15s"],
            "opal:stick_speed"
        );

        $this->customItems["stick_force"] = $this->createCustomItem(
            VanillaItems::STICK(),
            "§l§6💥 Bâton de Répulsion",
            ["§73 Charges — Cooldown 30s", "§6Clic droit: Onde de choc repoussant les assaillants à 6 blocs"],
            "opal:stick_force"
        );

        $this->customItems["stick_breach"] = $this->createCustomItem(
            VanillaItems::IRON_SWORD(),
            "§l§c💣 Bâton de Brèche (Siège)",
            ["§72 Charges — Utilisable entre 18h et 23h", "§cDésactive les défenses et tourelles ennemies"],
            "opal:stick_breach"
        );
    }

    private function registerToBedrockCreativeMenu() : void{
        $ci = CreativeInventory::getInstance();

        // Groupe Principal unifié "OPAL HEAVEN"
        $groupOpal = new CreativeGroup(
            new Translatable("§l§d🌌 OPAL HEAVEN — Tous les Ajouts"),
            VanillaItems::NETHER_STAR()
        );

        // Blocs (Barrières non disponibles)
        foreach($this->customBlocks as $item){
            $ci->add($item, CreativeCategory::ITEMS, $groupOpal);
        }

        // Mobs & Boss
        foreach($this->customMobs as $item){
            $ci->add($item, CreativeCategory::ITEMS, $groupOpal);
        }

        // Bâtons Tactiques
        foreach($this->customItems as $item){
            $ci->add($item, CreativeCategory::ITEMS, $groupOpal);
        }
    }

    public function onInventoryTransaction(InventoryTransactionEvent $event) : void{
        $transaction = $event->getTransaction();
        $player = $transaction->getSource();

        foreach($transaction->getActions() as $action){
            $source = $action->getSourceItem();
            $target = $action->getTargetItem();

            foreach([$source, $target] as $it){
                if($it->getNamedTag()->getTag("opal_unimplemented") !== null){
                    $event->cancel();
                    $name = $it->getNamedTag()->getString("opal_clean_name", "Bloc");
                    $player->sendMessage("§c❌ [{$name}] : Cet élément n'a pas encore de modèle 3D et n'est pas encore fait !");
                    $player->sendTip("§c❌ {$name} — Pas encore fait !");
                    return;
                }
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event) : void{
        $item = $event->getItem();
        if($item->getNamedTag()->getTag("opal_unimplemented") !== null){
            $event->cancel();
            $name = $item->getNamedTag()->getString("opal_clean_name", "Bloc");
            $player = $event->getPlayer();
            $player->sendMessage("§c❌ [{$name}] : Cet élément n'a pas encore de modèle 3D et n'est pas encore fait !");
            $player->sendTip("§c❌ {$name} — Pas encore fait !");
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event) : void{
        $item = $event->getItem();
        if($item->getNamedTag()->getTag("opal_unimplemented") !== null){
            $event->cancel();
            $name = $item->getNamedTag()->getString("opal_clean_name", "Bloc");
            $player = $event->getPlayer();
            $player->sendMessage("§c❌ [{$name}] : Cet élément n'a pas encore de modèle 3D et n'est pas encore fait !");
            $player->sendTip("§c❌ {$name} — Pas encore fait !");
        }
    }

    public function onUse(PlayerItemUseEvent $event) : void{
        $player = $event->getPlayer();
        $item = $event->getItem();
        $tag = $item->getNamedTag();

        if($tag->getTag("opal_unimplemented") !== null){
            $event->cancel();
            $name = $tag->getString("opal_clean_name", $item->getCustomName());
            $player->sendMessage("§c❌ [{$name}] : Cet élément n'a pas encore de modèle 3D et n'est pas encore fait !");
            $player->sendTip("§c❌ {$name} — Pas encore fait !");
            return;
        }

        if($tag->getTag("opal_id") === null){
            return;
        }

        $opalId = $tag->getString("opal_id");

        // Invocation d'un Mob ou Boss
        if(str_starts_with($opalId, "opal:boss_") || str_starts_with($opalId, "opal:mob_")){
            $event->cancel();
            $pos = $player->getPosition()->addVector($player->getDirectionVector()->multiply(3));

            // Sons et effets visuels d'invocation
            $pk = PlaySoundPacket::create("beacon.activate", $pos->x, $pos->y, $pos->z, 1.0, 1.5);
            $player->getNetworkSession()->sendDataPacket($pk);

            $partPk = SpawnParticleEffectPacket::create(0, -1, $pos->add(0, 1, 0), "minecraft:sparkler_emitter", null);
            $player->getNetworkSession()->sendDataPacket($partPk);

            $mobName = $item->getCustomName();
            $player->sendTitle("§6⭐ INVOCATION CÉLESTE ⭐", "§f{$mobName} §aest apparu !", 5, 30, 5);

            // Apparition de l'entité en jeu
            $this->spawnBossEntity($player, $pos, $mobName, $opalId);
        }
    }

    private function spawnBossEntity(Player $player, \pocketmine\world\Position $pos, string $name, string $opalId) : void{
        try{
            $location = Location::fromObject($pos, $player->getWorld(), $player->getLocation()->getYaw(), $player->getLocation()->getPitch());
            $entity = new Zombie($location);
            $entity->setNameTag($name);
            $entity->setNameTagAlwaysVisible(true);
            $entity->setMaxHealth(300);
            $entity->setHealth(300);
            $entity->spawnToAll();
        }catch(\Throwable $e){
            $player->getServer()->getLogger()->warning("[CreativeRegistry] Impossible de faire apparaître le boss : " . $e->getMessage());
        }
    }

    public function openCreativeGUI(Player $player) : void{
        $form = new SimpleForm(function(Player $player, $data) : void{
            if($data === null) return;
            switch($data){
                case 0: $this->openItemsSubMenu($player, "Blocs & Minerais", $this->customBlocks); break;
                case 1: $this->openItemsSubMenu($player, "Boss & Mobs", $this->customMobs); break;
                case 2: $this->openItemsSubMenu($player, "Bâtons & Tactiques", $this->customItems); break;
            }
        });

        $form->setTitle("§l§d🎨 MENU CRÉATIF OPAL HEAVEN");
        $form->setContent("§7Sélectionnez une catégorie pour récupérer vos éléments moddés (les blocs sans modèle 3D sont verrouillés) :");
        $form->addButton("§b💎 Blocs & Fortifications\n§8[" . count($this->customBlocks) . " blocs - Verrouillés]");
        $form->addButton("§c👑 Mobs & Boss\n§8[" . count($this->customMobs) . " entités]");
        $form->addButton("§6⚡ Bâtons Tactiques & Outils\n§8[" . count($this->customItems) . " items]");

        FormManager::getInstance()->sendForm($player, $form);
    }

    private function openItemsSubMenu(Player $player, string $category, array $items) : void{
        $keys = array_values($items);
        $form = new SimpleForm(function(Player $player, $data) use ($keys) : void{
            if($data === null){
                $this->openCreativeGUI($player);
                return;
            }
            if(isset($keys[$data])){
                $chosen = clone $keys[$data];
                if($chosen->getNamedTag()->getTag("opal_unimplemented") !== null){
                    $name = $chosen->getNamedTag()->getString("opal_clean_name", $chosen->getCustomName());
                    $player->sendMessage("§c❌ [{$name}] : Cet élément n'a pas encore de modèle 3D et n'est pas encore fait !");
                    $player->sendTip("§c❌ {$name} — Pas encore fait !");
                    return;
                }
                $chosen->setCount($chosen->getMaxStackSize());
                $player->getInventory()->addItem($chosen);
                $player->sendMessage("§a✔ Vous avez reçu : " . $chosen->getCustomName() . " x" . $chosen->getCount());
            }
        });

        $form->setTitle("§d" . $category);
        $form->setContent("§7Cliquez sur un élément (les blocs sans modèle 3D ne peuvent pas être pris) :");
        foreach($keys as $it){
            $form->addButton($it->getCustomName());
        }

        FormManager::getInstance()->sendForm($player, $form);
    }

    private function createCustomItem(Item $baseItem, string $name, array $lore, string $opalId) : Item{
        $item = clone $baseItem;
        $item->setCustomName($name);
        $item->setLore($lore);
        $item->getNamedTag()->setString("opal_id", $opalId);
        return $item;
    }

    private function createUnimplementedBlock(string $cleanName, array $description, string $opalId) : Item{
        $item = VanillaBlocks::BARRIER()->asItem();
        $item->setCustomName("§c⛔ " . $cleanName);
        $lore = [
            "§8[Opal Heaven — Non Disponible]",
            "§7Modèle 3D : §cEn cours de création",
            "§ePas encore fait",
            "§7Description :"
        ];
        foreach($description as $line){
            $lore[] = "§8• " . $line;
        }
        $lore[] = "§c❌ Impossible de récupérer cet item.";
        $item->setLore($lore);
        $item->getNamedTag()->setString("opal_id", $opalId);
        $item->getNamedTag()->setByte("opal_unimplemented", 1);
        $item->getNamedTag()->setString("opal_clean_name", $cleanName);
        return $item;
    }
}
