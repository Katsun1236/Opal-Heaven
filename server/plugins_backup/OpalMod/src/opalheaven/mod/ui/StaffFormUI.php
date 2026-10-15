<?php

declare(strict_types=1);

namespace opalheaven\mod\ui;

use opalheaven\mod\OpalMod;
use opalheaven\mod\forms\SimpleForm;
use opalheaven\mod\forms\CustomForm;
use pocketmine\player\Player;
use pocketmine\Server;

class StaffFormUI {

    public static function openStaffDashboard(Player $staff, OpalMod $plugin) : void {
        $server = Server::getInstance();
        $online = count($server->getOnlinePlayers());
        $tps = round($server->getTicksPerSecond(), 1);
        $pendingReports = count($plugin->getDatabase()->getPendingReports());

        $content = "§l§d╔═══════════════════════════════════════╗\n" .
                   "§l§d║    §f✦ §eTABLEAU DE BORD MODÉRATION §f✦    §l§d║\n" .
                   "§l§d╚═══════════════════════════════════════╝§r\n\n" .
                   "§7Joueurs connectés : §e{$online} §8| §7TPS : §a{$tps}\n" .
                   "§7Signalements en attente : " . ($pendingReports > 0 ? "§c§l{$pendingReports} §r§cREQUIS" : "§a0") . "\n\n" .
                   "§fSélectionnez une option de modération :";

        $form = new SimpleForm("§l§dOPAL MODÉRATION", $content);

        $form->addButton("§e§lJoueurs En Ligne\n§r§8Sanctions rapides, freeze, invsee", null, null, function(Player $p) use ($plugin) {
            self::openOnlinePlayersMenu($p, $plugin);
        });

        $form->addButton("§c§lSignalements ({$pendingReports})\n§r§8Traiter les plaintes des joueurs", null, null, function(Player $p) use ($plugin) {
            self::openReportsMenu($p, $plugin);
        });

        $form->addButton("§6§lHistorique & Dossier Joueur\n§r§8Consulter sanctions et antécédents", null, null, function(Player $p) use ($plugin) {
            self::openLookupForm($p, $plugin);
        });

        $form->addButton("§b§lLogs Récents du Staff\n§r§8Dernières actions enregistrées", null, null, function(Player $p) use ($plugin) {
            self::openLogsMenu($p, $plugin);
        });

        $isVanished = $plugin->isVanished($staff);
        $vanishText = $isVanished ? "§a§lVanish : ACTIF" : "§7§lVanish : INACTIF";
        $form->addButton("{$vanishText}\n§r§8Basculer l'invisibilité", null, null, function(Player $p) use ($plugin, $isVanished) {
            $plugin->setVanished($p, !$isVanished);
            $p->sendMessage($isVanished ? "§c[OpalMod] Vous n'êtes plus en vanish." : "§a[OpalMod] Vous êtes désormais invisible (Vanish).");
        });

        $form->addButton("§7Fermer", null, null, function(Player $p) {});

        $staff->sendForm($form);
    }

    public static function openOnlinePlayersMenu(Player $staff, OpalMod $plugin) : void {
        $players = Server::getInstance()->getOnlinePlayers();

        $form = new SimpleForm(
            "§l§eJoueurs En Ligne",
            "§7Sélectionnez un joueur pour interagir ou appliquer une sanction :"
        );

        foreach ($players as $target) {
            $status = $plugin->isFrozen($target) ? " §c[FREEZE]" : "";
            $form->addButton("§f{$target->getName()}{$status}\n§8IP: {$target->getNetworkSession()->getIp()}", null, null, function(Player $p) use ($target, $plugin) {
                if (!$target->isOnline()) {
                    $p->sendMessage("§cCe joueur s'est déconnecté.");
                    return;
                }
                self::openPlayerActionsMenu($p, $target, $plugin);
            });
        }

        $form->addButton("§7« Retour", null, null, function(Player $p) use ($plugin) {
            self::openStaffDashboard($p, $plugin);
        });

        $staff->sendForm($form);
    }

