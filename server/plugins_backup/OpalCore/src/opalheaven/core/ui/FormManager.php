<?php

declare(strict_types=1);

namespace opalheaven\core\ui;

use opalheaven\core\OpalCore;
use opalheaven\core\forms\SimpleForm;
use opalheaven\core\forms\CustomForm;
use opalheaven\core\forms\ModalForm;
use opalheaven\core\orders\OrderType;
use opalheaven\core\orders\SpecializationType;
use opalheaven\core\orders\BloodShopManager;
use opalheaven\core\faction\TechManager;
use opalheaven\core\faction\Faction;
use pocketmine\player\Player;

class FormManager {

    private OpalCore $plugin;

    public function __construct(OpalCore $plugin) {
        $this->plugin = $plugin;
    }

    /* ------------------------------------------------------------- */
    /*                     GRAND MENU PRINCIPAL                      */
    /* ------------------------------------------------------------- */

    public function openMainMenu(Player $player) : void {
        $orderMgr = $this->plugin->getOrderManager();
        $specMgr = $orderMgr->getSpecializationManager();
        $factionMgr = $this->plugin->getFactionManager();

        $activeOrder = $orderMgr->getActiveOrder($player);
        $activeSpec = $specMgr->getSpecialization($player);
        $faction = $factionMgr->getPlayerFaction($player);

        $orderText = $activeOrder !== null ? $activeOrder->getDisplayName() : "§7Aucun ordre choisi";
        $specText = $activeSpec !== null ? $activeSpec->getDisplayName() : "§7Aucune";
        $factionText = $faction !== null ? "§e" . $faction->name : "§7Sans faction";
        $marks = $orderMgr->getBloodMarks($player);

        $banner = "§5╔═══════════════════════════════════════╗\n" .
                  "§5║   §d✦ §f§lO P A L   H E A V E N§r §d✦   §5║\n" .
                  "§5║    §7» §eL'Ascension Révolutionnaire §7«    §5║\n" .
                  "§5╚═══════════════════════════════════════╝§r\n\n" .
                  "§fBienvenue aventurier §e{$player->getName()} §f!\n" .
                  "§8─── §d◈ §fVOTRE PROFIL §d◈ §8───\n" .
                  "§7Ordre Actif : {$orderText}\n" .
                  "§7Spécialisation : {$specText}\n" .
                  "§7Faction : {$factionText}\n" .
                  "§7Marques de Sang : §c{$marks} §7| Posture : §a" . (int)$this->plugin->getPostureManager()->getPosture($player) . "%\n\n" .
                  "§fChoisissez une destination céleste :";

        $form = new SimpleForm("§l§dOPAL HEAVEN §r§8| §7Menu Principal", $banner);

        $form->addButton("§6§lOrdres & Spécialisations\n§r§83 Ordres, 9 Voies, Compétences", null, null, function(Player $p) {
            $this->openOrdersMenu($p);
        });

        $form->addButton("§c§lBoutique du Sang\n§r§8Solde: {$marks} Marques", null, null, function(Player $p) {
            $this->openBloodShopMenu($p);
        });

        $form->addButton("§9§lMa Faction\n§r§8Territoires, Cœur & Technologies", null, null, function(Player $p) {
            $this->openFactionMenu($p);
        });

        $form->addButton("§b§lLes 40 Biomes Moddés\n§r§86 Grands Écosystèmes & Auras", null, null, function(Player $p) {
            $this->openBiomesMenu($p);
        });

        $form->addButton("§e§lCombat & Posture\n§r§8Parade chirurgicale & Stagger", null, null, function(Player $p) {
            $this->openCombatMenu($p);
        });

        $form->addButton("§3§lMinage Tellurique\n§r§8Harmoniques & Opale Céleste", null, null, function(Player $p) {
            $this->openMiningMenu($p);
        });

        $form->addButton("§a§lAgriculture & Vivant\n§r§8Génétique, Bêtes & Festins", null, null, function(Player $p) {
            $this->openAgricultureMenu($p);
        });

        $form->addButton("§7Fermer", null, null, function(Player $p) {});

        $player->sendForm($form);
    }

