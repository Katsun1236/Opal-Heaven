<?php

declare(strict_types=1);

namespace opal\core\database;

use SQLite3;

class DatabaseManager{

    private SQLite3 $db;

    public function __construct(string $dataFolder){
        // Utiliser le dossier commun pour la synchronisation multi-serveurs
        $sharedDir = dirname($dataFolder, 3) . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR;
        if(!is_dir($sharedDir)){
            @mkdir($sharedDir, 0777, true);
        }
        $dbPath = $sharedDir . "opal_shared.db";
        $this->db = new SQLite3($dbPath);
        $this->db->busyTimeout(10000);
        // Activation WAL pour haute concurrence entre plusieurs serveurs PocketMine
        $this->db->exec("PRAGMA journal_mode = WAL;");
        $this->db->exec("PRAGMA synchronous = NORMAL;");
        $this->initTables();
    }

    private function initTables() : void{
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS players (
                xuid TEXT PRIMARY KEY,
                username TEXT NOT NULL,
                first_join TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_join TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                role TEXT DEFAULT 'JOUEUR',
                money INTEGER DEFAULT 1000,
                paragon_level INTEGER DEFAULT 0,
                paragon_points INTEGER DEFAULT 0
            );

            CREATE TABLE IF NOT EXISTS factions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT UNIQUE NOT NULL,
                description TEXT DEFAULT 'Nouvelle faction d''Opal Heaven',
                leader_xuid TEXT NOT NULL,
                power INTEGER DEFAULT 10,
                max_power INTEGER DEFAULT 50,
                level INTEGER DEFAULT 1,
                bank_balance INTEGER DEFAULT 0,
                heart_world TEXT DEFAULT 'world',
                heart_x REAL DEFAULT 0,
                heart_y REAL DEFAULT 0,
                heart_z REAL DEFAULT 0,
                shield_start_hour INTEGER DEFAULT 18,
                shield_end_hour INTEGER DEFAULT 23,
                is_under_siege INTEGER DEFAULT 0
            );

            CREATE TABLE IF NOT EXISTS faction_members (
                xuid TEXT PRIMARY KEY,
                faction_id INTEGER NOT NULL,
                rank TEXT DEFAULT 'RECRUE'
            );

            CREATE TABLE IF NOT EXISTS faction_claims (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                faction_id INTEGER NOT NULL,
                chunk_x INTEGER NOT NULL,
                chunk_z INTEGER NOT NULL,
                world_name TEXT NOT NULL,
                UNIQUE(world_name, chunk_x, chunk_z)
            );

            CREATE TABLE IF NOT EXISTS player_orders (
                xuid TEXT NOT NULL,
                order_name TEXT NOT NULL,
                level INTEGER DEFAULT 1,
                experience INTEGER DEFAULT 0,
                specialization TEXT DEFAULT '',
                PRIMARY KEY (xuid, order_name)
            );

            CREATE TABLE IF NOT EXISTS bounty_contracts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                creator_xuid TEXT NOT NULL,
                target_xuid TEXT NOT NULL,
                reward INTEGER NOT NULL,
                is_claimed INTEGER DEFAULT 0
            );

            CREATE TABLE IF NOT EXISTS ah_listings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                seller_xuid TEXT NOT NULL,
                seller_name TEXT NOT NULL,
                item_nbt TEXT NOT NULL,
                item_name TEXT NOT NULL,
                category TEXT NOT NULL,
                price INTEGER NOT NULL,
                created_at INTEGER NOT NULL,
                expires_at INTEGER NOT NULL,
                is_sold INTEGER DEFAULT 0
            );

            CREATE TABLE IF NOT EXISTS ah_buy_orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                buyer_xuid TEXT NOT NULL,
                buyer_name TEXT NOT NULL,
                item_id TEXT NOT NULL,
                item_name TEXT NOT NULL,
                amount INTEGER NOT NULL,
                price_per_item INTEGER NOT NULL,
                total_reserved INTEGER NOT NULL,
                created_at INTEGER NOT NULL
            );

            CREATE TABLE IF NOT EXISTS player_quests (
                xuid TEXT NOT NULL,
                quest_id TEXT NOT NULL,
                progress INTEGER DEFAULT 0,
                completed INTEGER DEFAULT 0,
                claimed INTEGER DEFAULT 0,
                PRIMARY KEY (xuid, quest_id)
            );

            CREATE TABLE IF NOT EXISTS player_safeboxes (
                xuid TEXT PRIMARY KEY,
                capacity INTEGER DEFAULT 9,
                items_nbt TEXT DEFAULT '[]'
            );

            CREATE TABLE IF NOT EXISTS combat_stats (
                xuid TEXT PRIMARY KEY,
                kills INTEGER DEFAULT 0,
                deaths INTEGER DEFAULT 0,
                killstreak INTEGER DEFAULT 0,
                best_killstreak INTEGER DEFAULT 0,
                parries_success INTEGER DEFAULT 0
            );
        ");
    }

    public function getDb() : SQLite3{
        return $this->db;
    }

    public function close() : void{
        $this->db->close();
    }
}
