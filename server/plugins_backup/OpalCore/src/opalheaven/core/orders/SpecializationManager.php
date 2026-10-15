<?php

declare(strict_types=1);

namespace opalheaven\core\orders;

use pocketmine\player\Player;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\world\particle\FlameParticle;
use pocketmine\world\particle\EnchantParticle;
use pocketmine\world\particle\CriticalParticle;
use pocketmine\world\particle\PortalParticle;
use pocketmine\world\particle\DustParticle;
use pocketmine\color\Color;
use pocketmine\world\sound\ExplodeSound;
use pocketmine\world\sound\EndermanTeleportSound;
use pocketmine\world\sound\BellSound;
use pocketmine\world\sound\GhastShootSound;
use pocketmine\math\Vector3;

class SpecializationManager {

    /** @var array<string, SpecializationType> Active spec per player XUID */
    private array $activeSpecialization = [];

    /** @var array<string, array<string, int>> Cooldowns: [xuid => [spec_key => timestamp]] */
    private array $cooldowns = [];

    /** @var array<string, int> Targeted rupture buff expiration for Bretteur */
    private array $targetedRuptureActive = [];

    private \SQLite3 $db;

    public function __construct(\SQLite3 $db) {
        $this->db = $db;
        $this->db->exec("CREATE TABLE IF NOT EXISTS player_specializations (
            xuid TEXT PRIMARY KEY,
            spec_name TEXT NOT NULL,
            assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function loadPlayer(Player $player) : void {
        $xuid = $player->getXuid();
        $stmt = $this->db->prepare("SELECT spec_name FROM player_specializations WHERE xuid = :xuid");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $res = $stmt->execute();
        if ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $this->activeSpecialization[$xuid] = SpecializationType::tryFrom($row["spec_name"]);
        } else {
            $this->activeSpecialization[$xuid] = null;
        }
    }

    public function getSpecialization(Player $player) : ?SpecializationType {
        return $this->activeSpecialization[$player->getXuid()] ?? null;
    }

    public function setSpecialization(Player $player, SpecializationType $spec) : void {
        $xuid = $player->getXuid();
        $this->activeSpecialization[$xuid] = $spec;

        $stmt = $this->db->prepare("INSERT OR REPLACE INTO player_specializations (xuid, spec_name) VALUES (:xuid, :spec_name)");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $stmt->bindValue(":spec_name", $spec->value, SQLITE3_TEXT);
        $stmt->execute();

        $player->sendTitle("§d" . $spec->getDisplayName(), "§7" . $spec->getSubtitle(), 10, 40, 10);
        $player->sendMessage("§d[Spécialisation] §fVous avez choisi la voie : " . $spec->getDisplayName() . " §f!");
        $player->sendMessage("§ePassif : §6" . $spec->getPassiveName() . " §7— " . $spec->getPassiveDescription());
        $player->sendMessage("§bActif : §3" . $spec->getActiveName() . " §7— " . $spec->getActiveDescription());
    }

    public function getRemainingCooldown(Player $player, SpecializationType $spec) : int {
        $xuid = $player->getXuid();
        $expires = $this->cooldowns[$xuid][$spec->value] ?? 0;
        return max(0, $expires - time());
    }

    public function setCooldown(Player $player, SpecializationType $spec) : void {
        $this->cooldowns[$player->getXuid()][$spec->value] = time() + $spec->getActiveCooldown();
    }

    public function isTargetedRuptureActive(Player $player) : bool {
        $xuid = $player->getXuid();
        return isset($this->targetedRuptureActive[$xuid]) && $this->targetedRuptureActive[$xuid] >= microtime(true);
    }

    public function consumeTargetedRupture(Player $player) : void {
        unset($this->targetedRuptureActive[$player->getXuid()]);
    }

    /**
     * Tente de déclencher la compétence active de la spécialisation du joueur
     */
    public function triggerActiveSkill(Player $player) : bool {
        $spec = $this->getSpecialization($player);
        if ($spec === null) {
            $player->sendActionBarMessage("§cVous n'avez pas encore choisi de spécialisation (/ordre).");
            return false;
        }

        $rem = $this->getRemainingCooldown($player, $spec);
        if ($rem > 0) {
            $player->sendActionBarMessage("§cCompétence en recharge : §e{$rem}s restantes §c!");
            return false;
        }

        $world = $player->getWorld();
        $pos = $player->getPosition();

        switch ($spec) {
            case SpecializationType::BRETTEUR:
                // Rupture Ciblée : La prochaine attaque dans les 3s ignore 50% armure et +35% posture
                $this->targetedRuptureActive[$player->getXuid()] = microtime(true) + 3.0;
                $this->setCooldown($player, $spec);
                $world->addSound($pos, new BellSound());
                for ($i = 0; $i < 15; $i++) {
                    $world->addParticle($pos->add(0, 1.2, 0), new CriticalParticle());
                }
                $player->sendTitle("§c§lRUPTURE CIBLÉE !", "§eProchaine attaque perfore l'armure (3s) !", 5, 20, 5);
                $player->sendActionBarMessage("§c§l[BRETTEUR] §r§fRupture Ciblée active ! Frappez !");
                return true;

            case SpecializationType::BERSERKER:
                // Cri de Guerre : Onde de choc 5 blocs repousse ennemis de 3.5b + Speed II & Resistance I 8s
                $this->setCooldown($player, $spec);
                $world->addSound($pos, new ExplodeSound());
                for ($angle = 0; $angle < 360; $angle += 20) {
                    $rad = deg2rad($angle);
                    $px = $pos->x + cos($rad) * 4.0;
                    $pz = $pos->z + sin($rad) * 4.0;
                    $world->addParticle(new Vector3($px, $pos->y + 0.5, $pz), new FlameParticle());
                }
                foreach ($world->getNearbyEntities($player->getBoundingBox()->expandedCopy(5.0, 3.0, 5.0)) as $entity) {
                    if ($entity instanceof Player && $entity->getId() !== $player->getId()) {
                        $diff = $entity->getPosition()->subtractVector($pos)->normalize();
                        $entity->setMotion($diff->multiply(1.8)->add(0, 0.4, 0));
                        $entity->sendActionBarMessage("§c§l[CRI DE GUERRE] §r§7Repoussé par la furie du Berserker !");
                    }
                }
                $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 8 * 20, 1, false));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 8 * 20, 0, false));
                $player->sendTitle("§4§lCRI DE GUERRE !", "§cSpeed II & Resistance I octroyés !", 5, 25, 5);
                return true;

            case SpecializationType::TRAQUEUR:
                // Pas de l'Ombre : Blink 5 blocs en avant / dos de cible + 1s invisibilité
                $this->setCooldown($player, $spec);
                $dir = $player->getDirectionVector();
                $targetPos = $pos->addVector($dir->multiply(5.0));
                $world->addSound($pos, new EndermanTeleportSound());
                for ($i = 0; $i < 15; $i++) {
                    $world->addParticle($pos->add(0, 1, 0), new PortalParticle());
                }
                $player->teleport($targetPos);
                $player->getEffects()->add(new EffectInstance(VanillaEffects::INVISIBILITY(), 1 * 20, 0, false));
                $world->addSound($targetPos, new EndermanTeleportSound());
                $player->sendActionBarMessage("§8§l[PAS DE L'OMBRE] §r§7Vous avez surgi dans les ombres !");
                return true;

            case SpecializationType::SISMOLOGUE:
                // Fissure Sismique : Onde tellurique 10 blocs, Slowness III 4s
                $this->setCooldown($player, $spec);
                $world->addSound($pos, new ExplodeSound());
                $dir = $player->getDirectionVector()->setComponents($player->getDirectionVector()->x, 0, $player->getDirectionVector()->z)->normalize();
                for ($d = 1; $d <= 10; $d++) {
                    $stepPos = $pos->addVector($dir->multiply($d));
                    $world->addParticle($stepPos, new DustParticle(new Color(139, 69, 19)));
                }
                foreach ($world->getNearbyEntities($player->getBoundingBox()->expandedCopy(8.0, 3.0, 8.0)) as $e) {
                    if ($e instanceof Player && $e->getId() !== $player->getId()) {
                        $e->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 4 * 20, 2, false));
                        $e->sendActionBarMessage("§9§l[FISSURE SISMIQUE] §r§7La terre tremble sous vos pieds (Slowness III) !");
                    }
                }
                $player->sendActionBarMessage("§9§l[SISMOLOGUE] §r§fFissure Sismique déployée avec succès !");
                return true;

            case SpecializationType::PROSPECTEUR:
                // Résonance Spectrale : Révèle diamants/débris/opales dans 12 blocs pendant 10s
                $this->setCooldown($player, $spec);
                $world->addSound($pos, new BellSound());
                $bx = (int)$pos->x;
                $by = (int)$pos->y;
                $bz = (int)$pos->z;
                $found = 0;
                for ($x = $bx - 12; $x <= $bx + 12; $x += 2) {
                    for ($y = max(-64, $by - 8); $y <= min(319, $by + 8); $y += 2) {
                        for ($z = $bz - 12; $z <= $bz + 12; $z += 2) {
                            $block = $world->getBlockAt($x, $y, $z);
                            $bname = strtolower($block->getName());
                            if (str_contains($bname, "diamond") || str_contains($bname, "debris") || str_contains($bname, "opal") || str_contains($bname, "emerald")) {
                                $world->addParticle(new Vector3($x + 0.5, $y + 0.5, $z + 0.5), new EnchantParticle());
                                $found++;
                            }
                        }
                    }
                }
                $player->sendActionBarMessage("§b§l[RÉSONANCE] §r§f{$found} veines précieuses localisées dans la roche !");
                return true;

            case SpecializationType::TERRASCULPTEUR:
                // Frénésie Tellurique : Haste III & Speed II pendant 20s
                $this->setCooldown($player, $spec);
                $world->addSound($pos, new GhastShootSound());
                $player->getEffects()->add(new EffectInstance(VanillaEffects::HASTE(), 20 * 20, 2, false));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 20, 1, false));
                $player->sendTitle("§6§lFRÉNÉSIE TELLURIQUE !", "§eHaste III & Speed II actifs pendant 20s !", 5, 30, 5);
                return true;

            case SpecializationType::BOTANISTE:
                // Aura de Chlorophylle : Totem de sève 15x15, croissance x4 pendant 30s
                $this->setCooldown($player, $spec);
                $world->addSound($pos, new BellSound());
                for ($i = 0; $i < 20; $i++) {
                    $world->addParticle($pos->add(rand(-4, 4), rand(0, 2), rand(-4, 4)), new DustParticle(new Color(46, 204, 113)));
                }
                $player->sendTitle("§a§lAURA DE CHLOROPHYLLE", "§fCroissance végétale dopée de 400% (30s) !", 5, 30, 5);
                return true;

            case SpecializationType::MAITRE_DES_BETES:
                // Sifflet de Rappel : Loup Gardien Céleste spectral
                $this->setCooldown($player, $spec);
                $world->addSound($pos, new BellSound());
                $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 20, 0, false));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 20, 0, false));
                $player->sendTitle("§2§lGARDE MEUTE !", "§aVigueur protectrice activée !", 5, 25, 5);
                return true;

            case SpecializationType::APOTHICAIRE:
                // Nuage Vivifiant : Brume verte 6 blocs soignant 4 HP/s pendant 8s
                $this->setCooldown($player, $spec);
                $world->addSound($pos, new BellSound());
                for ($i = 0; $i < 20; $i++) {
                    $world->addParticle($pos->add(rand(-3, 3), 0.5, rand(-3, 3)), new DustParticle(new Color(0, 255, 128)));
                }
                $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 8 * 20, 1, false));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::ABSORPTION(), 120 * 20, 0, false));
                $player->sendTitle("§d§lNUAGE VIVIFIANT", "§fRégénération intensive 4 HP/s active !", 5, 25, 5);
                return true;
        }

        return false;
    }
}
