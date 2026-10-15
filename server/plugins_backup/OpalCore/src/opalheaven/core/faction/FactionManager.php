<?php

declare(strict_types=1);

namespace opalheaven\core\faction;

use pocketmine\player\Player;
use pocketmine\world\World;
use pocketmine\world\Position;
use pocketmine\item\VanillaItems;
use pocketmine\world\sound\ExplodeSound;
use pocketmine\world\particle\HugeExplodeParticle;

class FactionManager {

    private \SQLite3 $db;

    public function __construct(string $dbPath) {
        $this->db = new \SQLite3($dbPath);
        $this->initSchema();
    }

    private function initSchema() : void {
        $this->db->exec("CREATE TABLE IF NOT EXISTS factions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            description TEXT DEFAULT 'Nouvelle faction d''Opal Heaven',
            leader_xuid TEXT NOT NULL,
            power REAL DEFAULT 10.0,
            max_power REAL DEFAULT 50.0,
            faction_level INTEGER DEFAULT 1,
            faction_xp INTEGER DEFAULT 0,
            bank_balance INTEGER DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            heart_world TEXT,
            heart_x REAL,
            heart_y REAL,
            heart_z REAL,
            heart_health INTEGER DEFAULT 10000,
            is_under_siege INTEGER DEFAULT 0,
            siege_expires_at INTEGER
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS faction_members (
            xuid TEXT PRIMARY KEY,
            faction_id INTEGER NOT NULL,
            role TEXT DEFAULT 'RECRUE',
            joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            power_contribution REAL DEFAULT 10.0,
            FOREIGN KEY (faction_id) REFERENCES factions(id) ON DELETE CASCADE
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS faction_claims (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            faction_id INTEGER NOT NULL,
            world_name TEXT NOT NULL,
            chunk_x INTEGER NOT NULL,
            chunk_z INTEGER NOT NULL,
            claimed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE (world_name, chunk_x, chunk_z),
            FOREIGN KEY (faction_id) REFERENCES factions(id) ON DELETE CASCADE
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS faction_tech (
            faction_id INTEGER NOT NULL,
            tech_tier INTEGER NOT NULL,
            unlocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (faction_id, tech_tier)
        )");
    }

    public function createFaction(Player $leader, string $name) : ?Faction {
        $name = trim($name);
        if (strlen($name) < 3 || strlen($name) > 16 || !ctype_alnum($name)) {
            $leader->sendMessage("§cNom de faction invalide (3-16 caractères alphanumériques).");
            return null;
        }

        if ($this->getPlayerFaction($leader) !== null) {
            $leader->sendMessage("§cVous êtes déjà membre d'une faction.");
            return null;
        }

        $stmt = $this->db->prepare("INSERT INTO factions (name, leader_xuid) VALUES (:name, :leader_xuid)");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $stmt->bindValue(":leader_xuid", $leader->getXuid(), SQLITE3_TEXT);
        try {
            $stmt->execute();
        } catch (\Throwable $e) {
            $leader->sendMessage("§cCe nom de faction est déjà utilisé.");
            return null;
        }

        $id = $this->db->lastInsertRowID();

        // Ajout du chef dans faction_members
        $stmt = $this->db->prepare("INSERT INTO faction_members (xuid, faction_id, role) VALUES (:xuid, :fid, 'CHEF')");
        $stmt->bindValue(":xuid", $leader->getXuid(), SQLITE3_TEXT);
        $stmt->bindValue(":fid", $id, SQLITE3_INTEGER);
        $stmt->execute();

        // Débloquer Tier 1 Tech par défaut
        $this->unlockTech($id, 1);

        $leader->sendMessage("§a[Faction] La faction §e{$name} §aa été fondée avec succès !");
        return $this->getFactionById($id);
    }

    public function getFactionById(int $id) : ?Faction {
        $stmt = $this->db->prepare("SELECT * FROM factions WHERE id = :id");
        $stmt->bindValue(":id", $id, SQLITE3_INTEGER);
        $res = $stmt->execute();
        if ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            return new Faction(
                (int)$row["id"],
                $row["name"],
                $row["description"] ?? "",
                $row["leader_xuid"],
                (float)$row["power"],
                (float)$row["max_power"],
                (int)$row["faction_level"],
                (int)$row["faction_xp"],
                (int)$row["bank_balance"],
                $row["heart_world"],
                $row["heart_x"] !== null ? (float)$row["heart_x"] : null,
                $row["heart_y"] !== null ? (float)$row["heart_y"] : null,
                $row["heart_z"] !== null ? (float)$row["heart_z"] : null,
                (int)$row["heart_health"],
                (bool)$row["is_under_siege"],
                $row["siege_expires_at"] !== null ? (int)$row["siege_expires_at"] : null
            );
        }
        return null;
    }

    public function getPlayerFaction(Player $player) : ?Faction {
        $stmt = $this->db->prepare("SELECT faction_id FROM faction_members WHERE xuid = :xuid");
        $stmt->bindValue(":xuid", $player->getXuid(), SQLITE3_TEXT);
        $res = $stmt->execute();
        if ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            return $this->getFactionById((int)$row["faction_id"]);
        }
        return null;
    }

    public function getPlayerRole(Player $player) : ?string {
        $stmt = $this->db->prepare("SELECT role FROM faction_members WHERE xuid = :xuid");
        $stmt->bindValue(":xuid", $player->getXuid(), SQLITE3_TEXT);
        $res = $stmt->execute();
        if ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            return $row["role"];
        }
        return null;
    }

