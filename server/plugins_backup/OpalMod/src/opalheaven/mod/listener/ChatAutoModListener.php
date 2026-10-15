<?php

declare(strict_types=1);

namespace opalheaven\mod\listener;

use opalheaven\mod\OpalMod;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\player\Player;

class ChatAutoModListener implements Listener {

    private OpalMod $plugin;
    /** @var array<string, float> Last chat timestamp per player */
    private array $lastChat = [];

    // Mots interdits / injures / insultes filtrés
    private const BLACKLIST = [
        "fdp", "ntm", "connard", "salope", "pute", "enculé", "merde", "nique", "nazi", "hitler"
    ];

    public function __construct(OpalMod $plugin) {
        $this->plugin = $plugin;
    }

    public function onChat(PlayerChatEvent $event) : void {
        $player = $event->getPlayer();
        $name = $player->getName();
        $xuid = $player->getXuid();
        $msg = $event->getMessage();

        // 1. Vérification Mute Actif
        $mute = $this->plugin->getDatabase()->getActiveMute($name, $xuid);
        if ($mute !== null) {
            $event->cancel();
            $exp = (int) $mute["expires_at"];
            $timeText = $exp > 0 ? "jusqu'au " . date("d/m/Y H:i:s", $exp) : "définitif";
            $player->sendMessage("§c§l[MUTE] §r§cVous êtes réduit au silence par §e{$mute['staff_name']} §c({$timeText}) !");
            $player->sendMessage("§7Raison : §f{$mute['reason']}");
            return;
        }

        // Si op/staff, dispense de l'anti-spam
        if ($player->hasPermission("opalmod.staff")) {
            return;
        }

        $now = microtime(true);

        // 2. Anti-Spam (1 message toutes les 1.2s)
        if (isset($this->lastChat[$name])) {
            $diff = $now - $this->lastChat[$name];
            if ($diff < 1.2) {
                $event->cancel();
                $player->sendMessage("§c[AutoMod] Merci de ralentir votre cadence dans le chat.");
                return;
            }
        }
        $this->lastChat[$name] = $now;

        // 3. Anti-Publicité (IPs ou liens externes)
        // Détection IP v4
        if (preg_match('/\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b/', $msg)) {
            $event->cancel();
            $player->sendMessage("§c[AutoMod] Les adresses IP sont strictement interdites dans le chat !");
            $this->alertStaff("Tentative de pub IP par {$name} : \"{$msg}\"");
            return;
        }

        // Détection domaines / discord
        if (preg_match('/(discord\.(gg|me|io)|(?:https?:\/\/)?(?:[a-z0-9\-]+\.)+(?:com|net|org|fr|eu|io|gg|to|be|xyz))\b/i', $msg)) {
            $event->cancel();
            $player->sendMessage("§c[AutoMod] Les liens et invitations externes sont interdits !");
            $this->alertStaff("Tentative de lien externe par {$name} : \"{$msg}\"");
            return;
        }

        // 4. Anti-Majuscules excessives (> 70% si longueur > 6)
        if (strlen($msg) > 6) {
            $caps = strlen(preg_replace('/[^A-Z]/', '', $msg));
            $ratio = $caps / strlen($msg);
            if ($ratio > 0.65) {
                $msg = mb_strtolower($msg);
                $event->setMessage($msg);
            }
        }

        // 5. Filtre de grossièretés / Blacklist
        $lowered = mb_strtolower($msg);
        foreach (self::BLACKLIST as $badWord) {
            if (str_contains($lowered, $badWord)) {
                $event->cancel();
                $player->sendMessage("§c[AutoMod] Propos inappropriés détectés. Restez courtois !");
                $this->plugin->getDatabase()->addWarn($name, $xuid, "AUTO-MOD", "Langage inapproprié");
                $this->alertStaff("Langage grossier de {$name} : \"{$msg}\"");
                return;
            }
        }
    }

    private function alertStaff(string $content) : void {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $staff) {
            if ($staff->hasPermission("opalmod.staff")) {
                $staff->sendMessage("§e§l[STAFF ALERT] §r§6{$content}");
            }
        }
    }
}
