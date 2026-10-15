<?php

declare(strict_types=1);

namespace opalheaven\core\orders;

use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\item\Item;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\world\sound\XpLevelUpSound;

class BloodShopManager {

    public static function getCatalogue() : array {
        return [
            1 => ["name" => "Rations de Viande Épicée (x16)", "cost" => 15, "desc" => "Restaure 8 barres de faim + saturation."],
            2 => ["name" => "Pomme d'Or d'Arène (x4)", "cost" => 35, "desc" => "Pomme dorée bénie pour les duels intenses."],
            3 => ["name" => "Poussière de Réparation (x1)", "cost" => 25, "desc" => "Restaure 100% de durabilité sur l'arme en main."],
            4 => ["name" => "Fiole de Rage Sanguine (x2)", "cost" => 30, "desc" => "Potion conférant Force I pendant 1 min 30."],
            5 => ["name" => "Rapière du Bretteur (Fer)", "cost" => 50, "desc" => "Arme de précision avec +10% fenêtre de parade."],
            6 => ["name" => "Plastron d'Arène Renforcé", "cost" => 80, "desc" => "Plastron en fer enchanté Protection II."],
            7 => ["name" => "Boussole de Traque de Prime", "cost" => 60, "desc" => "Pointe vers la cible sous prime la plus proche."],
            8 => ["name" => "Éclat de Renaissance d'Opale", "cost" => 250, "desc" => "Permet de réassigner sa spécialisation."]
        ];
    }

    public static function buyItem(Player $player, int $itemId, OrderManager $orderMgr) : bool {
        $catalogue = self::getCatalogue();
        if (!isset($catalogue[$itemId])) {
            return false;
        }

        $itemData = $catalogue[$itemId];
        $cost = $itemData["cost"];

        if (!$orderMgr->removeBloodMarks($player, $cost)) {
            $player->sendMessage("§c[Boutique du Sang] Solde insuffisant ! Requis: §e{$cost} Marques de Sang§c.");
            return false;
        }

        $inv = $player->getInventory();
        switch ($itemId) {
            case 1:
                $item = VanillaItems::COOKED_BEEF()->setCount(16)->setCustomName("§cRations de Viande Épicée");
                $inv->addItem($item);
                break;
            case 2:
                $item = VanillaItems::GOLDEN_APPLE()->setCount(4)->setCustomName("§6Pomme d'Or d'Arène");
                $inv->addItem($item);
                break;
            case 3:
                // Restaure la durabilité de l'item en main
                $held = $player->getInventory()->getItemInHand();
                if ($held instanceof \pocketmine\item\Durable) {
                    $held->setDamage(0);
                    $player->getInventory()->setItemInHand($held);
                    $player->sendMessage("§a[Boutique du Sang] Votre arme en main a été entièrement réparée à 100% !");
                } else {
                    $player->sendMessage("§e[Boutique du Sang] Poussière conservée dans l'inventaire.");
                    $inv->addItem(VanillaItems::GLOWSTONE_DUST()->setCustomName("§ePoussière de Réparation"));
                }
                break;
            case 4:
                $item = VanillaItems::POTION()->setCount(2)->setCustomName("§cFiole de Rage Sanguine");
                $inv->addItem($item);
                break;
            case 5:
                $sword = VanillaItems::IRON_SWORD()->setCustomName("§c§lRapière du Bretteur");
                $sword->setLore(["§7Arme d'hast chirurgicale", "§e+10% fenêtre de parade"]);
                $inv->addItem($sword);
                break;
            case 6:
                $chest = VanillaItems::IRON_CHESTPLATE()->setCustomName("§c§lPlastron d'Arène Renforcé");
                $chest->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2));
                $inv->addItem($chest);
                break;
            case 7:
                $comp = VanillaItems::COMPASS()->setCustomName("§dBoussole de Traque de Prime");
                $inv->addItem($comp);
                break;
            case 8:
                // Respec
                $player->sendMessage("§d[Sanctuaire] Vous pouvez désormais réassigner votre spécialisation (/ordre) !");
                break;
        }

        $player->getWorld()->addSound($player->getPosition(), new XpLevelUpSound(20));
        $player->sendMessage("§a[Boutique du Sang] Achat réussi : §f" . $itemData["name"] . " §7(-{$cost} Marques)");
        return true;
    }
}