    /* ------------------------------------------------------------- */
    /*                  MENU ORDRES & SPÉCIALISATIONS                */
    /* ------------------------------------------------------------- */

    public function openOrdersMenu(Player $player) : void {
        $orderMgr = $this->plugin->getOrderManager();
        $specMgr = $orderMgr->getSpecializationManager();
        $activeOrder = $orderMgr->getActiveOrder($player);
        $activeSpec = $specMgr->getSpecialization($player);

        $banner = "§6╔═══════════════════════════════════════╗\n" .
                  "§6║     §e❂ §f§lLES 3 ORDRES D'ASCENSION§r §e❂     §6║\n" .
                  "§6╚═══════════════════════════════════════╝§r\n\n" .
                  "§7Chaque ordre possède son socle de progression et 3 spécialisations exclusives.\n" .
                  "§eSpécialisation actuelle : " . ($activeSpec !== null ? $activeSpec->getDisplayName() : "§7Aucune") . "\n\n" .
                  "§fSélectionnez un ordre pour en consulter les voies :";

        $form = new SimpleForm("§l§6LES ORDRES D'ASCENSION", $banner);

        foreach (OrderType::cases() as $order) {
            $lvl = $orderMgr->getLevel($player, $order);
            $xp = $orderMgr->getXp($player, $order);
            $req = $orderMgr->getRequiredXp($lvl);
            $isActive = ($activeOrder === $order) ? " §e[ACTIF]" : "";

            $btnText = "{$order->getDisplayName()}{$isActive}\n§8Niveau {$lvl} ({$xp}/{$req} XP)";
            $form->addButton($btnText, null, null, function(Player $p) use ($order) {
                $this->openOrderDetails($p, $order);
            });
        }

        $form->addButton("§7« Retour au Menu", null, null, function(Player $p) {
            $this->openMainMenu($p);
        });

        $player->sendForm($form);
    }

    public function openOrderDetails(Player $player, OrderType $order) : void {
        $orderMgr = $this->plugin->getOrderManager();
        $specMgr = $orderMgr->getSpecializationManager();
        $lvl = $orderMgr->getLevel($player, $order);
        $xp = $orderMgr->getXp($player, $order);
        $req = $orderMgr->getRequiredXp($lvl);

        $desc = "{$order->getDisplayName()}\n" .
                "§7" . $order->getDescription() . "\n\n" .
                "§7Niveau Actuel : §e{$lvl} / 50\n" .
                "§7Progression : §f{$xp} §8/ §f{$req} XP\n\n" .
                "§fChoisissez une action ou explorez les spécialisations :";

        $form = new SimpleForm($order->getDisplayName(), $desc);

        // Bouton pour rejoindre l'Ordre
        $form->addButton("§aRejoindre cet Ordre", null, null, function(Player $p) use ($order, $orderMgr) {
            $orderMgr->setActiveOrder($p, $order);
            $this->openOrderDetails($p, $order);
        });

        // Boutons pour les 3 Spécialisations de l'Ordre
        foreach ($order->getSpecializations() as $spec) {
            $isCur = ($specMgr->getSpecialization($player) === $spec) ? " §e[CHOISIE]" : "";
            $form->addButton("{$spec->getDisplayName()}{$isCur}\n§8" . $spec->getSubtitle(), null, null, function(Player $p) use ($spec) {
                $this->openSpecDetails($p, $spec);
            });
        }

        $form->addButton("§7« Retour aux Ordres", null, null, function(Player $p) {
            $this->openOrdersMenu($p);
        });

        $player->sendForm($form);
    }