    public static function openPlayerActionsMenu(Player $staff, Player $target, OpalMod $plugin) : void {
        $isFrozen = $plugin->isFrozen($target);
        $mute = $plugin->getDatabase()->getActiveMute($target->getName(), $target->getXuid());

        $content = "§l§eActions sur : §f{$target->getName()}\n\n" .
                   "§7Statut : " . ($isFrozen ? "§cFIGÉ (FREEZE)" : "§aLibre") . "\n" .
                   "§7Muet : " . ($mute !== null ? "§cOUI" : "§aNON") . "\n" .
                   "§7Position : §e" . (int)$target->getPosition()->x . ", " . (int)$target->getPosition()->y . ", " . (int)$target->getPosition()->z;

        $form = new SimpleForm("§l§cModération : {$target->getName()}", $content);

        // Téléportation
        $form->addButton("§a§lSe téléporter\n§r§8Aller vers le joueur", null, null, function(Player $p) use ($target) {
            $p->teleport($target->getPosition());
            $p->sendMessage("§a[OpalMod] Téléporté à {$target->getName()}.");
        });

        // Téléporter le joueur à soi
        $form->addButton("§2§lTéléporter à soi\n§r§8Amener le joueur ici", null, null, function(Player $p) use ($target) {
            $target->teleport($p->getPosition());
            $p->sendMessage("§a[OpalMod] {$target->getName()} a été téléporté à votre position.");
        });

        // Freeze / Unfreeze
        $freezeBtn = $isFrozen ? "§a§lDéfiger (Unfreeze)\n§r§8Libérer le suspect" : "§c§lFiger (Freeze)\n§r§8Bloquer tout mouvement";
        $form->addButton($freezeBtn, null, null, function(Player $p) use ($target, $plugin, $isFrozen) {
            if ($isFrozen) {
                $plugin->unfreeze($target);
                $p->sendMessage("§a[OpalMod] {$target->getName()} a été libéré du freeze.");
            } else {
                $plugin->freeze($target, $p->getName());
                $p->sendMessage("§c[OpalMod] {$target->getName()} a été placé sous freeze !");
            }
        });

        // Avertissement (Warn)
        $form->addButton("§e§lAvertir (Warn)\n§r§8Enregistrer un avertissement", null, null, function(Player $p) use ($target, $plugin) {
            self::openWarnDialog($p, $target->getName(), $target->getXuid(), $plugin);
        });

        // Rendre muet (Mute)
        $form->addButton("§6§lRendre Muet (Mute)\n§r§8Couper le chat du joueur", null, null, function(Player $p) use ($target, $plugin) {
            self::openMuteDialog($p, $target->getName(), $target->getXuid(), $plugin);
        });

        // Expulser (Kick)
        $form->addButton("§d§lExpulser (Kick)\n§r§8Éjecter du serveur", null, null, function(Player $p) use ($target, $plugin) {
            self::openKickDialog($p, $target, $plugin);
        });

        // Bannir (Ban)
        $form->addButton("§4§lBannir (Ban)\n§r§8Exclure temporairement ou définitivement", null, null, function(Player $p) use ($target, $plugin) {
            self::openBanDialog($p, $target->getName(), $target->getXuid(), $plugin);
        });

        $form->addButton("§7« Retour", null, null, function(Player $p) use ($plugin) {
            self::openOnlinePlayersMenu($p, $plugin);
        });

        $staff->sendForm($form);
    }

    public static function openWarnDialog(Player $staff, string $targetName, string $xuid, OpalMod $plugin) : void {
        $form = new CustomForm("§l§eAvertir : {$targetName}");
        $form->addInput("§fMotif de l'avertissement :", "Spam, provocation, non respect...");

        $form->setCallback(function(Player $p, ?array $data) use ($targetName, $xuid, $plugin) {
            if ($data === null) {
                return;
            }
            $reason = trim((string)($data[0] ?? ""));
            if ($reason === "") {
                $reason = "Comportement non conforme";
            }

            $plugin->getDatabase()->addWarn($targetName, $xuid, $p->getName(), $reason);
            $p->sendMessage("§a[OpalMod] Avertissement envoyé à {$targetName} pour : {$reason}");

            $target = Server::getInstance()->getPlayerExact($targetName);
            if ($target !== null && $target->isOnline()) {
                $target->sendMessage("§c§l[AVERTISSEMENT] §r§cVous avez reçu un avertissement de §e{$p->getName()} §c!\n§7Raison : §f{$reason}");
            }
        });

        $staff->sendForm($form);
    }

