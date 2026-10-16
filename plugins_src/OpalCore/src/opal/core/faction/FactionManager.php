<?php

declare(strict_types=1);

namespace opal\core\faction;

use opal\core\database\DatabaseManager;
use pocketmine\player\Player;
use pocketmine\world\Position;

class FactionManager{

    private static ?self $instance = null;
    private DatabaseManager $database;

    public function __construct(DatabaseManager $database){
        self::$instance = $this;
        $this->database = $database;
    }

    public static function getInstance() : self{
        return self::$instance ?? throw new \RuntimeException("FactionManager has not been initialized yet.");
    }

    public function createFaction(Player $leader, string $name) : bool{
        $name = trim($name);
        if(strlen($name) < 3 || strlen($name) > 16 || !ctype_alnum(str_replace(['_', '-'], '', $name))){
            $leader->sendMessage("§c❌ Le nom de faction doit contenir entre 3 et 16 caractères alphanumériques.");
            return false;
        }

        $db = $this->database->getDb();
        $leaderKey = !empty($leader->getXuid()) ? $leader->getXuid() : strtolower($leader->getName());

        if($this->getPlayerFaction($leader) !== null){
            $leader->sendMessage("§c❌ Vous faites déjà partie d'une faction !");
            return false;
        }

        $stmt = $db->prepare("INSERT INTO factions (name, leader_xuid, power, max_power) VALUES (:name, :leader, 10, 50)");
        if($stmt === false){
            $leader->sendMessage("§c❌ Erreur interne lors de la création de faction.");
            return false;
        }
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $stmt->bindValue(":leader", $leaderKey, SQLITE3_TEXT);
        $success = @$stmt->execute();

        if(!$success){
            $leader->sendMessage("§c❌ Ce nom de faction est déjà utilisé.");
            return false;
        }

        $factionId = $db->lastInsertRowID();
        $this->setMemberRank($leaderKey, $factionId, "CHEF");
        $leader->sendMessage("§a🏰 Faction §e{$name} §acréée avec succès !");
        return true;
    }

    public function disbandFaction(Player $leader) : bool{
        $faction = $this->getPlayerFaction($leader);
        if($faction === null || $faction["rank"] !== "CHEF"){
            $leader->sendMessage("§c❌ Seul le Chef peut dissoudre la faction !");
            return false;
        }

        $id = (int)$faction["id"];
        $db = $this->database->getDb();
        
        $stmtF = $db->prepare("DELETE FROM factions WHERE id = :id");
        if($stmtF !== false){
            $stmtF->bindValue(":id", $id, SQLITE3_INTEGER);
            $stmtF->execute();
        }
        $stmtM = $db->prepare("DELETE FROM faction_members WHERE faction_id = :id");
        if($stmtM !== false){
            $stmtM->bindValue(":id", $id, SQLITE3_INTEGER);
            $stmtM->execute();
        }
        $stmtC = $db->prepare("DELETE FROM faction_claims WHERE faction_id = :id");
        if($stmtC !== false){
            $stmtC->bindValue(":id", $id, SQLITE3_INTEGER);
            $stmtC->execute();
        }

        $leader->sendMessage("§e🏰 Votre faction a été dissoute.");
        return true;
    }

