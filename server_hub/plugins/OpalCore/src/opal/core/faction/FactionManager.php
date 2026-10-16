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
        return self::$instance;
    }

    public function createFaction(Player $leader, string $name) : bool{
        $db = $this->database->getDb();
        $leaderXuid = $leader->getXuid();

        if($this->getPlayerFaction($leader) !== null){
            $leader->sendMessage("§c❌ Vous faites déjà partie d'une faction !");
            return false;
        }

        $stmt = $db->prepare("INSERT INTO factions (name, leader_xuid, power, max_power) VALUES (:name, :leader, 10, 50)");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $stmt->bindValue(":leader", $leaderXuid, SQLITE3_TEXT);
        $success = @$stmt->execute();

        if(!$success){
            $leader->sendMessage("§c❌ Ce nom de faction est déjà utilisé.");
            return false;
        }

        $factionId = $db->lastInsertRowID();
        $this->setMemberRank($leaderXuid, $factionId, "CHEF");
        $leader->sendMessage("§a🏰 Faction §e{$name} §acréée avec succès !");
        return true;
    }

    public function disbandFaction(Player $leader) : bool{
        $faction = $this->getPlayerFaction($leader);
        if($faction === null || $faction["rank"] !== "CHEF"){
            $leader->sendMessage("§c❌ Seul le Chef peut dissoudre la faction !");
            return false;
        }

        $id = $faction["id"];
        $db = $this->database->getDb();
        $db->exec("DELETE FROM factions WHERE id = $id");
        $db->exec("DELETE FROM faction_members WHERE faction_id = $id");
        $db->exec("DELETE FROM faction_claims WHERE faction_id = $id");

        $leader->sendMessage("§e🏰 Votre faction a été dissoute.");
        return true;
    }

    public function getPlayerFaction(Player $player) : ?array{
        $xuid = $player->getXuid();
        $db = $this->database->getDb();
        $stmt = $db->prepare("
            SELECT f.*, fm.rank 
            FROM faction_members fm
            JOIN factions f ON f.id = fm.faction_id
            WHERE fm.xuid = :xuid
            LIMIT 1
        ");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $res = $stmt->execute();
        $row = $res->fetchArray(SQLITE3_ASSOC);
        return $row !== false ? $row : null;
    }

    public function getFactionByName(string $name) : ?array{
        $db = $this->database->getDb();
        $stmt = $db->prepare("SELECT * FROM factions WHERE name = :name COLLATE NOCASE LIMIT 1");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $res = $stmt->execute();
        $row = $res->fetchArray(SQLITE3_ASSOC);
        return $row !== false ? $row : null;
    }

    public function setMemberRank(string $xuid, int $factionId, string $rank) : void{
        $db = $this->database->getDb();
        $stmt = $db->prepare("INSERT INTO faction_members (xuid, faction_id, rank) VALUES (:xuid, :fid, :rank)
            ON CONFLICT(xuid) DO UPDATE SET faction_id = :fid, rank = :rank");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $stmt->bindValue(":fid", $factionId, SQLITE3_INTEGER);
        $stmt->bindValue(":rank", $rank, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function removeMember(string $xuid) : void{
        $db = $this->database->getDb();
        $stmt = $db->prepare("DELETE FROM faction_members WHERE xuid = :xuid");
        $stmt->bindValue(":xuid", $xuid, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function getFactionMembers(int $factionId) : array{
        $db = $this->database->getDb();
        $stmt = $db->prepare("SELECT p.username, fm.rank, fm.xuid FROM faction_members fm LEFT JOIN players p ON p.xuid = fm.xuid WHERE fm.faction_id = :fid");
        $stmt->bindValue(":fid", $factionId, SQLITE3_INTEGER);
        $res = $stmt->execute();
        $members = [];
        while($row = $res->fetchArray(SQLITE3_ASSOC)){
            $members[] = $row;
        }
        return $members;
    }

    public function setFactionHome(int $factionId, Position $pos) : void{
        $db = $this->database->getDb();
        $stmt = $db->prepare("UPDATE factions SET heart_world = :w, heart_x = :x, heart_y = :y, heart_z = :z WHERE id = :fid");
        $stmt->bindValue(":w", $pos->getWorld()->getFolderName(), SQLITE3_TEXT);
        $stmt->bindValue(":x", $pos->x, SQLITE3_FLOAT);
        $stmt->bindValue(":y", $pos->y, SQLITE3_FLOAT);
        $stmt->bindValue(":z", $pos->z, SQLITE3_FLOAT);
        $stmt->bindValue(":fid", $factionId, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public function getFactionHome(int $factionId) : ?array{
        $db = $this->database->getDb();
        $stmt = $db->prepare("SELECT heart_world, heart_x, heart_y, heart_z FROM factions WHERE id = :fid");
        $stmt->bindValue(":fid", $factionId, SQLITE3_INTEGER);
        $res = $stmt->execute();
        $row = $res->fetchArray(SQLITE3_ASSOC);
        if($row !== false && $row["heart_world"] !== null){
            return $row;
        }
        return null;
    }

    public function depositBank(int $factionId, int $amount) : void{
        $db = $this->database->getDb();
        $db->exec("UPDATE factions SET bank_balance = bank_balance + $amount WHERE id = $factionId");
    }

    public function withdrawBank(int $factionId, int $amount) : bool{
        $db = $this->database->getDb();
        $stmt = $db->prepare("SELECT bank_balance FROM factions WHERE id = :fid");
        $stmt->bindValue(":fid", $factionId, SQLITE3_INTEGER);
        $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        if($res !== false && $res["bank_balance"] >= $amount){
            $db->exec("UPDATE factions SET bank_balance = bank_balance - $amount WHERE id = $factionId");
            return true;
        }
        return false;
    }

    public function isShieldActive(array $faction) : bool{
        $hour = (int)date("G");
        $start = (int)$faction["shield_start_hour"];
        $end = (int)$faction["shield_end_hour"];
        // Vulnérable de 18h à 23h (durant le siège)
        return !($hour >= $start && $hour < $end);
    }
}