    public static function openMuteDialog(Player $staff, string $targetName, string $xuid, OpalMod $plugin) : void {
        $form = new CustomForm("§l§6Rendre Muet : {$targetName}");
        $form->addInput("§fMotif du mute :", "Insultes, spam...");
        $form->addDropdown("§fDurée :", ["15 minutes", "1 heure", "12 heures", "1 jour", "7 jours", "Définitif"]);

        $form->setCallback(function(Player $p, ?array $data) use ($targetName, $xuid, $plugin) {
            if ($data === null) {
                return;
            }
            $reason = trim((string)($data[0] ?? ""));
            if ($reason === "") { $reason = "Infraction au chat"; }

            $durationIdx = (int)($data[1] ?? 0);
            $seconds = match($durationIdx) {
                0 => 15 * 60,
                1 => 3600,
                2 => 12 * 3600,
                3 => 86400,
                4 => 7 * 86400,
                default => 0
            };

            $plugin->getDatabase()->addMute($targetName, $xuid, $p->getName(), $reason, $seconds);
            $durText = $seconds > 0 ? "{$seconds}s" : "définitif";
            $p->sendMessage("§a[OpalMod] {$targetName} a été mute ({$durText}) pour : {$reason}");

            $target = Server::getInstance()->getPlayerExact($targetName);
            if ($target !== null && $target->isOnline()) {
                $target->sendMessage("§c§l[MUTE] §r§cVous avez été rendu muet par §e{$p->getName()} §c!\n§7Raison : §f{$reason}");
            }
        });

        $staff->sendForm($form);
    }

    public static function openBanDialog(Player $staff, string $targetName, string $xuid, OpalMod $plugin) : void {
        $form = new CustomForm("§l§4Bannir : {$targetName}");
        $form->addInput("§fMotif du bannissement :", "Triche, refus de vérif, menace...");
        $form->addDropdown("§fDurée :", ["1 heure", "12 heures", "1 jour", "3 jours", "7 jours", "30 jours", "Définitif"]);

        $form->setCallback(function(Player $p, ?array $data) use ($targetName, $xuid, $plugin) {
            if ($data === null) {
                return;
            }
            $reason = trim((string)($data[0] ?? ""));
            if ($reason === "") { $reason = "Infraction grave aux règles"; }

            $durationIdx = (int)($data[1] ?? 0);
            $seconds = match($durationIdx) {
                0 => 3600,
                1 => 12 * 3600,
                2 => 86400,
                3 => 3 * 86400,
                4 => 7 * 86400,
                5 => 30 * 86400,
                default => 0
            };

            $plugin->getDatabase()->addBan($targetName, $xuid, $p->getName(), $reason, $seconds);
            $durText = $seconds > 0 ? "pour " . round($seconds / 86400, 1) . "j" : "définitif";
            $p->sendMessage("§a[OpalMod] {$targetName} a été banni ({$durText}) pour : {$reason}");

            $target = Server::getInstance()->getPlayerExact($targetName);
            if ($target !== null && $target->isOnline()) {
                $target->kick("§c§lVOUS ÊTES BANNI !\n§7Modérateur: §f{$p->getName()}\n§7Raison: §e{$reason}");
            }
        });

        $staff->sendForm($form);
    }

    public static function openKickDialog(Player $staff, Player $target, OpalMod $plugin) : void {
        $targetName = $target->getName();
        $form = new CustomForm("§l§dExpulser : {$targetName}");
        $form->addInput("§fMotif de l'expulsion :", "Merci de corriger votre attitude...");

        $form->setCallback(function(Player $p, ?array $data) use ($target, $plugin) {
            if ($data === null) {
                return;
            }
            $reason = trim((string)($data[0] ?? ""));
            if ($reason === "") { $reason = "Expulsé par un modérateur"; }

            $plugin->getDatabase()->logStaffAction($p->getName(), "KICK", $target->getName(), $reason);
            $target->kick("§c§lEXPULSION DU SERVEUR\n§7Modérateur: §f{$p->getName()}\n§7Raison: §e{$reason}");
            $p->sendMessage("§a[OpalMod] {$target->getName()} a été expulsé.");
        });

        $staff->sendForm($form);
    }

    public static function openReportsMenu(Player $staff, OpalMod $plugin) : void {
        $reports = $plugin->getDatabase()->getPendingReports();

        $form = new SimpleForm(
            "§l§cSignalements des Joueurs",
            count($reports) === 0 ? "§aAucun signalement en attente !" : "§7Sélectionnez un signalement à traiter :"
        );

        foreach ($reports as $rep) {
            $timeStr = date("d/m H:i", (int)$rep["created_at"]);
            $form->addButton(
                "§c#{$rep['id']} - {$rep['reported']}\n§8Par {$rep['reporter']} ({$timeStr})",
                null,
                null,
                function(Player $p) use ($rep, $plugin) {
                    self::openReportDetail($p, $rep, $plugin);
                }
            );
        }

        $form->addButton("§7« Retour", null, null, function(Player $p) use ($plugin) {
            self::openStaffDashboard($p, $plugin);
        });

        $staff->sendForm($form);
    }