    public function openSpecDetails(Player $player, SpecializationType $spec) : void {
        $orderMgr = $this->plugin->getOrderManager();
        $specMgr = $orderMgr->getSpecializationManager();
        $isCurrent = $specMgr->getSpecialization($player) === $spec;

        $desc = "§l{$spec->getDisplayName()} §r§8— §7" . $spec->getSubtitle() . "\n\n" .
                "§6Passif : §e" . $spec->getPassiveName() . "\n" .
                "§7" . $spec->getPassiveDescription() . "\n\n" .
                "§bCompétence Active : §3" . $spec->getActiveName() . " §8(CD: " . $spec->getActiveCooldown() . "s)\n" .
                "§7" . $spec->getActiveDescription() . "\n\n" .
                "§7Déclenchement : Sneak + Double Clic ou Commande §e/skill§7.";

        $form = new SimpleForm($spec->getDisplayName(), $desc);

        if (!$isCurrent) {
            $form->addButton("§a§lChoisir cette Spécialisation", null, null, function(Player $p) use ($spec, $specMgr) {
                $specMgr->setSpecialization($p, $spec);
                $this->openSpecDetails($p, $spec);
            });
        } else {
            $form->addButton("§e§lActiver la Compétence en Jeu\n§r§8Tester le pouvoir", null, null, function(Player $p) use ($specMgr) {
                $specMgr->triggerActiveSkill($p);
            });
        }

        $form->addButton("§7« Retour", null, null, function(Player $p) use ($spec) {
            $this->openOrderDetails($p, $spec->getOrder());
        });

        $player->sendForm($form);
    }

    /* ------------------------------------------------------------- */
    /*                      BOUTIQUE DU SANG                         */
    /* ------------------------------------------------------------- */

    public function openBloodShopMenu(Player $player) : void {
        $orderMgr = $this->plugin->getOrderManager();
        $marks = $orderMgr->getBloodMarks($player);

        $desc = "§c╔═══════════════════════════════════════╗\n" .
                "§c║     §4⚔ §f§lLA BOUTIQUE DU SANG§r §4⚔     §c║\n" .
                "§c╚═══════════════════════════════════════╝§r\n\n" .
                "§7L'autonomie totale du guerrier PvP. Achetez tout votre équipement\n" .
                "de combat avec vos Marques de Sang, sans jamais miner ni farmer !\n\n" .
                "§fVotre Solde : §c§l{$marks} Marques de Sang\n" .
                "§7Choisissez un article :";

        $form = new SimpleForm("§l§cBOUTIQUE DU SANG", $desc);

        $catalogue = BloodShopManager::getCatalogue();
        foreach ($catalogue as $id => $item) {
            $form->addButton("{$item['name']}\n§e{$item['cost']} Marques §8• {$item['desc']}", null, null, function(Player $p) use ($id, $orderMgr) {
                BloodShopManager::buyItem($p, $id, $orderMgr);
                $this->openBloodShopMenu($p);
            });
        }

        $form->addButton("§7« Retour au Menu", null, null, function(Player $p) {
            $this->openMainMenu($p);
        });

        $player->sendForm($form);
    }

    /* ------------------------------------------------------------- */
    /*                        MENU FACTION                           */
    /* ------------------------------------------------------------- */

