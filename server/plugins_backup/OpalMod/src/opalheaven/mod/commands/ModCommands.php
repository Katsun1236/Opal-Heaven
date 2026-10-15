<?php

declare(strict_types=1);

namespace opalheaven\mod\commands;

use opalheaven\mod\OpalMod;
use opalheaven\mod\ui\StaffFormUI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use function implode;
use function array_shift;
use function count;
use function is_numeric;
use function substr;

class StaffCommand extends Command {
    public function __construct(private OpalMod $plugin) {
        parent::__construct("staff", "Ouvrir le menu de modération OpalMod", "/staff", ["mod"]);
        $this->setPermission("opalmod.staff");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (!($sender instanceof Player)) {
            $sender->sendMessage("§cCette commande doit être exécutée en jeu.");
            return;
        }
        StaffFormUI::openStaffDashboard($sender, $this->plugin);
    }
}

class BanCommand extends Command {
    public function __construct(private OpalMod $plugin) {
        parent::__construct("ban", "Bannir un joueur", "/ban <joueur> [durée: 1d/12h] [raison]");
        $this->setPermission("opalmod.ban");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 1) {
            $sender->sendMessage("§cUtilisation: /ban <joueur> [durée] [raison]");
            return;
        }

        $targetName = array_shift($args);
        $duration = 0;
        if (count($args) > 0 && preg_match('/^(\d+)([smhdj])$/i', $args[0], $m)) {
            $val = (int)$m[1];
            $unit = strtolower($m[2]);
            $duration = match($unit) {
                's' => $val,
                'm' => $val * 60,
                'h' => $val * 3600,
                'd', 'j' => $val * 86400,
                default => 0
            };
            array_shift($args);
        }

        $reason = count($args) > 0 ? implode(" ", $args) : "Banni par un modérateur";
        $target = Server::getInstance()->getPlayerExact($targetName);
        $xuid = $target !== null ? $target->getXuid() : "";

        $this->plugin->getDatabase()->addBan($targetName, $xuid, $sender->getName(), $reason, $duration);
        $durText = $duration > 0 ? "pour " . round($duration / 86400, 1) . "j" : "définitif";

        Server::getInstance()->broadcastMessage("§c§l[BAN] §r§e{$targetName} §ca été banni ({$durText}) par §f{$sender->getName()} §c! Raison: §7{$reason}");
        if ($target !== null && $target->isOnline()) {
            $target->kick("§c§lVOUS ÊTES BANNI DU SERVEUR !\n§7Modérateur: §f{$sender->getName()}\n§7Raison: §e{$reason}");
        }
    }
}

class UnbanCommand extends Command {
    public function __construct(private OpalMod $plugin) {
        parent::__construct("unban", "Débannir un joueur", "/unban <joueur>");
        $this->setPermission("opalmod.ban");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 1) {
            $sender->sendMessage("§cUtilisation: /unban <joueur>");
            return;
        }

        $targetName = $args[0];
        if ($this->plugin->getDatabase()->unban($targetName, $sender->getName())) {
            $sender->sendMessage("§a[OpalMod] {$targetName} a été débanni avec succès.");
        } else {
            $sender->sendMessage("§c[OpalMod] Aucun ban actif trouvé pour {$targetName}.");
        }
    }
}

class MuteCommand extends Command {
    public function __construct(private OpalMod $plugin) {
        parent::__construct("mute", "Rendre muet un joueur", "/mute <joueur> [durée] [raison]");
        $this->setPermission("opalmod.mute");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 1) {
            $sender->sendMessage("§cUtilisation: /mute <joueur> [durée] [raison]");
            return;
        }

        $targetName = array_shift($args);
        $duration = 0;
        if (count($args) > 0 && preg_match('/^(\d+)([smhdj])$/i', $args[0], $m)) {
            $val = (int)$m[1];
            $unit = strtolower($m[2]);
            $duration = match($unit) {
                's' => $val,
                'm' => $val * 60,
                'h' => $val * 3600,
                'd', 'j' => $val * 86400,
                default => 0
            };
            array_shift($args);
        }

        $reason = count($args) > 0 ? implode(" ", $args) : "Infraction au chat";
        $target = Server::getInstance()->getPlayerExact($targetName);
        $xuid = $target !== null ? $target->getXuid() : "";

        $this->plugin->getDatabase()->addMute($targetName, $xuid, $sender->getName(), $reason, $duration);
        $durText = $duration > 0 ? "pour " . round($duration / 60, 1) . "min" : "définitif";

        Server::getInstance()->broadcastMessage("§6§l[MUTE] §r§e{$targetName} §6a été réduit au silence ({$durText}) par §f{$sender->getName()} §6! Raison: §7{$reason}");
    }
}

