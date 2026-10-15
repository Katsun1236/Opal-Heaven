<?php

declare(strict_types=1);

namespace opalheaven\mod\listener;

use opalheaven\mod\OpalMod;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;

class FreezeListener implements Listener {

    private OpalMod $plugin;

    public function __construct(OpalMod $plugin) {
        $this->plugin = $plugin;
    }

    public function onMove(PlayerMoveEvent $event) : void {
        $player = $event->getPlayer();
        if ($this->plugin->isFrozen($player)) {
            $from = $event->getFrom();
            $to = $event->getTo();
            if ($from->x !== $to->x || $from->z !== $to->z) {
                $event->cancel();
            }
        }
    }

    public function onBreak(BlockBreakEvent $event) : void {
        if ($this->plugin->isFrozen($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onPlace(BlockPlaceEvent $event) : void {
        if ($this->plugin->isFrozen($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onInteract(PlayerInteractEvent $event) : void {
        if ($this->plugin->isFrozen($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onDamage(EntityDamageByEntityEvent $event) : void {
        $damager = $event->getDamager();
        $target = $event->getEntity();

        if ($damager instanceof Player && $this->plugin->isFrozen($damager)) {
            $event->cancel();
            return;
        }

        if ($target instanceof Player && $this->plugin->isFrozen($target)) {
            $event->cancel();
        }
    }

    public function onCommand(CommandEvent $event) : void {
        $sender = $event->getSender();
        if ($sender instanceof Player && $this->plugin->isFrozen($sender)) {
            $event->cancel();
            $sender->sendMessage("§c[OpalMod] Vous ne pouvez pas exécuter de commande pendant une vérification !");
        }
    }

    public function onQuit(PlayerQuitEvent $event) : void {
        $player = $event->getPlayer();
        if ($this->plugin->isFrozen($player)) {
            // Déconnexion en freeze = alerte staff + ban automatique 7 jours
            $this->plugin->getDatabase()->addBan(
                $player->getName(),
                $player->getXuid(),
                "AUTO-MOD",
                "Déconnexion en cours de freeze / vérification",
                7 * 86400
            );

            $this->plugin->getServer()->broadcastMessage(
                "§c§l[OPAL ALERTE] §r§e{$player->getName()} §cs'est déconnecté en étant freeze ! Ban automatique 7j appliqué."
            );
            $this->plugin->unfreeze($player);
        }
    }
}
