<?php

declare(strict_types=1);

namespace opal\core\creative;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\entity\Zombie;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
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

    /** @var Item[] */
    private array $titaneEquipment = [];

    /** @var Item[] */
    private array $opaleEquipment = [];

    public function __construct(){
        self::$instance = $this;
        $this->initOpalBlocks();
        $this->initOpalMobs();
        $this->initOpalTacticalItems();
        $this->initTitaneEquipment();
        $this->initOpaleEquipment();
        $this->registerToBedrockCreativeMenu();
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    private function initOpalBlocks() : void{
        // -------------------------------------------------------------
        // 1. BLOCS AVEC MODÈLES 3D & TEXTURES (100% Déverrouillés & Posables)
        // -------------------------------------------------------------
        
        // Opale Céleste
        $this->customBlocks["celestial_opal_ore"] = $this->createImplementedBlock(
            VanillaBlocks::DIAMOND_ORE()->asItem(),
            "§l§d💎 Minerai d'Opale Céleste",
            ["Légendaire T5 — Outil requis: Pioche d'Opale", "Minerai d'élite ultra-rare", "Génère des éclats d'opale pure", "Modèle 3D & Texture intégrés"],
            "opal:celestial_opal_ore"
        );

        $this->customBlocks["deepslate_opal_ore"] = $this->createImplementedBlock(
            VanillaBlocks::DEEPSLATE_DIAMOND_ORE()->asItem(),
            "§l§5💎 Minerai d'Opale des Abîmes",
            ["Niveau T5 — Génère dans le Deepslate sous y = 0", "Matrice de roche dense ultra-résistante", "Modèle 3D & Texture intégrés"],
            "opal:deepslate_opal_ore"
        );

        $this->customBlocks["block_pure_opal"] = $this->createImplementedBlock(
            VanillaBlocks::DIAMOND()->asItem(),
            "§l§d✨ Bloc d'Opale Pure",
            ["Bloc de prestige céleste ultime", "Émet une aura stellaire continue", "Modèle 3D & Texture intégrés"],
            "opal:block_pure_opal"
        );

        $this->customBlocks["opal_bricks"] = $this->createImplementedBlock(
            VanillaBlocks::STONE_BRICKS()->asItem(),
            "§l§b🏛 Briques d'Opale Céleste",
            ["Matériau d'architecture céleste raffiné", "Finition polie aux reflets prismatiques", "Modèle 3D & Texture intégrés"],
            "opal:opal_bricks"
        );

        $this->customBlocks["opal_lamp"] = $this->createImplementedBlock(
            VanillaBlocks::REDSTONE_LAMP()->asItem(),
            "§l§e🏮 Lampe d'Opale Luminescente",
            ["Source de lumière stellaire pure", "Niveau d'éclairage 15 constant", "Modèle 3D & Texture intégrés"],
            "opal:opal_lamp"
        );

        // Argent
        $this->customBlocks["silver_ore"] = $this->createImplementedBlock(
            VanillaBlocks::IRON_ORE()->asItem(),
            "§l§f🪙 Minerai d'Argent",
            ["Niveau T2 — Gisement métallique pur", "Composant de monnaie et d'ingénierie avancée", "Modèle 3D & Texture intégrés"],
            "opal:silver_ore"
        );

        $this->customBlocks["deepslate_silver_ore"] = $this->createImplementedBlock(
            VanillaBlocks::DEEPSLATE_IRON_ORE()->asItem(),
            "§l§8🪙 Minerai d'Argent des Abîmes",
            ["Niveau T2 — Encastré sous y = 0", "Veine dense de grande pureté", "Modèle 3D & Texture intégrés"],
            "opal:deepslate_silver_ore"
        );

        $this->customBlocks["silver_block"] = $this->createImplementedBlock(
            VanillaBlocks::IRON()->asItem(),
            "§l§f◽ Bloc d'Argent Massif",
            ["Bloc de réserve noble et raffiné", "Condense 9 lingots d'argent pur", "Modèle 3D & Texture intégrés"],
            "opal:silver_block"
        );

        // Rubis
        $this->customBlocks["ruby_ore"] = $this->createImplementedBlock(
            VanillaBlocks::REDSTONE_ORE()->asItem(),
            "§l§c🔴 Minerai de Rubis Flamboyant",
            ["Niveau T3 — Gemme magmatique incandescente", "Rayonne au contact des roches volcaniques", "Modèle 3D & Texture intégrés"],
            "opal:ruby_ore"
        );

        $this->customBlocks["deepslate_ruby_ore"] = $this->createImplementedBlock(
            VanillaBlocks::DEEPSLATE_REDSTONE_ORE()->asItem(),
            "§l§4🔴 Minerai de Rubis des Abîmes",
            ["Niveau T3 — Cristallisation sous forte pression", "Génère dans les strates les plus profondes", "Modèle 3D & Texture intégrés"],
            "opal:deepslate_ruby_ore"
        );

        $this->customBlocks["ruby_block"] = $this->createImplementedBlock(
            VanillaBlocks::REDSTONE()->asItem(),
            "§l§c🟥 Bloc de Rubis Massif",
            ["Bloc compact de gemmes incandescentes", "Diffuse une chaleur tellurique pulsante", "Modèle 3D & Texture intégrés"],
            "opal:ruby_block"
        );

        $this->customBlocks["ruby_crystal_cluster"] = $this->createImplementedBlock(
            VanillaBlocks::AMETHYST_CLUSTER()->asItem(),
            "§l§d🔮 Amas de Cristaux Stellaire & Rubis",
            ["Formation géologique résonnante", "Syntonise l'énergie stellaire pour la pousse d'Opale", "Modèle 3D & Texture intégrés"],
            "opal:ruby_crystal_cluster"
        );

        // Titane (Bloc)
        $this->customBlocks["block_titanium"] = $this->createImplementedBlock(
            VanillaBlocks::NETHERITE()->asItem(),
            "§l§7🛡 Bloc de Titane Forgé",
            ["Niveau T3 — Blindage impénétrable", "Idéal pour sécuriser les forteresses et nexus", "Modèle 3D & Texture intégrés"],
            "opal:block_titanium"
        );

        // -------------------------------------------------------------
        // 2. BLOCS NON ENCORE FAITS DANS MES_CREATIONS (Statut Barrière ⛔)
        // -------------------------------------------------------------
        $this->customBlocks["cobalt_ore"] = $this->createUnimplementedBlock(
            "Minerai de Cobalt",
            ["Niveau T2 - Outil requis: Pioche Acier+", "Génère dans les failles rocheuses"],
            "opal:cobalt_ore"
        );

        $this->customBlocks["titanium_ore"] = $this->createUnimplementedBlock(
            "Minerai de Titane Brut",
            ["Niveau T3 - Outil requis: Pioche Titane+", "Génère sous y = 0"],
            "opal:titanium_ore"
        );

        $this->customBlocks["aetherite_ore"] = $this->createUnimplementedBlock(
            "Minerai d'Aetherite",
            ["Niveau T4 - Outil requis: Pioche Aetherite+", "Génère dans les îles célestes (y > 180)"],
            "opal:aetherite_ore"
        );

        $this->customBlocks["block_cobalt"] = $this->createUnimplementedBlock(
            "Bloc de Cobalt",
            ["Bloc de construction dense T2"],
            "opal:block_cobalt"
        );

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
        // -------------------------------------------------------------
        // 1. LES 6 BOSS OFFICIELS D'OPAL HEAVEN (Modèles 3D Dédiés)
        // -------------------------------------------------------------
        $this->customMobs["boss_golem"] = $this->createCustomItem(
            VanillaItems::NETHER_STAR(),
            "§l§6👑 Œuf de Boss : Golem de Basalte",
            ["§6Boss de la Forge Ardente — 800 HP", "§eÉcrasement tellurique et éruptions de magma", "§8Modèle 3D Blockbench : geometry.boss_basalt_golem"],
            "opal:boss_basalt_golem"
        );

        $this->customMobs["boss_hydra"] = $this->createCustomItem(
            VanillaItems::NETHER_STAR(),
            "§l§2👑 Œuf de Boss : Hydre des Cavernes",
            ["§2Boss Abyssal Serpentin — 700 HP", "§aSalves de venin acide corrosif", "§8Modèle 3D Blockbench : geometry.boss_cave_hydra"],
            "opal:boss_cave_hydra"
        );

        $this->customMobs["boss_monarch"] = $this->createCustomItem(
            VanillaItems::NETHER_STAR(),
            "§l§5👑 Œuf de Boss : Roi Déchu",
            ["§5Boss Primordial — 750 HP", "§dMagie des failles stellaires et invocation de spectres", "§8Modèle 3D Blockbench : geometry.boss_fallen_monarch"],
            "opal:boss_fallen_monarch"
        );

        $this->customMobs["boss_vampire"] = $this->createCustomItem(
            VanillaItems::NETHER_STAR(),
            "§l§c👑 Œuf de Boss : Seigneur Vampire",
            ["§4Boss de Donjon — 500 HP", "§cAttaque circulaire 360°, Dash d'Ombre et Drain vital", "§8Modèle 3D Blockbench : geometry.boss_vampire_lord"],
            "opal:boss_vampire_lord"
        );

        $this->customMobs["boss_sentinel"] = $this->createCustomItem(
            VanillaItems::NETHER_STAR(),
            "§l§7👑 Œuf de Boss : Sentinelle de Pierre",
            ["§7Gardien des Sanctuaires — 850 HP", "§fBlindage de quartz et onde de choc sismique", "§8Modèle 3D Blockbench : geometry.boss_stone_sentinel"],
            "opal:boss_stone_sentinel"
        );

        $this->customMobs["boss_behemoth"] = $this->createCustomItem(
            VanillaItems::NETHER_STAR(),
            "§l§e👑 Œuf de Boss : Béhémoth Tellurique",
            ["§eColosse Souterrain — 900 HP", "§6Séisme dévastateur brisant la posture", "§8Modèle 3D Blockbench : geometry.boss_telluric_behemoth"],
            "opal:boss_telluric_behemoth"
        );

        // -------------------------------------------------------------
        // 2. LES 17 MONSTRES DU BESTIAIRE 3D (Modèles Blockbench)
        // -------------------------------------------------------------
        $mobs = [
            "mob_magma_scorpion" => ["Scorpion de Magma", "geometry.mob_magma_scorpion"],
            "mob_crystal_spider" => ["Araignée de Cristal", "geometry.mob_crystal_spider"],
            "mob_opal_crab" => ["Crabe d'Opale", "geometry.mob_opal_crab"],
            "mob_shadow_wolf" => ["Loup des Ombres", "geometry.mob_shadow_wolf"],
            "mob_basalt_gargoyle" => ["Gargouille de Basalte", "geometry.mob_basalt_gargoyle"],
            "mob_tribal_warrior" => ["Guerrier de la Tribu", "geometry.mob_tribal_warrior"],
            "mob_tribal_archer" => ["Archer de la Tribu", "geometry.mob_tribal_archer"],
            "mob_deep_burrower" => ["Fouisseur des Profondeurs", "geometry.mob_deep_burrower"],
            "mob_tribal_shaman" => ["Chaman de la Tribu", "geometry.mob_tribal_shaman"],
            "mob_mine_scarab" => ["Scarabée des Mines", "geometry.mob_mine_scarab"],
            "mob_tribal_berserker" => ["Berserker de la Tribu", "geometry.mob_tribal_berserker"],
            "mob_crypt_bat" => ["Chauve-souris des Cryptes", "geometry.mob_crypt_bat"],
            "mob_prismatic_lizard" => ["Lézard Prismatique", "geometry.mob_prismatic_lizard"],
            "mob_giant_mosquito" => ["Moustique Géant", "geometry.mob_giant_mosquito"],
            "mob_swamp_ghoul" => ["Goule des Marais", "geometry.mob_swamp_ghoul"],
            "mob_ash_spectre" => ["Spectre des Cendres", "geometry.mob_ash_spectre"],
            "mob_ember_crawler" => ["Rampant des Braises", "geometry.mob_ember_crawler"]
        ];

        foreach($mobs as $id => [$name, $geo]){
            $this->customMobs[$id] = $this->createCustomItem(
                VanillaItems::FIREWORK_STAR(),
                "§e🥚 Œuf de Monstre : " . $name,
                ["§7Monstre d'Élite d'Opal Heaven", "§8Modèle 3D: " . $geo, "§bClic droit pour faire apparaître"],
                "opal:" . $id
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

    private function initTitaneEquipment() : void{
        $this->titaneEquipment["casque_en_titane"] = $this->createCustomItem(
            VanillaItems::NETHERITE_HELMET(),
            "§l§7🛡 Casque en Titane Forgé",
            ["§7Protection T3 Supérieure", "§8Modèle 3D Blockbench & Texture intégrés", "§eDéfense accrue contre les coups critiques"],
            "opal:casque_en_titane"
        );
        $this->titaneEquipment["plastron_en_titane"] = $this->createCustomItem(
            VanillaItems::NETHERITE_CHESTPLATE(),
            "§l§7🛡 Plastron en Titane Forgé",
            ["§7Protection T3 Supérieure", "§8Modèle 3D Blockbench & Texture intégrés", "§eRéduction des dégâts d'impact"],
            "opal:plastron_en_titane"
        );
        $this->titaneEquipment["jambieres_en_titane"] = $this->createCustomItem(
            VanillaItems::NETHERITE_LEGGINGS(),
            "§l§7🛡 Jambières en Titane Forgé",
            ["§7Protection T3 Supérieure", "§8Modèle 3D Blockbench & Texture intégrés", "§eStabilité accrue face au recul"],
            "opal:jambieres_en_titane"
        );
        $this->titaneEquipment["bottes_en_titane"] = $this->createCustomItem(
            VanillaItems::NETHERITE_BOOTS(),
            "§l§7🛡 Bottes en Titane Forgé",
            ["§7Protection T3 Supérieure", "§8Modèle 3D Blockbench & Texture intégrés", "§eAmortissement des chutes lourdes"],
            "opal:bottes_en_titane"
        );
        $this->titaneEquipment["epee_en_titane"] = $this->createCustomItem(
            VanillaItems::NETHERITE_SWORD(),
            "§l§7⚔ Épée en Titane Forgé",
            ["§7Attaque +9.0", "§8Modèle 3D Blockbench & Texture intégrés", "§eTranchant lourd capable de briser la garde"],
            "opal:epee_en_titane"
        );
        $this->titaneEquipment["pioche_en_titane"] = $this->createCustomItem(
            VanillaItems::NETHERITE_PICKAXE(),
            "§l§7⛏ Pioche en Titane Forgé",
            ["§7Vitesse de minage T3", "§8Modèle 3D Blockbench & Texture intégrés", "§ePermet d'extraire les minerais d'Opale et Deepslate"],
            "opal:pioche_en_titane"
        );
        $this->titaneEquipment["hache_en_titane"] = $this->createCustomItem(
            VanillaItems::NETHERITE_AXE(),
            "§l§7🪓 Hache en Titane Forgé",
            ["§7Attaque +10.0 — Dégâts destructeurs", "§8Modèle 3D Blockbench & Texture intégrés", "§eDésactive les boucliers pendant 3s"],
            "opal:hache_en_titane"
        );
        $this->titaneEquipment["pelle_en_titane"] = $this->createCustomItem(
            VanillaItems::NETHERITE_SHOVEL(),
            "§l§7⛏ Pelle en Titane Forgé",
            ["§7Excavation ultra-rapide", "§8Modèle 3D Blockbench & Texture intégrés"],
            "opal:pelle_en_titane"
        );
        $this->titaneEquipment["masse_en_titane"] = $this->createCustomItem(
            VanillaItems::NETHERITE_AXE(),
            "§l§7🔨 Masse de Guerre en Titane",
            ["§7Arme de choc lourde", "§8Modèle 3D Blockbench & Texture intégrés", "§eDégâts d'écrasement proportionnels à la hauteur"],
            "opal:masse_en_titane"
        );
        $this->titaneEquipment["bouclier_en_titane"] = $this->createCustomItem(
            VanillaItems::IRON_SWORD(),
            "§l§7🛡 Bouclier Renforcé en Titane",
            ["§7Parade parfaite 9 ticks", "§8Modèle 3D Blockbench & Texture intégrés", "§eAbsorption totale des attaques physiques"],
            "opal:bouclier_en_titane"
        );
    }

    private function initOpaleEquipment() : void{
        $this->opaleEquipment["casque_opale"] = $this->createCustomItem(
            VanillaItems::DIAMOND_HELMET(),
            "§l§d✨ Casque en Opale Céleste",
            ["§dProtection Céleste Ultime T5", "§8Modèle 3D Blockbench & Texture intégrés", "§bVision céleste nocturne constante"],
            "opal:casque_opale"
        );
        $this->opaleEquipment["plastron_opale"] = $this->createCustomItem(
            VanillaItems::DIAMOND_CHESTPLATE(),
            "§l§d✨ Plastron en Opale Céleste",
            ["§dProtection Céleste Ultime T5", "§8Modèle 3D Blockbench & Texture intégrés", "§bAura protectrice réduisant les dégâts magiques de 25%"],
            "opal:plastron_opale"
        );
        $this->opaleEquipment["jambieres_opale"] = $this->createCustomItem(
            VanillaItems::DIAMOND_LEGGINGS(),
            "§l§d✨ Jambières en Opale Céleste",
            ["§dProtection Céleste Ultime T5", "§8Modèle 3D Blockbench & Texture intégrés", "§bMobilité accrue en combat"],
            "opal:jambieres_opale"
        );
        $this->opaleEquipment["bottes_opale"] = $this->createCustomItem(
            VanillaItems::DIAMOND_BOOTS(),
            "§l§d✨ Bottes en Opale Céleste",
            ["§dProtection Céleste Ultime T5", "§8Modèle 3D Blockbench & Texture intégrés", "§bLévitation légère amortissant les chutes"],
            "opal:bottes_opale"
        );
        $this->opaleEquipment["epee_opale"] = $this->createCustomItem(
            VanillaItems::DIAMOND_SWORD(),
            "§l§d✨ Épée Céleste d'Opale",
            ["§dAttaque +11.5", "§8Modèle 3D Blockbench & Texture intégrés", "§bSalves d'énergie céleste scintillante"],
            "opal:epee_opale"
        );
        $this->opaleEquipment["pioche_opale"] = $this->createCustomItem(
            VanillaItems::DIAMOND_PICKAXE(),
            "§l§d✨ Pioche Céleste d'Opale",
            ["§dVitesse de minage instantanée T5", "§8Modèle 3D Blockbench & Texture intégrés", "§bMinage en zone 3x3 actif en sneak"],
            "opal:pioche_opale"
        );
        $this->opaleEquipment["hache_opale"] = $this->createCustomItem(
            VanillaItems::DIAMOND_AXE(),
            "§l§d✨ Hache Céleste d'Opale",
            ["§dAttaque +12.0", "§8Modèle 3D Blockbench & Texture intégrés", "§bFend le bois et les défenses instantanément"],
            "opal:hache_opale"
        );
        $this->opaleEquipment["pelle_opale"] = $this->createCustomItem(
            VanillaItems::DIAMOND_SHOVEL(),
            "§l§d✨ Pelle Céleste d'Opale",
            ["§dExcavation céleste instantanée", "§8Modèle 3D Blockbench & Texture intégrés"],
            "opal:pelle_opale"
        );
        $this->opaleEquipment["faux_opale"] = $this->createCustomItem(
            VanillaItems::DIAMOND_HOE(),
            "§l§d✨ Faux Céleste d'Opale",
            ["§dFauchage de zone 360°", "§8Modèle 3D Blockbench & Texture intégrés", "§bRécolte instantanée des cultures et balayage"],
            "opal:faux_opale"
        );
        $this->opaleEquipment["pavois_opale"] = $this->createCustomItem(
            VanillaItems::DIAMOND_SWORD(),
            "§l§d✨ Pavois Céleste d'Opale",
            ["§dGrand Pavois Céleste T5", "§8Modèle 3D Blockbench & Texture intégrés", "§bRenvoie 30% des dégâts subis à l'attaquant"],
            "opal:pavois_opale"
        );
    }

    private function initOpalTacticalItems() : void{
        $this->customItems["stick_heal"] = $this->createCustomItem(
            VanillaItems::BLAZE_ROD(),
            "§l§a💖 Bâton de Guérison",
            ["§7Charges : §a3/3 §7— Cooldown §e45s", "§aClic droit: Restaure 4 cœurs de vie"],
            "opal:stick_heal",
            3
        );

        $this->customItems["stick_speed"] = $this->createCustomItem(
            VanillaItems::FEATHER(),
            "§l§b⚡ Bâton de Vitesse",
            ["§7Charges : §b5/5 §7— Cooldown §e30s", "§bClic droit: Octroie Vitesse II pendant 15s"],
            "opal:stick_speed",
            5
        );

        $this->customItems["stick_force"] = $this->createCustomItem(
            VanillaItems::STICK(),
            "§l§6💥 Bâton de Répulsion",
            ["§7Charges : §63/3 §7— Cooldown §e30s", "§6Clic droit: Onde de choc repoussant les assaillants à 6 blocs"],
            "opal:stick_force",
            3
        );

        $this->customItems["stick_breach"] = $this->createCustomItem(
            VanillaItems::IRON_SWORD(),
            "§l§c💣 Bâton de Brèche (Siège)",
            ["§7Charges : §c2/2 §7— Fenêtre §e18h - 23h", "§cDésactive les défenses et tourelles ennemies"],
            "opal:stick_breach",
            2
        );
    }

    private function registerToBedrockCreativeMenu() : void{
        $ci = CreativeInventory::getInstance();

        $groupOpal = new CreativeGroup(
            new Translatable("§l§d🌌 OPAL HEAVEN — Tous les Ajouts"),
            VanillaItems::NETHER_STAR()
        );

        // Blocs & Minerais
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

        // Équipement Titane 3D
        foreach($this->titaneEquipment as $item){
            $ci->add($item, CreativeCategory::ITEMS, $groupOpal);
        }

        // Équipement Opale 3D
        foreach($this->opaleEquipment as $item){
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

    public function onDropItem(PlayerDropItemEvent $event) : void{
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

        // Mannequin d'entraînement
        if($opalId === "opal:combat_dummy"){
            $event->cancel();
            \opal\core\training\CombatDummyManager::getInstance()->startTraining($player);
            return;
        }

        // Invocation d'un Mob ou Boss
        if(str_starts_with($opalId, "opal:boss_") || str_starts_with($opalId, "opal:mob_") || $opalId === "opal:abyssal_leviathan"){
            $event->cancel();
            $pos = $player->getPosition()->addVector($player->getDirectionVector()->multiply(3));

            $pk = new PlaySoundPacket();
            $pk->soundName = "beacon.activate";
            $pk->x = (float)$pos->x;
            $pk->y = (float)$pos->y;
            $pk->z = (float)$pos->z;
            $pk->volume = 1.0;
            $pk->pitch = 1.5;
            $player->getNetworkSession()->sendDataPacket($pk);

            $partPk = SpawnParticleEffectPacket::create(0, -1, $pos->add(0, 1, 0), "minecraft:sparkler_emitter", null);
            $player->getNetworkSession()->sendDataPacket($partPk);

            $mobName = $item->getCustomName();
            $player->sendTitle("§6⭐ INVOCATION CÉLESTE ⭐", "§f{$mobName} §aest apparu !", 5, 30, 5);

            $this->spawnBossEntity($player, $pos, $mobName, $opalId);
        }
    }

    private function spawnBossEntity(Player $player, \pocketmine\world\Position $pos, string $name, string $opalId) : void{
        try{
            $world = $player->getWorld();
            $world->loadChunk($pos->getFloorX() >> 4, $pos->getFloorZ() >> 4);
            $location = Location::fromObject($pos, $world, $player->getLocation()->getYaw(), $player->getLocation()->getPitch());

            $healthMap = [
                "opal:boss_basalt_golem" => 800,
                "opal:boss_cave_hydra" => 700,
                "opal:boss_fallen_monarch" => 750,
                "opal:boss_vampire_lord" => 500,
                "opal:boss_stone_sentinel" => 850,
                "opal:boss_telluric_behemoth" => 900,
                "opal:boss_bramble_queen" => 650,
                "opal:abyssal_leviathan" => 350,
            ];
            $health = $healthMap[$opalId] ?? 120;

            $entity = new Zombie($location);
            $entity->setNameTag($name . " §c[" . $health . " HP]");
            $entity->setNameTagAlwaysVisible(true);
            $entity->setMaxHealth($health);
            $entity->setHealth($health);
            $entity->spawnToAll();
        }catch(\Throwable $e){
            $player->getServer()->getLogger()->warning("[CreativeRegistry] Impossible de faire apparaître le boss : " . $e->getMessage());
        }
    }

    public function openCreativeGUI(Player $player) : void{
        $form = new SimpleForm(function(Player $player, $data) : void{
            if($data === null) return;
            switch($data){
                case 0: $this->openItemsSubMenu($player, "Blocs & Minerais Déverrouillés", $this->customBlocks); break;
                case 1: $this->openItemsSubMenu($player, "Boss & Mobs 3D", $this->customMobs); break;
                case 2: $this->openItemsSubMenu($player, "Bâtons & Tactiques", $this->customItems); break;
                case 3: $this->openItemsSubMenu($player, "Équipement Titane 3D", $this->titaneEquipment); break;
                case 4: $this->openItemsSubMenu($player, "Équipement Opale 3D", $this->opaleEquipment); break;
            }
        });

        $unlockedCount = count(array_filter($this->customBlocks, fn(Item $it) => $it->getNamedTag()->getTag("opal_unimplemented") === null));
        $lockedCount = count($this->customBlocks) - $unlockedCount;

        $form->setTitle("§l§d🎨 MENU CRÉATIF OPAL HEAVEN");
        $form->setContent("§7Sélectionnez une catégorie pour récupérer vos éléments moddés (les blocs avec modèles 3D sont 100% déverrouillés et posables) :");
        $form->addButton("§b💎 Blocs & Minerais\n§a[" . $unlockedCount . " déverrouillés | §c" . $lockedCount . " barrières]");
        $form->addButton("§c👑 Boss & Mobs 3D\n§8[" . count($this->customMobs) . " entités]");
        $form->addButton("§6⚡ Bâtons Tactiques\n§8[" . count($this->customItems) . " items]");
        $form->addButton("§7🛡 Équipement Titane 3D\n§8[" . count($this->titaneEquipment) . " pièces]");
        $form->addButton("§d✨ Équipement Opale 3D\n§8[" . count($this->opaleEquipment) . " pièces]");

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
        $form->setContent("§7Cliquez sur un élément pour l'obtenir dans votre inventaire :");
        foreach($keys as $it){
            $form->addButton($it->getCustomName());
        }

        FormManager::getInstance()->sendForm($player, $form);
    }

    private function createImplementedBlock(Item $baseItem, string $name, array $description, string $opalId) : Item{
        $item = clone $baseItem;
        $item->setCustomName($name);
        $lore = [
            "§8[Opal Heaven — Matériau Céleste]",
            "§a✔ Modèle 3D & Texture intégrés",
            "§bPosable et récupérable en jeu",
            "§7Caractéristiques :"
        ];
        foreach($description as $line){
            $lore[] = "§8• " . $line;
        }
        $item->setLore($lore);
        $item->getNamedTag()->setString("opal_id", $opalId);
        $item->getNamedTag()->setString("opal_clean_name", $name);
        return $item;
    }

    private function createCustomItem(Item $baseItem, string $name, array $lore, string $opalId, int $charges = 0) : Item{
        $item = clone $baseItem;
        $item->setCustomName($name);
        $item->setLore($lore);
        $item->getNamedTag()->setString("opal_id", $opalId);
        if($charges > 0){
            $item->getNamedTag()->setInt("opal_charges", $charges);
            $item->getNamedTag()->setInt("opal_max_charges", $charges);
        }
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