class UnmuteCommand extends Command {
    public function __construct(private OpalMod $plugin) {
        parent::__construct("unmute", "Rendre la parole à un joueur", "/unmute <joueur>");
        $this->setPermission("opalmod.mute");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 1) {
            $sender->sendMessage("§cUtilisation: /unmute <joueur>");
            return;
        }

        $targetName = $args[0];
        if ($this->plugin->getDatabase()->unmute($targetName, $sender->getName())) {
            $sender->sendMessage("§a[OpalMod] {$targetName} a été démuté.");
            $target = Server::getInstance()->getPlayerExact($targetName);
            if ($target !== null) {
                $target->sendMessage("§a[OpalMod] Vous pouvez à nouveau parler dans le chat.");
            }
        } else {
            $sender->sendMessage("§c[OpalMod] Aucun mute actif pour {$targetName}.");
        }
    }
}

class WarnCommand extends Command {
    public function __construct(private OpalMod $plugin) {
        parent::__construct("warn", "Avertir un joueur", "/warn <joueur> <raison>");
        $this->setPermission("opalmod.staff");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 2) {
            $sender->sendMessage("§cUtilisation: /warn <joueur> <raison>");
            return;
        }

        $targetName = array_shift($args);
        $reason = implode(" ", $args);
        $target = Server::getInstance()->getPlayerExact($targetName);
        $xuid = $target !== null ? $target->getXuid() : "";

        $this->plugin->getDatabase()->addWarn($targetName, $xuid, $sender->getName(), $reason);
        $sender->sendMessage("§a[OpalMod] Avertissement enregistré pour {$targetName}.");

        if ($target !== null && $target->isOnline()) {
            $target->sendMessage("§c§l[AVERTISSEMENT] §r§cVous avez été averti par §e{$sender->getName()} §c!\n§7Raison : §f{$reason}");
        }
    }
}

class FreezeCommand extends Command {
    public function __construct(private OpalMod $plugin) {
        parent::__construct("freeze", "Figer un joueur pour vérification", "/freeze <joueur>");
        $this->setPermission("opalmod.freeze");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 1) {
            $sender->sendMessage("§cUtilisation: /freeze <joueur>");
            return;
        }

        $target = Server::getInstance()->getPlayerExact($args[0]);
        if ($target === null || !$target->isOnline()) {
            $sender->sendMessage("§cJoueur introuvable ou hors-ligne.");
            return;
        }

        $this->plugin->freeze($target, $sender->getName());
        $sender->sendMessage("§c[OpalMod] {$target->getName()} a été placé sous freeze !");
    }
}

class UnfreezeCommand extends Command {
    public function __construct(private OpalMod $plugin) {
        parent::__construct("unfreeze", "Libérer un joueur du freeze", "/unfreeze <joueur>");
        $this->setPermission("opalmod.freeze");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 1) {
            $sender->sendMessage("§cUtilisation: /unfreeze <joueur>");
            return;
        }

        $target = Server::getInstance()->getPlayerExact($args[0]);
        if ($target === null) {
            $sender->sendMessage("§cJoueur introuvable.");
            return;
        }

        $this->plugin->unfreeze($target);
        $sender->sendMessage("§a[OpalMod] {$target->getName()} a été libéré.");
    }
}

class VanishCommand extends Command {
    public function __construct(private OpalMod $plugin) {
        parent::__construct("vanish", "Devenir totalement invisible", "/vanish", ["v"]);
        $this->setPermission("opalmod.vanish");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (!($sender instanceof Player)) {
            $sender->sendMessage("§cEn jeu uniquement.");
            return;
        }

        $active = $this->plugin->isVanished($sender);
        $this->plugin->setVanished($sender, !$active);
        $sender->sendMessage(!$active ? "§a[OpalMod] Vous êtes maintenant en Vanish (invisible)." : "§c[OpalMod] Vous n'êtes plus en Vanish.");
    }
}

class ReportCommand extends Command {
    public function __construct(private OpalMod $plugin) {
        parent::__construct("report", "Signaler un joueur aux modérateurs", "/report <joueur> <raison>");
        $this->setPermission("opalmod.report");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (count($args) < 2) {
            $sender->sendMessage("§cUtilisation: /report <joueur> <raison détaillée>");
            return;
        }

        $targetName = array_shift($args);
        $reason = implode(" ", $args);

        $id = $this->plugin->getDatabase()->addReport($sender->getName(), $targetName, $reason);
        $sender->sendMessage("§a[Opal Heaven] Merci ! Votre signalement #{$id} concernant {$targetName} a été transmis à l'équipe.");

        // Alerte audio/visuelle aux membres du staff
        foreach (Server::getInstance()->getOnlinePlayers() as $staff) {
            if ($staff->hasPermission("opalmod.staff")) {
                $staff->sendMessage("§c§l[NOUVEAU SIGNALEMENT #{$id}] §r§e{$sender->getName()} §7a signalé §c{$targetName} §7pour : §f{$reason}");
            }
        }
    }
}