    public static function openReportDetail(Player $staff, array $rep, OpalMod $plugin) : void {
        $content = "§l§cSignalement #{$rep['id']}\n\n" .
                   "§7Joueur signalé : §e{$rep['reported']}\n" .
                   "§7Auteur du signalement : §f{$rep['reporter']}\n" .
                   "§7Date : §b" . date("d/m/Y H:i:s", (int)$rep['created_at']) . "\n\n" .
                   "§7Motif indiqué :\n§f{$rep['reason']}\n\n" .
                   "§fSélectionnez l'action à entreprendre :";

        $form = new SimpleForm("§l§cTraitement Signalement #{$rep['id']}", $content);

        $form->addButton("§a§lMarquer comme Traité\n§r§8Clôturer le signalement", null, null, function(Player $p) use ($rep, $plugin) {
            $plugin->getDatabase()->resolveReport((int)$rep['id'], 'TRAITÉ', $p->getName());
            $p->sendMessage("§a[OpalMod] Signalement #{$rep['id']} marqué comme traité.");
        });

        $form->addButton("§e§lRejeter (Faux signalement)\n§r§8Clôturer sans suite", null, null, function(Player $p) use ($rep, $plugin) {
            $plugin->getDatabase()->resolveReport((int)$rep['id'], 'REJETÉ', $p->getName());
            $p->sendMessage("§e[OpalMod] Signalement #{$rep['id']} rejeté.");
        });

        $form->addButton("§7« Retour", null, null, function(Player $p) use ($plugin) {
            self::openReportsMenu($p, $plugin);
        });

        $staff->sendForm($form);
    }

    public static function openLookupForm(Player $staff, OpalMod $plugin) : void {
        $form = new CustomForm("§l§6Dossier Joueur");
        $form->addInput("§fPseudo du joueur :", "Ex: Katsun1246");

        $form->setCallback(function(Player $p, ?array $data) use ($plugin) {
            if ($data === null) { return; }
            $target = trim((string)($data[0] ?? ""));
            if ($target === "") { return; }

            $history = $plugin->getDatabase()->getPlayerHistory($target);
            self::openHistoryDisplay($p, $target, $history, $plugin);
        });

        $staff->sendForm($form);
    }

    public static function openHistoryDisplay(Player $staff, string $target, array $history, OpalMod $plugin) : void {
        $bansCount = count($history["bans"]);
        $mutesCount = count($history["mutes"]);
        $warnsCount = count($history["warns"]);

        $text = "§l§eDossier disciplinaire : §f{$target}\n\n" .
                "§cTotal Bans : §f{$bansCount} §8| §6Total Mutes : §f{$mutesCount} §8| §eTotal Warns : §f{$warnsCount}\n\n";

        $text .= "§e§l--- DERNIERS AVERTISSEMENTS ---\n";
        if ($warnsCount === 0) {
            $text .= "§7Aucun avertissement.\n";
        } else {
            foreach (array_slice($history["warns"], 0, 5) as $w) {
                $d = date("d/m/Y", (int)$w["created_at"]);
                $text .= "§7- [{$d}] par §e{$w['staff_name']}§7 : §f{$w['reason']}\n";
            }
        }

        $text .= "\n§c§l--- DERNIERS BANS ---\n";
        if ($bansCount === 0) {
            $text .= "§7Aucun ban.\n";
        } else {
            foreach (array_slice($history["bans"], 0, 3) as $b) {
                $d = date("d/m/Y", (int)$b["created_at"]);
                $text .= "§7- [{$d}] par §e{$b['staff_name']}§7 : §f{$b['reason']}\n";
            }
        }

        $form = new SimpleForm("§l§6Historique : {$target}", $text);
        $form->addButton("§7« Retour", null, null, function(Player $p) use ($plugin) {
            self::openStaffDashboard($p, $plugin);
        });

        $staff->sendForm($form);
    }

    public static function openLogsMenu(Player $staff, OpalMod $plugin) : void {
        $logs = $plugin->getDatabase()->getRecentLogs(25);

        $text = "§l§b25 Dernières actions du Staff :\n\n";
        foreach ($logs as $l) {
            $d = date("d/m H:i", (int)$l["timestamp"]);
            $text .= "§8[{$d}] §e{$l['staff_name']} §7» §b{$l['action']} §7sur §f{$l['target']} §8({$l['details']})\n";
        }

        $form = new SimpleForm("§l§bLogs Récents Staff", $text);
        $form->addButton("§7« Retour", null, null, function(Player $p) use ($plugin) {
            self::openStaffDashboard($p, $plugin);
        });

        $staff->sendForm($form);
    }
}
