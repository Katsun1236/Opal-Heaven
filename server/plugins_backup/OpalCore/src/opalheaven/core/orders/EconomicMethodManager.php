<?php

declare(strict_types=1);

namespace opalheaven\core\orders;

use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\item\Item;
use pocketmine\world\sound\XpCollectSound;

class EconomicMethodManager {

    /**
     * Déclenche une méthode économique pour le joueur, en vérifiant son alignement de spécialisation.
     */
    public static function trigger(Player $player, SpecializationType $spec, int $methodIndex, OrderManager $orderMgr) : void {
        $playerSpec = $orderMgr->getSpecializationManager()->getSpecialization($player);
        if ($playerSpec !== $spec) {
            return;
        }

        $order = $spec->getOrder();
        $xp = 0;
        $name = "";
        $rewardItem = null;

        switch ($spec) {
            case SpecializationType::BRETTEUR:
                switch ($methodIndex) {
                    case 1: $name = "Parade Parfaite Réactive"; $xp = 50; $orderMgr->addBloodMarks($player, 1); break;
                    case 2: $name = "Désarçonnement par Posture Break"; $xp = 80; break;
                    case 3: $name = "Victoire en Duel Pur 1v1"; $xp = 400; $orderMgr->addBloodMarks($player, 5); break;
                    case 4: $name = "Riposte Chirurgicale"; $xp = 45; $orderMgr->addBloodMarks($player, 1); break;
                    case 5: $name = "Défenseur de Bastion"; $xp = 70; break;
                    case 6: $name = "Sparring de Recrue"; $xp = 120; break;
                }
                break;

            case SpecializationType::BERSERKER:
                switch ($methodIndex) {
                    case 1: $name = "Massacre en Sous-Nombre"; $xp = 350; $orderMgr->addBloodMarks($player, 4); break;
                    case 2: $name = "Clutch Sanguinaire (<20% PV)"; $xp = 130; $orderMgr->addBloodMarks($player, 2); break;
                    case 3: $name = "Pillage de Convoi Marchand"; $xp = 200; break;
                    case 4: $name = "Brise-Murailles Humain"; $xp = 150; break;
                    case 5: $name = "Exécution à la Hache de Guerre"; $xp = 80; break;
                    case 6: $name = "Frénésie de Première Ligne"; $xp = 250; $orderMgr->addBloodMarks($player, 3); break;
                }
                break;

            case SpecializationType::TRAQUEUR:
                switch ($methodIndex) {
                    case 1: $name = "Attaque Furtive dans le Dos"; $xp = 90; $orderMgr->addBloodMarks($player, 1); break;
                    case 2: $name = "Exécution de Contrat de Prime"; $xp = 800; $orderMgr->addBloodMarks($player, 10); break;
                    case 3: $name = "Filature & Balisage Tactique"; $xp = 110; break;
                    case 4: $name = "Sabotage Souterrain"; $xp = 160; break;
                    case 5: $name = "Vol à la Tire Furtif"; $xp = 120; break;
                    case 6: $name = "Kill en Territoire Hostile"; $xp = 140; break;
                }
                break;

            case SpecializationType::SISMOLOGUE:
                switch ($methodIndex) {
                    case 1: $name = "Détonation Séismique 3x3"; $xp = 54; break;
                    case 2: $name = "Forage Pyrotechnique"; $xp = 180; break;
                    case 3: $name = "Purge des Monstres Fouisseurs"; $xp = 130; break;
                    case 4: $name = "Effondrement Contrôlé de Gravats"; $xp = 80; break;
                    case 5: $name = "Double Surcharge Harmonique"; $xp = 300; break;
                    case 6: $name = "Extraction de Basalte Dense"; $xp = 60; break;
                }
                break;

            case SpecializationType::PROSPECTEUR:
                switch ($methodIndex) {
                    case 1: $name = "Extraction Pure & Impeccable"; $xp = 100; break;
                    case 2: $name = "Découverte de Géode Scellée"; $xp = 250; break;
                    case 3: $name = "Sondage à la Boussole"; $xp = 75; break;
                    case 4: $name = "Tamisage de Sédiments Anciens"; $xp = 45; break;
                    case 5: $name = "Extraction d'Opale Pure d'Aether"; $xp = 200; break;
                    case 6: $name = "Expertise Gemmologique au Spawn"; $xp = 60; break;
                }
                break;

            case SpecializationType::TERRASCULPTEUR:
                switch ($methodIndex) {
                    case 1: $name = "Forage en Sprint Ininterrompu"; $xp = 120; break;
                    case 2: $name = "Fusion Thermique Instantanée (Auto-Smelt)"; $xp = 60; break;
                    case 3: $name = "Nivellement de Claim"; $xp = 300; break;
                    case 4: $name = "Percement de Puits Abyssal"; $xp = 220; break;
                    case 5: $name = "Recyclage de Gravats"; $xp = 90; break;
                    case 6: $name = "Maintenance de Foreuse"; $xp = 150; break;
                }
                break;

            case SpecializationType::BOTANISTE:
                switch ($methodIndex) {
                    case 1: $name = "Hybridation par Pollinisation"; $xp = 140; break;
                    case 2: $name = "Récolte de Lianes Métallifères"; $xp = 180; break;
                    case 3: $name = "Activation du Totem de Chlorophylle"; $xp = 110; break;
                    case 4: $name = "Stabilisation de Souche Végétale"; $xp = 250; break;
                    case 5: $name = "Cueillette d'Herbes Lunaires Célestes"; $xp = 95; break;
                    case 6: $name = "Purification des Sols Corrompus"; $xp = 130; break;
                }
                break;

            case SpecializationType::MAITRE_DES_BETES:
                switch ($methodIndex) {
                    case 1: $name = "Sélection Génétique Élite"; $xp = 280; break;
                    case 2: $name = "Dressage de Bêtes de Siège"; $xp = 350; break;
                    case 3: $name = "Tonte de Toisons Soyeuses"; $xp = 45; break;
                    case 4: $name = "Traite de Lait Alchimique"; $xp = 80; break;
                    case 5: $name = "Soins Vétérinaires de Troupeau"; $xp = 150; break;
                    case 6: $name = "Domptage de Faune des 40 Biomes"; $xp = 220; break;
                }
                break;

            case SpecializationType::APOTHICAIRE:
                switch ($methodIndex) {
                    case 1: $name = "Confection du Grand Banquet de Siège"; $xp = 320; break;
                    case 2: $name = "Distillation d'Élixir à Triple Infusion"; $xp = 150; break;
                    case 3: $name = "Fabrication de Pains de Route"; $xp = 70; break;
                    case 4: $name = "Concoction de Venins & Gaz"; $xp = 120; break;
                    case 5: $name = "Maturation en Fût de Chêne"; $xp = 180; break;
                    case 6: $name = "Fabrication de Cataplasmes de Secours"; $xp = 85; break;
                }
                break;
        }

        if ($xp > 0) {
            $orderMgr->addXp($player, $order, $xp);
            $player->getWorld()->addSound($player->getPosition(), new XpCollectSound());
            $player->sendActionBarMessage("§6[MÉTHODE: {$name}] §a+{$xp} XP §7({$order->getDisplayName()}§7)");
        }
    }
}