    public function claimChunk(Player $player, Faction $faction) : bool {
        if ($faction->isUnderSiege()) {
            $player->sendMessage("§cImpossible de revendiquer du territoire en période de siège !");
            return false;
        }

        $world = $player->getWorld()->getFolderName();
        $cx = $player->getPosition()->getFloorX() >> 4;
        $cz = $player->getPosition()->getFloorZ() >> 4;

        // Vérifier si le chunk est déjà revendiqué
        $existing = $this->getChunkOwner($world, $cx, $cz);
        if ($existing !== null) {
            $player->sendMessage("§cCe territoire appartient déjà à la faction §e{$existing->name}§c.");
            return false;
        }

        // Vérifier le Power
        $claimCount = $this->getClaimCount($faction->id);
        if ($claimCount >= (int)$faction->power) {
            $player->sendMessage("§cPower insuffisant pour revendiquer plus de chunks ({$claimCount}/" . (int)$faction->power . ").");
            return false;
        }

        $stmt = $this->db->prepare("INSERT INTO faction_claims (faction_id, world_name, chunk_x, chunk_z) VALUES (:fid, :w, :cx, :cz)");
        $stmt->bindValue(":fid", $faction->id, SQLITE3_INTEGER);
        $stmt->bindValue(":w", $world, SQLITE3_TEXT);
        $stmt->bindValue(":cx", $cx, SQLITE3_INTEGER);
        $stmt->bindValue(":cz", $cz, SQLITE3_INTEGER);
        $stmt->execute();

        $player->sendMessage("§a[Faction] Chunk [{$cx}, {$cz}] revendiqué pour §e{$faction->name} §a!");
        return true;
    }

    public function unclaimChunk(Player $player, Faction $faction) : bool {
        if ($faction->isUnderSiege()) {
            $player->sendMessage("§cImpossible d'abandonner du territoire pendant un siège !");
            return false;
        }

        $world = $player->getWorld()->getFolderName();
        $cx = $player->getPosition()->getFloorX() >> 4;
        $cz = $player->getPosition()->getFloorZ() >> 4;

        $stmt = $this->db->prepare("DELETE FROM faction_claims WHERE faction_id = :fid AND world_name = :w AND chunk_x = :cx AND chunk_z = :cz");
        $stmt->bindValue(":fid", $faction->id, SQLITE3_INTEGER);
        $stmt->bindValue(":w", $world, SQLITE3_TEXT);
        $stmt->bindValue(":cx", $cx, SQLITE3_INTEGER);
        $stmt->bindValue(":cz", $cz, SQLITE3_INTEGER);
        $stmt->execute();

        $player->sendMessage("§e[Faction] Territoire [{$cx}, {$cz}] libéré.");
        return true;
    }