    public function getPlayerFaction(Player|string $player) : ?array{
        $key = $player instanceof Player
            ? (!empty($player->getXuid()) ? $player->getXuid() : strtolower($player->getName()))
            : $player;

        $db = $this->database->getDb();
        $stmt = $db->prepare("
            SELECT f.*, fm.rank 
            FROM faction_members fm
            JOIN factions f ON f.id = fm.faction_id
            WHERE fm.xuid = :key
            LIMIT 1
        ");
        if($stmt === false) return null;

        $stmt->bindValue(":key", $key, SQLITE3_TEXT);
        $res = $stmt->execute();
        if($res === false) return null;

        $row = $res->fetchArray(SQLITE3_ASSOC);
        return $row !== false ? $row : null;
    }

    public function getFactionByName(string $name) : ?array{
        $db = $this->database->getDb();
        $stmt = $db->prepare("SELECT * FROM factions WHERE name = :name COLLATE NOCASE LIMIT 1");
        if($stmt === false) return null;

        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $res = $stmt->execute();
        if($res === false) return null;

        $row = $res->fetchArray(SQLITE3_ASSOC);
        return $row !== false ? $row : null;
    }

    public function setMemberRank(string $xuid, int $factionId, string $rank) : void{
        $db = $this->database->getDb();
        $stmt = $db->prepare("INSERT INTO faction_members (xuid, faction_id, rank) VALUES (:xuid, :fid, :rank)
            ON CONFLICT(xuid) DO UPDATE SET faction_id = :fid, rank = :rank");
        if($stmt === false) return;

        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $stmt->bindValue(":fid", $factionId, SQLITE3_INTEGER);
        $stmt->bindValue(":rank", $rank, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function removeMember(string $xuid) : void{
        $db = $this->database->getDb();
        $stmt = $db->prepare("DELETE FROM faction_members WHERE xuid = :xuid");
        if($stmt === false) return;

        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function getFactionMembers(int $factionId) : array{
        $db = $this->database->getDb();
        $stmt = $db->prepare("SELECT COALESCE(p.username, fm.xuid) as username, fm.rank, fm.xuid 
            FROM faction_members fm 
            LEFT JOIN players p ON p.xuid = fm.xuid 
            WHERE fm.faction_id = :fid");
        if($stmt === false) return [];

        $stmt->bindValue(":fid", $factionId, SQLITE3_INTEGER);
        $res = $stmt->execute();
        if($res === false) return [];

        $members = [];
        while($row = $res->fetchArray(SQLITE3_ASSOC)){
            $members[] = $row;
        }
        return $members;
    }

    public function setFactionHome(int $factionId, Position $pos) : bool{
        $world = $pos->getWorld();
        if($world === null) return false;

        $db = $this->database->getDb();
        $stmt = $db->prepare("UPDATE factions SET heart_world = :w, heart_x = :x, heart_y = :y, heart_z = :z WHERE id = :fid");
        if($stmt === false) return false;

        $stmt->bindValue(":w", $world->getFolderName(), SQLITE3_TEXT);
        $stmt->bindValue(":x", $pos->x, SQLITE3_FLOAT);
        $stmt->bindValue(":y", $pos->y, SQLITE3_FLOAT);
        $stmt->bindValue(":z", $pos->z, SQLITE3_FLOAT);
        $stmt->bindValue(":fid", $factionId, SQLITE3_INTEGER);
        $stmt->execute();
        return true;
    }

    public function getFactionHome(int $factionId) : ?array{
        $db = $this->database->getDb();
        $stmt = $db->prepare("SELECT heart_world, heart_x, heart_y, heart_z FROM factions WHERE id = :fid");
        if($stmt === false) return null;

        $stmt->bindValue(":fid", $factionId, SQLITE3_INTEGER);
        $res = $stmt->execute();
        if($res === false) return null;

        $row = $res->fetchArray(SQLITE3_ASSOC);
        if($row !== false && $row["heart_world"] !== null){
            return $row;
        }
        return null;
    }

    public function depositBank(int $factionId, int $amount) : bool{
        if($amount <= 0) return false;

        $db = $this->database->getDb();
        $stmt = $db->prepare("UPDATE factions SET bank_balance = bank_balance + :amount WHERE id = :fid");
        if($stmt === false) return false;

        $stmt->bindValue(":amount", $amount, SQLITE3_INTEGER);
        $stmt->bindValue(":fid", $factionId, SQLITE3_INTEGER);
        $stmt->execute();
        return $db->changes() > 0;
    }

    public function withdrawBank(int $factionId, int $amount) : bool{
        if($amount <= 0) return false;

        $db = $this->database->getDb();
        // Atomic update prevents race conditions / double withdrawals
        $stmt = $db->prepare("UPDATE factions SET bank_balance = bank_balance - :amount WHERE id = :fid AND bank_balance >= :amount");
        if($stmt === false) return false;

        $stmt->bindValue(":amount", $amount, SQLITE3_INTEGER);
        $stmt->bindValue(":fid", $factionId, SQLITE3_INTEGER);
        $stmt->execute();

        return $db->changes() > 0;
    }

    public function isShieldActive(array $faction) : bool{
        $hour = (int)date("G");
        $start = (int)($faction["shield_start_hour"] ?? 18);
        $end = (int)($faction["shield_end_hour"] ?? 23);
        // Vulnérable durant la plage de siège (ex: 18h à 23h)
        if($start < $end){
            return !($hour >= $start && $hour < $end);
        }
        // Plage traversant minuit (ex: 22h à 04h)
        return !($hour >= $start || $hour < $end);
    }
}
