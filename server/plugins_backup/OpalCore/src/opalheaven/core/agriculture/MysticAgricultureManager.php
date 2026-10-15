<?php

declare(strict_types=1);

namespace opalheaven\core\agriculture;

use pocketmine\player\Player;
use pocketmine\block\Block;
use pocketmine\block\Crops;
use pocketmine\block\Wheat;
use pocketmine\block\Carrot;
use pocketmine\block\Potato;
use pocketmine\block\NetherWartPlant;
use pocketmine\item\VanillaItems;
use pocketmine\item\Item;
use pocketmine\world\particle\HappyVillagerParticle;
use pocketmine\world\particle\HeartParticle;
use pocketmine\world\sound\XpPickupSound;

class MysticAgricultureManager {

    public function handleCropHarvest(Player $player, Block $block, array &$drops) : void {
        // Vérifier si la plante est mûre
        $isMatureCrop = false;
        if ($block instanceof Crops) {
            $isMatureCrop = ($block->getAge() >= 7);
        } elseif ($block instanceof NetherWartPlant) {
            $isMatureCrop = ($block->getAge() >= 3);
        }

        if (!$isMatureCrop) {
            return;
        }

        // 15% de chance d'obtenir une mutation agricole d'Opal
        if (mt_rand(1, 100) <= 15) {
            $world = $player->getWorld();
            $pos = $block->getPosition();

            $mutationRoll = mt_rand(1, 3);
            $specialItem = match($mutationRoll) {
                1 => VanillaItems::WHEAT_SEEDS()->setCustomName("§l§eGraine d'Astrale Dorée")->setLore(["§7Semence bénie par l'Ordre de la Sylve.", "§eDouble la vitesse de pousse des cultures environnantes."]),
                2 => VanillaItems::GOLDEN_CARROT()->setCustomName("§l§bCarotte de Clarté Céleste")->setLore(["§7Légume lumineux gorgé de mana céleste.", "§aConfère Vision Nocturne et Vitesse II à la consommation."]),
                default => VanillaItems::NETHER_WART()->setCustomName("§l§4Verrue Corrompue du Néant")->setLore(["§7Plante mutée dans les failles volcaniques.", "§cComposant d'alchimie de puissance majeure."])
            };

            $drops[] = $specialItem;

            $world->addSound($pos, new XpPickupSound());
            for ($i = 0; $i < 8; ++$i) {
                $world->addParticle($pos->add(0.5, 0.5, 0.5), new HappyVillagerParticle());
            }

            $player->sendActionBarMessage("§a§l[RÉCOLTE MYSTIQUE] §r§eMutation Botanique Récoltée !");
        }
    }

    public function handleAnimalBreeding(Player $player, $entity) : void {
        // 20% de chance d'engendrer une créature sylvestre rare
        if (mt_rand(1, 100) <= 20) {
            $pos = $entity->getPosition();
            $world = $entity->getWorld();

            $world->addSound($pos, new XpPickupSound());
            for ($i = 0; $i < 10; ++$i) {
                $world->addParticle($pos->add(0, 0.8, 0), new HeartParticle());
            }

            $player->sendMessage("§d[Opal Heaven] §a§lBénédiction de la Sylve ! §r§fVotre élevage a donné naissance à une bête supérieure.");
        }
    }
}
