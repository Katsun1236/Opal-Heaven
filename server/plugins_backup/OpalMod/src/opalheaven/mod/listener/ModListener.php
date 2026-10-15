<?php

declare(strict_types=1);

namespace opalheaven\mod\listener;

use opalheaven\mod\OpalMod;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;

class ModListener implements Listener {

    private OpalMod $plugin;

    public function __construct(OpalMod $plugin) {
        $this->plugin = $plugin;
    }

    public function onPreLogin(PlayerPreLoginEvent $event) : void {
        $info = $event->getPlayerInfo();
        $name = $info->getUsername();
        $xuid = $info instanceof \pocketmine\player\XboxLivePlayerInfo ? $info->getXuid() : "";

        $ban = $this->plugin->getDatabase()->getActiveBan($name, $xuid);
        if ($ban !== null) {
            $exp = (int) $ban["expires_at"];
            $expText = $exp > 0 ? date("d/m/Y à H:i:s", $exp) : "DÉFINITIF";

            $kickMsg = "§l§d╔══════════════════════════════════╗\n" .
                       "§l§d║       §f✦ §eOPAL HEAVEN §f✦       §l§d║\n" .
                       "§l§d╚══════════════════════════════════╝§r\n\n" .
                       "§c§lVOUS ÊTES BANNI DU SERVEUR !\n\n" .
                       "§7Modérateur : §f{$ban['staff_name']}\n" .
                       "§7Raison : §e{$ban['reason']}\n" .
                       "§7Expiration : §c{$expText}\n\n" .
                       "§8Pour contester, rendez-vous sur discord.gg/opalheaven";

            $event->setKickReason(PlayerPreLoginEvent::KICK_REASON_BANNED, $kickMsg);
        }
    }

    public function onJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();

        // Cacher les joueurs en vanish
        foreach ($this->plugin->getVanishedPlayers() as $vanishedPlayer) {
            if (!$player->hasPermission("opalmod.vanish")) {
                $player->hidePlayer($vanishedPlayer);
            }
        }

        // Notification des signalements aux membres du staff
        if ($player->hasPermission("opalmod.staff")) {
            $pending = count($this->plugin->getDatabase()->getPendingReports());
            if ($pending > 0) {
                $player->sendMessage("§e§l[STAFF] §r§6Il y a actuellement §e{$pending} §6signalement(s) en attente (/staff) !");
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event) : void {
        $player = $event->getPlayer();
        if ($this->plugin->isVanished($player)) {
            $this->plugin->setVanished($player, false);
            $event->setQuitMessage("");
        }
        if ($this->plugin->isFrozen($player)) {
            $this->plugin->unfreeze($player);
        }
    }
}
