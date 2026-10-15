-- =====================================================================
--                   OPAL HEAVEN - SCHEMA DE BASE DE DONNEES
-- =====================================================================

-- 1. Table des Joueurs (Identification par XUID Bedrock permanent)
CREATE TABLE IF NOT EXISTS players (
    xuid VARCHAR(32) PRIMARY KEY,
    username VARCHAR(32) NOT NULL,
    first_join TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_join TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    role VARCHAR(24) DEFAULT 'JOUEUR',
    money BIGINT DEFAULT 1000,
    paragon_level INT DEFAULT 0,
    paragon_points INT DEFAULT 0,
    INDEX idx_username (username)
);

-- 2. Table des Factions
CREATE TABLE IF NOT EXISTS factions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(24) UNIQUE NOT NULL,
    description VARCHAR(128) DEFAULT 'Nouvelle faction d''Opal Heaven',
    leader_xuid VARCHAR(32) NOT NULL,
    power INT DEFAULT 10,
    max_power INT DEFAULT 50,
    level INT DEFAULT 1,
    bank_balance BIGINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Cœur de Faction & Bouclier Stellaire
    heart_world VARCHAR(32),
    heart_x DOUBLE,
    heart_y DOUBLE,
    heart_z DOUBLE,
    shield_start_hour INT DEFAULT 18, -- 18h00 par défaut
    shield_end_hour INT DEFAULT 23,   -- 23h00 par défaut
    is_under_siege BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (leader_xuid) REFERENCES players(xuid) ON DELETE CASCADE
);

-- 3. Membres de Faction
CREATE TABLE IF NOT EXISTS faction_members (
    xuid VARCHAR(32) PRIMARY KEY,
    faction_id INT NOT NULL,
    rank ENUM('RECRUE', 'MEMBRE', 'OFFICIER', 'CHEF') DEFAULT 'RECRUE',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (xuid) REFERENCES players(xuid) ON DELETE CASCADE,
    FOREIGN KEY (faction_id) REFERENCES factions(id) ON DELETE CASCADE
);

-- 4. Territoires & Claims
CREATE TABLE IF NOT EXISTS faction_claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faction_id INT NOT NULL,
    chunk_x INT NOT NULL,
    chunk_z INT NOT NULL,
    world_name VARCHAR(32) NOT NULL,
    claimed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_claim (world_name, chunk_x, chunk_z),
    FOREIGN KEY (faction_id) REFERENCES factions(id) ON DELETE CASCADE
);

-- 5. Progression dans les 4 Ordres d'Ascension
CREATE TABLE IF NOT EXISTS player_orders (
    xuid VARCHAR(32) NOT NULL,
    order_name ENUM('SANG', 'TERRE', 'ABYSSES', 'FORGE') NOT NULL,
    level INT DEFAULT 1,
    experience BIGINT DEFAULT 0,
    unlocked_talents JSON,
    PRIMARY KEY (xuid, order_name),
    FOREIGN KEY (xuid) REFERENCES players(xuid) ON DELETE CASCADE
);

-- 6. Contrats de Primes & de Mercenariat
CREATE TABLE IF NOT EXISTS bounty_contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    creator_xuid VARCHAR(32) NOT NULL,
    target_xuid VARCHAR(32) NOT NULL,
    reward BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_claimed BOOLEAN DEFAULT FALSE,
    claimed_by_xuid VARCHAR(32),
    FOREIGN KEY (creator_xuid) REFERENCES players(xuid) ON DELETE CASCADE,
    FOREIGN KEY (target_xuid) REFERENCES players(xuid) ON DELETE CASCADE
);