    public function openFactionMenu(Player $player) : void {
        $factionMgr = $this->plugin->getFactionManager();
        $faction = $factionMgr->getPlayerFaction($player);

        if ($faction === null) {
            $desc = "§eVous ne faites partie d'aucune faction actuellement.\n\n" .
                    "Fondez votre propre clan pour conquérir des territoires et bâtir votre Cœur territorial !";
            $form = new SimpleForm("§l§9GESTION FACTION", $desc);

            $form->addButton("§a§lCréer une Faction\n§r§8Fonder votre dynastie", null, null, function(Player $p) {
                $this->openCreateFactionForm($p);
            });

            $form->addButton("§7« Retour", null, null, function(Player $p) {
                $this->openMainMenu($p);
            });

            $player->sendForm($form);
            return;
        }

        $role = $factionMgr->getPlayerRole($player);
        $claims = $factionMgr->getClaimCount($faction->id);
        $siegeStatus = $faction->isUnderSiege() ? "§c§lEN SIÈGE !" : "§aPaix";

        $desc = "§9╔═══════════════════════════════════════╗\n" .
                "§9║   §b🏰 §f§lFACTION : {$faction->name}§r §b🏰   §9║\n" .
                "§9╚═══════════════════════════════════════╝§r\n\n" .
                "§7Rôle : §e{$role} §7| Niveau : §6{$faction->level} §7(XP: {$faction->xp})\n" .
                "§7Power : §b{$faction->power} §8/ §b{$faction->maxPower}\n" .
                "§7Claims Territoriaux : §f{$claims} chunks\n" .
                "§7Banque de Faction : §6{$faction->bankBalance} Or\n" .
                "§7Cœur Territorial : §f" . ($faction->isHeartSet() ? "HP {$faction->heartHealth}/10000" : "§cNon posé") . "\n" .
                "§7Statut Siège : {$siegeStatus}\n";

        $form = new SimpleForm("§l§9FACTION : {$faction->name}", $desc);

        $form->addButton("§aRevendiquer ce Chunk (/f claim)\n§r§8Élargir le territoire", null, null, function(Player $p) use ($faction, $factionMgr) {
            $factionMgr->claimChunk($p, $faction);
        });

        $form->addButton("§cAbandonner ce Chunk (/f unclaim)\n§r§8Libérer le territoire", null, null, function(Player $p) use ($faction, $factionMgr) {
            $factionMgr->unclaimChunk($p, $faction);
        });

        $form->addButton("§6Arbre Technologique (20 Paliers)\n§r§8Avantages permanents", null, null, function(Player $p) use ($faction) {
            $this->openTechTreeMenu($p, $faction);
        });

        $form->addButton("§eDéposer à la Banque\n§r§8Contribuer au trésor de guerre", null, null, function(Player $p) use ($faction, $factionMgr) {
            $factionMgr->depositBank($p, $faction, 100);
            $this->openFactionMenu($p);
        });

        $form->addButton("§dPoser le Cœur Territorial\n§r§8À votre position actuelle", null, null, function(Player $p) use ($faction, $factionMgr) {
            $factionMgr->setFactionHeart($faction, $p->getPosition());
            $p->sendMessage("§a[Faction] Cœur territorial ancré à votre position ! (10 000 HP)");
            $this->openFactionMenu($p);
        });

        $form->addButton("§7« Retour au Menu", null, null, function(Player $p) {
            $this->openMainMenu($p);
        });

        $player->sendForm($form);
    }

    private function openCreateFactionForm(Player $player) : void {
        $form = new CustomForm("§l§aCRÉER UNE FACTION");
        $form->addInput("Nom de la faction (3-16 lettres/chiffres) :", "Ex: Celestia");

        $form->setHandler(function(Player $p, array $data) {
            $name = $data[0] ?? "";
            if ($name !== "") {
                $this->plugin->getFactionManager()->createFaction($p, $name);
            }
        });

        $player->sendForm($form);
    }

    public function openTechTreeMenu(Player $player, Faction $faction) : void {
        $factionMgr = $this->plugin->getFactionManager();
        $tiers = TechManager::getTechTiers();

        $desc = "§6Arbre de Technologies de Faction (Livre Blanc Section 9)\n" .
                "§7Débloquez des paliers pour conférer des bonus permanents à tous les membres !\n";

        $form = new SimpleForm("§l§6TECHNOLOGIES DE FACTION", $desc);

        foreach ($tiers as $tier => $info) {
            $unlocked = $factionMgr->hasTech($faction->id, $tier);
            $status = $unlocked ? "§a[DÉBLOQUÉ]" : "§7[VERROUILLÉ]";
            $form->addButton("Niveau {$tier} : {$info['name']} {$status}\n§8{$info['desc']}", null, null, function(Player $p) use ($faction, $tier, $unlocked, $factionMgr) {
                if (!$unlocked) {
                    $factionMgr->unlockTech($faction->id, $tier);
                    $p->sendMessage("§a[Tech Faction] Technologie Palier {$tier} débloquée !");
                }
                $this->openTechTreeMenu($p, $faction);
            });
        }

        $form->addButton("§7« Retour à la Faction", null, null, function(Player $p) {
            $this->openFactionMenu($p);
        });

        $player->sendForm($form);
    }

    /* ------------------------------------------------------------- */
    /*                      AUTRES MENUS SECONDAIRES                 */
    /* ------------------------------------------------------------- */

