<?php

declare(strict_types=1);

namespace opalheaven\mod\database;

use SQLite3;

class ModDatabase {

    private SQLite3 $db;

    public function __construct(string $dbPath) {
        $this->db = new SQLite3($dbPath);
        $this->initTables();
    }

    private function initTables() : void {
        $this->db->exec("CREATE TABLE IF NOT EXISTS bans (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            player_name TEXT NOT NULL COLLATE NOCASE,
            xuid TEXT NOT NULL,
            staff_name TEXT NOT NULL,
            reason TEXT NOT NULL,
            expires_at INTEGER NOT NULL DEFAULT 0,
            created_at INTEGER NOT NULL,
            active INTEGER NOT NULL DEFAULT 1
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS mutes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            player_name TEXT NOT NULL COLLATE NOCASE,
            xuid TEXT NOT NULL,
            staff_name TEXT NOT NULL,
            reason TEXT NOT NULL,
            expires_at INTEGER NOT NULL DEFAULT 0,
            created_at INTEGER NOT NULL,
            active INTEGER NOT NULL DEFAULT 1
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS warns (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            player_name TEXT NOT NULL COLLATE NOCASE,
            xuid TEXT NOT NULL,
            staff_name TEXT NOT NULL,
            reason TEXT NOT NULL,
            created_at INTEGER NOT NULL
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS reports (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            reporter TEXT NOT NULL COLLATE NOCASE,
            reported TEXT NOT NULL COLLATE NOCASE,
            reason TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'EN_ATTENTE',
            created_at INTEGER NOT NULL
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS staff_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            staff_name TEXT NOT NULL COLLATE NOCASE,
            action TEXT NOT NULL,
            target TEXT NOT NULL COLLATE NOCASE,
            details TEXT NOT NULL,
            timestamp INTEGER NOT NULL
        )");
    }

    public function addBan(string $playerName, string $xuid, string $staffName, string $reason, int $durationSeconds = 0) : int {
        $now = time();
        $expires = $durationSeconds > 0 ? ($now + $durationSeconds) : 0;

        // Désactiver d'anciens bans pour le même joueur
        $stmt = $this->db->prepare("UPDATE bans SET active = 0 WHERE (player_name = :p OR xuid = :x) AND active = 1");
        $stmt->bindValue(":p", $playerName, SQLITE3_TEXT);
        $stmt->bindValue(":x", $xuid, SQLITE3_TEXT);
        $stmt->execute();

        $stmt = $this->db->prepare("INSERT INTO bans (player_name, xuid, staff_name, reason, expires_at, created_at, active) VALUES (:p, :x, :s, :r, :e, :c, 1)");
        $stmt->bindValue(":p", $playerName, SQLITE3_TEXT);
        $stmt->bindValue(":x", $xuid, SQLITE3_TEXT);
        $stmt->bindValue(":s", $staffName, SQLITE3_TEXT);
        $stmt->bindValue(":r", $reason, SQLITE3_TEXT);
        $stmt->bindValue(":e", $expires, SQLITE3_INTEGER);
        $stmt->bindValue(":c", $now, SQLITE3_INTEGER);
        $stmt->execute();

        $this->logStaffAction($staffName, "BAN", $playerName, "Durée: " . ($durationSeconds > 0 ? "{$durationSeconds}s" : "PERM") . " | Motif: {$reason}");
        return (int) $this->db->lastInsertRowID();
    }

    public function unban(string $playerName, string $staffName) : bool {
        $stmt = $this->db->prepare("UPDATE bans SET active = 0 WHERE player_name = :p AND active = 1");
        $stmt->bindValue(":p", $playerName, SQLITE3_TEXT);
        $res = $stmt->execute();
        $changed = $this->db->changes() > 0;
        if ($changed) {
            $this->logStaffAction($staffName, "UNBAN", $playerName, "Débannissement exécuté");
        }
        return $changed;
    }

    public function getActiveBan(string $playerName, string $xuid) : ?array {
        $now = time();
        $stmt = $this->db->prepare("SELECT * FROM bans WHERE (player_name = :p OR (xuid != '' AND xuid = :x)) AND active = 1 ORDER BY id DESC LIMIT 1");
        $stmt->bindValue(":p", $playerName, SQLITE3_TEXT);
        $stmt->bindValue(":x", $xuid, SQLITE3_TEXT);
        $res = $stmt->execute();
        if ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $expires = (int) $row["expires_at"];
            if ($expires > 0 && $now >= $expires) {
                // Ban expiré
                $this->db->exec("UPDATE bans SET active = 0 WHERE id = " . (int)$row["id"]);
                return null;
            }
            return $row;
        }
        return null;
    }

    public function addMute(string $playerName, string $xuid, string $staffName, string $reason, int $durationSeconds = 0) : int {
        $now = time();
        $expires = $durationSeconds > 0 ? ($now + $durationSeconds) : 0;

        $stmt = $this->db->prepare("UPDATE mutes SET active = 0 WHERE (player_name = :p OR xuid = :x) AND active = 1");
        $stmt->bindValue(":p", $playerName, SQLITE3_TEXT);
        $stmt->bindValue(":x", $xuid, SQLITE3_TEXT);
        $stmt->execute();

        $stmt = $this->db->prepare("INSERT INTO mutes (player_name, xuid, staff_name, reason, expires_at, created_at, active) VALUES (:p, :x, :s, :r, :e, :c, 1)");
        $stmt->bindValue(":p", $playerName, SQLITE3_TEXT);
        $stmt->bindValue(":x", $xuid, SQLITE3_TEXT);
        $stmt->bindValue(":s", $staffName, SQLITE3_TEXT);
        $stmt->bindValue(":r", $reason, SQLITE3_TEXT);
        $stmt->bindValue(":e", $expires, SQLITE3_INTEGER);
        $stmt->bindValue(":c", $now, SQLITE3_INTEGER);
        $stmt->execute();

        $this->logStaffAction($staffName, "MUTE", $playerName, "Durée: " . ($durationSeconds > 0 ? "{$durationSeconds}s" : "PERM") . " | Motif: {$reason}");
        return (int) $this->db->lastInsertRowID();
    }

    public function unmute(string $playerName, string $staffName) : bool {
        $stmt = $this->db->prepare("UPDATE mutes SET active = 0 WHERE player_name = :p AND active = 1");
        $stmt->bindValue(":p", $playerName, SQLITE3_TEXT);
        $res = $stmt->execute();
        $changed = $this->db->changes() > 0;
        if ($changed) {
            $this->logStaffAction($staffName, "UNMUTE", $playerName, "Démutage exécuté");
        }
        return $changed;
    }

    public function getActiveMute(string $playerName, string $xuid) : ?array {
        $now = time();
        $stmt = $this->db->prepare("SELECT * FROM mutes WHERE (player_name = :p OR (xuid != '' AND xuid = :x)) AND active = 1 ORDER BY id DESC LIMIT 1");
        $stmt->bindValue(":p", $playerName, SQLITE3_TEXT);
        $stmt->bindValue(":x", $xuid, SQLITE3_TEXT);
        $res = $stmt->execute();
        if ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $expires = (int) $row["expires_at"];
            if ($expires > 0 && $now >= $expires) {
                $this->db->exec("UPDATE mutes SET active = 0 WHERE id = " . (int)$row["id"]);
                return null;
            }
            return $row;
        }
        return null;
    }