    public function getChunkOwner(string $world, int $chunkX, int $chunkZ) : ?Faction {
        $stmt = $this->db->prepare("SELECT faction_id FROM faction_claims WHERE world_name = :w AND chunk_x = :cx AND chunk_z = :cz");
        $stmt->bindValue(":w", $world, SQLITE3_TEXT);
        $stmt->bindValue(":cx", $chunkX, SQLITE3_INTEGER);
        $stmt->bindValue(":cz", $chunkZ, SQLITE3_INTEGER);
        $res = $stmt->execute();
        if ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            return $this->getFactionById((int)$row["faction_id"]);
        }
        return null;
    }

    public function getClaimCount(int $factionId) : int {
        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM faction_claims WHERE faction_id = :fid");
        $stmt->bindValue(":fid", $factionId, SQLITE3_INTEGER);
        $res = $stmt->execute();
        $row = $res->fetchArray(SQLITE3_ASSOC);
        return (int)($row["cnt"] ?? 0);
    }

    public function setFactionHeart(Faction $faction, Position $pos) : void {
        $stmt = $this->db->prepare("UPDATE factions SET heart_world = :w, heart_x = :x, heart_y = :y, heart_z = :z, heart_health = 10000 WHERE id = :id");
        $stmt->bindValue(":w", $pos->getWorld()->getFolderName(), SQLITE3_TEXT);
        $stmt->bindValue(":x", $pos->x, SQLITE3_FLOAT);
        $stmt->bindValue(":y", $pos->y, SQLITE3_FLOAT);
        $stmt->bindValue(":z", $pos->z, SQLITE3_FLOAT);
        $stmt->bindValue(":id", $faction->id, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public function damageHeart(Faction $faction, int $damage, ?Player $attacker, World $world) : void {
        // Dégâts appliqués au Cœur (Livre Blanc Section 6.B)
        $newHealth = max(0, $faction->heartHealth - $damage);
        $faction->heartHealth = $newHealth;

        $stmt = $this->db->prepare("UPDATE factions SET heart_health = :h WHERE id = :id");
        $stmt->bindValue(":h", $newHealth, SQLITE3_INTEGER);
        $stmt->bindValue(":id", $faction->id, SQLITE3_INTEGER);
        $stmt->execute();

        // Chute de la faction si le Cœur est détruit (HeartHealth <= 0)
        if ($newHealth <= 0) {
            $this->triggerFactionFall($faction, $world);
        }
    }

    private function triggerFactionFall(Faction $faction, World $world) : void {
        if ($faction->isHeartSet()) {
            $pos = new Position($faction->heartX, $faction->heartY, $faction->heartZ, $world);
            $world->addSound($pos, new ExplodeSound());
            $world->addParticle($pos, new HugeExplodeParticle());

            // 50% de la banque matérialisée en or au sol
            $goldToDrop = (int)($faction->bankBalance * 0.50);
            if ($goldToDrop > 0) {
                $ingots = min(64, max(1, (int)($goldToDrop / 10)));
                $world->dropItem($pos, VanillaItems::GOLD_INGOT()->setCount($ingots));
            }
        }

        // Libération de tous les claims de la faction
        $stmt = $this->db->prepare("DELETE FROM faction_claims WHERE faction_id = :id");
        $stmt->bindValue(":id", $faction->id, SQLITE3_INTEGER);
        $stmt->execute();

        // Reset du Cœur
        $stmt = $this->db->prepare("UPDATE factions SET heart_world = NULL, heart_health = 0, is_under_siege = 0 WHERE id = :id");
        $stmt->bindValue(":id", $faction->id, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public function triggerSiege(Faction $faction) : void {
        $expires = time() + (30 * 60); // 30 minutes de verrouillage
        $stmt = $this->db->prepare("UPDATE factions SET is_under_siege = 1, siege_expires_at = :exp WHERE id = :id");
        $stmt->bindValue(":exp", $expires, SQLITE3_INTEGER);
        $stmt->bindValue(":id", $faction->id, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public function unlockTech(int $factionId, int $techTier) : void {
        $stmt = $this->db->prepare("INSERT OR IGNORE INTO faction_tech (faction_id, tech_tier) VALUES (:fid, :tier)");
        $stmt->bindValue(":fid", $factionId, SQLITE3_INTEGER);
        $stmt->bindValue(":tier", $techTier, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public function hasTech(int $factionId, int $techTier) : bool {
        $stmt = $this->db->prepare("SELECT 1 FROM faction_tech WHERE faction_id = :fid AND tech_tier = :tier");
        $stmt->bindValue(":fid", $factionId, SQLITE3_INTEGER);
        $stmt->bindValue(":tier", $techTier, SQLITE3_INTEGER);
        $res = $stmt->execute();
        return $res->fetchArray() !== false;
    }

    public function depositBank(Player $player, Faction $faction, int $amount) : bool {
        if ($amount <= 0) return false;
        $faction->bankBalance += $amount;
        $stmt = $this->db->prepare("UPDATE factions SET bank_balance = :b WHERE id = :id");
        $stmt->bindValue(":b", $faction->bankBalance, SQLITE3_INTEGER);
        $stmt->bindValue(":id", $faction->id, SQLITE3_INTEGER);
        $stmt->execute();
        $player->sendMessage("§a[Banque Faction] Déposé : §e{$amount} Or §a(Solde: §f{$faction->bankBalance} Or§a).");
        return true;
    }
}