    public function openCombatMenu(Player $player) : void {
        $posture = (int)$this->plugin->getPostureManager()->getPosture($player);
        $desc = "§cSystème de Combat & Posture d'Opal Heaven\n\n" .
                "§f• §eParade Parfaite : §7Accroupissez-vous (Sneak) entre 0.05s et 0.45s avant l'impact.\n" .
                "§f• §eEffet Parade : §7Annule les dégâts, renvoie +28% de posture (+39.2% Bretteur) et repousse l'assaillant.\n" .
                "§f• §eStagger (Rupture) : §7À 100% de posture, étourdi 2s (Slowness VI, Mining Fatigue V, -30% armure).\n\n" .
                "§7Votre Posture Actuelle : §a{$posture} / 100%";

        $form = new SimpleForm("§l§cPOSTURE & COMBAT", $desc);
        $form->addButton("§7« Retour", null, null, function(Player $p) { $this->openMainMenu($p); });
        $player->sendForm($form);
    }

    public function openMiningMenu(Player $player) : void {
        $charge = $this->plugin->getResonanceManager()->getCharge($player);
        $desc = "§3Système de Résonance Tellurique & Sismologie\n\n" .
                "§f• Frappez la roche en rythme pour accumuler la charge tellurique.\n" .
                "§f• À 100% de charge, déclenche une surcharge libérant des gemmes rares d'opale !\n\n" .
                "§7Charge Actuelle : §b{$charge} / 100%";

        $form = new SimpleForm("§l§3RÉSONANCE TELLURIQUE", $desc);
        $form->addButton("§7« Retour", null, null, function(Player $p) { $this->openMainMenu($p); });
        $player->sendForm($form);
    }

    public function openAgricultureMenu(Player $player) : void {
        $desc = "§aAgriculture Mystique & Génétique Végétale\n\n" .
                "§f• Hybridation de semences arcaniques pour produire des Lianes de Fer.\n" .
                "§f• Élevage génétique sélectif de destriers célestes (+50% PV, vitesse x1.8).\n" .
                "§f• Confection de grands banquets de siège et élixirs alchimiques triples.";

        $form = new SimpleForm("§l§aAGRICULTURE MYSTIQUE", $desc);
        $form->addButton("§7« Retour", null, null, function(Player $p) { $this->openMainMenu($p); });
        $player->sendForm($form);
    }

    public function openBiomesMenu(Player $player) : void {
        $registry = $this->plugin->getBiomeRegistry();
        $total = $registry->getCount();

        $desc = "§bLes 40 Biomes Moddés d'Opal Heaven ({$total} Chargés)\n\n" .
                "Sélectionnez un biome pour consulter ses propriétés et son aura environnementale :";

        $form = new SimpleForm("§l§bLES 40 BIOMES MODDÉS", $desc);

        foreach ($registry->getAll() as $biome) {
            $form->addButton("{$biome->getName()}\n§8{$biome->getCategory()}", null, null, function(Player $p) use ($biome) {
                $this->openBiomeDetails($p, $biome);
            });
        }

        $form->addButton("§7« Retour", null, null, function(Player $p) { $this->openMainMenu($p); });
        $player->sendForm($form);
    }

    public function openBiomeDetails(Player $player, \opalheaven\core\biomes\OpalBiome $biome) : void {
        $desc = "§l{$biome->getName()} §r§8(ID: {$biome->getId()})\n" .
                "§7Royaume : §e{$biome->getCategory()} §7| Rareté : §6{$biome->getRarity()}/10\n\n" .
                "§fDescription :\n§7{$biome->getDescription()}\n\n" .
                "§fAura Environnementale : §a" . ($biome->getAuraEffect() ?? "Aucune") . "\n" .
                "§fBloc de Surface : §b{$biome->getTopBlockId()}\n" .
                "§fSous-Sol : §b{$biome->getSubBlockId()}\n";

        $form = new SimpleForm($biome->getName(), $desc);
        $form->addButton("§7« Retour aux Biomes", null, null, function(Player $p) { $this->openBiomesMenu($p); });
        $player->sendForm($form);
    }
}