    public function addWarn(string $playerName, string $xuid, string $staffName, string $reason) : int {
        $now = time();
        $stmt = $this->db->prepare("INSERT INTO warns (player_name, xuid, staff_name, reason, created_at) VALUES (:p, :x, :s, :r, :c)");
        $stmt->bindValue(":p", $playerName, SQLITE3_TEXT);
        $stmt->bindValue(":x", $xuid, SQLITE3_TEXT);
        $stmt->bindValue(":s", $staffName, SQLITE3_TEXT);
        $stmt->bindValue(":r", $reason, SQLITE3_TEXT);
        $stmt->bindValue(":c", $now, SQLITE3_INTEGER);
        $stmt->execute();

        $this->logStaffAction($staffName, "WARN", $playerName, "Avertissement : {$reason}");
        return (int) $this->db->lastInsertRowID();
    }

    public function getWarns(string $playerName) : array {
        $stmt = $this->db->prepare("SELECT * FROM warns WHERE player_name = :p ORDER BY id DESC");
        $stmt->bindValue(":p", $playerName, SQLITE3_TEXT);
        $res = $stmt->execute();
        $warns = [];
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $warns[] = $row;
        }
        return $warns;
    }

    public function removeWarn(int $warnId, string $staffName) : bool {
        $stmt = $this->db->prepare("DELETE FROM warns WHERE id = :id");
        $stmt->bindValue(":id", $warnId, SQLITE3_INTEGER);
        $stmt->execute();
        $changed = $this->db->changes() > 0;
        if ($changed) {
            $this->logStaffAction($staffName, "UNWARN", "ID #{$warnId}", "Avertissement retiré");
        }
        return $changed;
    }

    public function addReport(string $reporter, string $reported, string $reason) : int {
        $now = time();
        $stmt = $this->db->prepare("INSERT INTO reports (reporter, reported, reason, status, created_at) VALUES (:rep, :trg, :r, 'EN_ATTENTE', :c)");
        $stmt->bindValue(":rep", $reporter, SQLITE3_TEXT);
        $stmt->bindValue(":trg", $reported, SQLITE3_TEXT);
        $stmt->bindValue(":r", $reason, SQLITE3_TEXT);
        $stmt->bindValue(":c", $now, SQLITE3_INTEGER);
        $stmt->execute();
        return (int) $this->db->lastInsertRowID();
    }

    public function getPendingReports() : array {
        $res = $this->db->query("SELECT * FROM reports WHERE status = 'EN_ATTENTE' ORDER BY id DESC LIMIT 50");
        $reports = [];
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $reports[] = $row;
        }
        return $reports;
    }

    public function resolveReport(int $reportId, string $status, string $staffName) : void {
        $stmt = $this->db->prepare("UPDATE reports SET status = :s WHERE id = :id");
        $stmt->bindValue(":s", $status, SQLITE3_TEXT);
        $stmt->bindValue(":id", $reportId, SQLITE3_INTEGER);
        $stmt->execute();
        $this->logStaffAction($staffName, "REPORT_RESOLVE", "Report #{$reportId}", "Statut passé à : {$status}");
    }

    public function logStaffAction(string $staffName, string $action, string $target, string $details) : void {
        $stmt = $this->db->prepare("INSERT INTO staff_logs (staff_name, action, target, details, timestamp) VALUES (:s, :a, :t, :d, :ts)");
        $stmt->bindValue(":s", $staffName, SQLITE3_TEXT);
        $stmt->bindValue(":a", $action, SQLITE3_TEXT);
        $stmt->bindValue(":t", $target, SQLITE3_TEXT);
        $stmt->bindValue(":d", $details, SQLITE3_TEXT);
        $stmt->bindValue(":ts", time(), SQLITE3_INTEGER);
        $stmt->execute();
    }

    public function getRecentLogs(int $limit = 50) : array {
        $res = $this->db->query("SELECT * FROM staff_logs ORDER BY id DESC LIMIT " . (int)$limit);
        $logs = [];
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $logs[] = $row;
        }
        return $logs;
    }

    public function getPlayerHistory(string $playerName) : array {
        $bansStmt = $this->db->prepare("SELECT * FROM bans WHERE player_name = :p ORDER BY id DESC");
        $bansStmt->bindValue(":p", $playerName, SQLITE3_TEXT);
        $bansRes = $bansStmt->execute();
        $bans = [];
        while ($r = $bansRes->fetchArray(SQLITE3_ASSOC)) { $bans[] = $r; }

        $mutesStmt = $this->db->prepare("SELECT * FROM mutes WHERE player_name = :p ORDER BY id DESC");
        $mutesStmt->bindValue(":p", $playerName, SQLITE3_TEXT);
        $mutesRes = $mutesStmt->execute();
        $mutes = [];
        while ($r = $mutesRes->fetchArray(SQLITE3_ASSOC)) { $mutes[] = $r; }

        $warns = $this->getWarns($playerName);

        return [
            "bans" => $bans,
            "mutes" => $mutes,
            "warns" => $warns
        ];
    }
}
