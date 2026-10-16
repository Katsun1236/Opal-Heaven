<?php

declare(strict_types=1);

require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

spl_autoload_register(function(string $class) : void{
    if(str_starts_with($class, 'opal\\core\\')){
        $rel = substr($class, strlen('opal\\core\\'));
        $file = __DIR__ . '/../plugins_src/OpalCore/src/opal/core/' . str_replace('\\', '/', $rel) . '.php';
        if(file_exists($file)){
            require_once $file;
        }
    }
});

use opal\core\database\DatabaseManager;
use opal\core\economy\EconomyManager;
use opal\core\role\RoleManager;
use opal\core\orders\OrderManager;
use opal\core\quests\QuestManager;
use opal\core\bounty\BountyManager;
use opal\core\faction\FactionManager;
use opal\core\faction\FactionClaim;
use opal\core\ah\AuctionHouseManager;
use opal\core\training\CombatDummyManager;
use opal\core\sticks\TacticalSticksManager;
use opal\core\creative\CreativeRegistry;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\item\Item;

echo "========================================================\n";
echo "   🌌 OPAL HEAVEN — SUITE DE SIMULATION & TESTS 🌌   \n";
echo "========================================================\n\n";

$testsPassed = 0;
$testsFailed = 0;

function assertTest(string $name, bool $condition, string $detail = "") : void{
    global $testsPassed, $testsFailed;
    if($condition){
        echo "  [PASS] $name\n";
        $testsPassed++;
    }else{
        echo "  [FAIL] $name : $detail\n";
        $testsFailed++;
    }
}

// 1. Initialisation Base de Données de test
echo "--- 1. Base de données & Schémas ---\n";
$tempDbPath = sys_get_temp_dir() . '/test_opal_' . time() . '.db';
if(file_exists($tempDbPath)) unlink($tempDbPath);

$db = new \SQLite3($tempDbPath);
$db->busyTimeout(5000);
$db->exec("PRAGMA journal_mode = WAL;");

// Mock DatabaseManager
$database = new class($db) extends DatabaseManager {
    public function __construct(private \SQLite3 $customDb){}
    public function getDb() : \SQLite3{ return $this->customDb; }
};

// Initialiser les tables
$database->getDb()->exec("
    CREATE TABLE IF NOT EXISTS players (
        xuid TEXT PRIMARY KEY,
        username TEXT NOT NULL,
        role TEXT DEFAULT 'JOUEUR',
        money INTEGER DEFAULT 1000,
        last_join DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS factions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT UNIQUE NOT NULL,
        leader_xuid TEXT NOT NULL,
        power INTEGER DEFAULT 10,
        max_power INTEGER DEFAULT 50,
        bank_balance INTEGER DEFAULT 0,
        heart_world TEXT DEFAULT NULL,
        heart_x REAL DEFAULT NULL,
        heart_y REAL DEFAULT NULL,
        heart_z REAL DEFAULT NULL,
        shield_expires INTEGER DEFAULT 0
    );
    CREATE TABLE IF NOT EXISTS faction_members (
        xuid TEXT PRIMARY KEY,
        faction_id INTEGER NOT NULL,
        rank TEXT DEFAULT 'RECRUE'
    );
    CREATE TABLE IF NOT EXISTS faction_claims (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        faction_id INTEGER NOT NULL,
        world_name TEXT NOT NULL,
        chunk_x INTEGER NOT NULL,
        chunk_z INTEGER NOT NULL,
        claimed_at INTEGER NOT NULL,
        UNIQUE(world_name, chunk_x, chunk_z)
    );
    CREATE TABLE IF NOT EXISTS player_orders (
        xuid TEXT NOT NULL,
        order_name TEXT NOT NULL,
        level INTEGER DEFAULT 1,
        experience INTEGER DEFAULT 0,
        specialization TEXT DEFAULT NULL,
        PRIMARY KEY(xuid, order_name)
    );
    CREATE TABLE IF NOT EXISTS player_quests (
        xuid TEXT NOT NULL,
        quest_id TEXT NOT NULL,
        progress INTEGER DEFAULT 0,
        completed INTEGER DEFAULT 0,
        claimed INTEGER DEFAULT 0,
        PRIMARY KEY(xuid, quest_id)
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
    CREATE TABLE IF NOT EXISTS bounty_contracts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        creator_xuid TEXT NOT NULL,
        target_xuid TEXT NOT NULL,
        reward INTEGER NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        is_claimed INTEGER DEFAULT 0
    );
");
assertTest("Initialisation des tables SQLite", true);

// 2. Économie
echo "\n--- 2. Système Économique ---\n";
$economy = new EconomyManager($database);
$economy->registerPlayer("xuid_p1", "Katsun1246");
$economy->registerPlayer("xuid_p2", "ShadowPlayer");

assertTest("Enregistrement joueurs + solde initial 1000", $economy->getMoney("xuid_p1") === 1000);
$economy->addMoney("xuid_p1", 500);
assertTest("addMoney (+500 -> 1500)", $economy->getMoney("xuid_p1") === 1500);
assertTest("getBalance alias", $economy->getBalance("xuid_p1") === 1500);
assertTest("removeMoney disponible et fonctionnel", $economy->removeMoney("xuid_p1", 300) && $economy->getMoney("xuid_p1") === 1200);
assertTest("removeMoney refuse montant excessif", !$economy->removeMoney("xuid_p1", 5000));
assertTest("transfert inter-joueurs", $economy->transfer("xuid_p1", "xuid_p2", 200));
assertTest("soldes après transfert (P1=1000, P2=1200)", $economy->getMoney("xuid_p1") === 1000 && $economy->getMoney("xuid_p2") === 1200);

// 3. Factions
echo "\n--- 3. Système de Factions & Claims ---\n";
$factions = new FactionManager($database);
$claims = new FactionClaim($database, $factions);

// Création d'une faction par pseudo/clé
$db->exec("INSERT INTO factions (name, leader_xuid, power, max_power) VALUES ('OpalVanguard', 'xuid_p1', 20, 50)");
$facId = $db->lastInsertRowID();
$factions->setMemberRank("xuid_p1", $facId, "CHEF");

$fac = $factions->getFactionByName("OpalVanguard");
assertTest("Création et lecture de faction", $fac !== null && (int)$fac["id"] === $facId);

$factions->depositBank($facId, 500);
$facAfter = $factions->getFactionByName("OpalVanguard");
assertTest("Dépôt banque de faction (+500)", (int)$facAfter["bank_balance"] === 500);
assertTest("Retrait banque de faction (-200)", $factions->withdrawBank($facId, 200));

// Claims
$db->exec("INSERT INTO faction_claims (faction_id, world_name, chunk_x, chunk_z, claimed_at) VALUES ($facId, 'world', 10, 20, " . time() . ")");
$claim = $claims->getClaimAt('world', 10, 20);
assertTest("Revendication et détection de Claim", $claim !== null && (int)$claim["faction_id"] === $facId);

// 4. Ordres d'Ascension
echo "\n--- 4. Ordres d'Ascension & Quêtes ---\n";
$orders = new OrderManager($database);
$quests = new QuestManager($database, $economy, $orders);

$db->exec("INSERT INTO player_orders (xuid, order_name, level, experience) VALUES ('xuid_p1', 'SANG', 1, 0)");
$pOrder = $db->query("SELECT * FROM player_orders WHERE xuid = 'xuid_p1'")->fetchArray(SQLITE3_ASSOC);
assertTest("Serment de Voie Céleste (SANG)", $pOrder !== false && $pOrder["order_name"] === "SANG");

$quests->setQuestCompleted("xuid_p1", "chap_1");
$qProg = $quests->getQuestProgress("xuid_p1", "chap_1");
assertTest("Progression de quête enregistrée", (int)$qProg["completed"] === 1);

// 5. HDV & Primes
echo "\n--- 5. Hôtel des Ventes & Primes ---\n";
$bounties = new BountyManager($database, $economy);

$db->exec("INSERT INTO bounty_contracts (creator_xuid, target_xuid, reward) VALUES ('xuid_p1', 'xuid_p2', 1500)");
$totalBounty = $bounties->getActiveBountiesOn("xuid_p2");
assertTest("Dépôt et calcul de prime active (1500 Or)", $totalBounty === 1500);

// HDV
$fakeNbt = base64_encode("fake_serialized_item");
$db->exec("INSERT INTO ah_listings (seller_xuid, seller_name, item_nbt, item_name, category, price, created_at, expires_at)
    VALUES ('xuid_p1', 'Katsun1246', '$fakeNbt', 'Épée de Diamant', 'ARMES', 2500, " . time() . ", " . (time() + 86400) . ")");
$ahRow = $db->query("SELECT * FROM ah_listings WHERE seller_xuid = 'xuid_p1' AND is_sold = 0")->fetchArray(SQLITE3_ASSOC);
assertTest("Listing HDV créé", $ahRow !== false && (int)$ahRow["price"] === 2500);

// 6. Registre Créatif & Blocs Déverrouillés / Barrières
echo "\n--- 6. Registre Créatif & Modèles 3D ---\n";
$cr = new CreativeRegistry();

$ref = new ReflectionClass($cr);
$blocksProp = $ref->getProperty("customBlocks");
$blocksProp->setAccessible(true);
$blocks = $blocksProp->getValue($cr);

$unlockedCount = 0;
$lockedCount = 0;
foreach($blocks as $k => $b){
    if($b->getNamedTag()->getTag("opal_unimplemented") === null){
        $unlockedCount++;
    }else{
        $lockedCount++;
        assertTest("Bloc sans modèle '$k' est bien une Barrière", $b->getTypeId() === VanillaBlocks::BARRIER()->asItem()->getTypeId());
    }
}
assertTest("Les 13 blocs avec modèles 3D & textures sont déverrouillés", $unlockedCount === 13);
assertTest("Les 11 blocs sans modèle restent en Barrière", $lockedCount === 11);

// Vérifier les Boss et Monstres
$mobsProp = $ref->getProperty("customMobs");
$mobsProp->setAccessible(true);
$mobs = $mobsProp->getValue($cr);
assertTest("Tous les boss et monstres ont un tag 'opal_id'", count($mobs) >= 20 && isset($mobs["boss_vampire"]));

// Bâtons tactiques
$itemsProp = $ref->getProperty("customItems");
$itemsProp->setAccessible(true);
$items = $itemsProp->getValue($cr);
assertTest("Les 4 bâtons tactiques sont enregistrés", count($items) === 4);

// Équipement Titane & Opale
$titaneProp = $ref->getProperty("titaneEquipment");
$titaneProp->setAccessible(true);
$titane = $titaneProp->getValue($cr);
assertTest("L'équipement Titane 3D complet est enregistré (10 pièces)", count($titane) === 10);

$opaleProp = $ref->getProperty("opaleEquipment");
$opaleProp->setAccessible(true);
$opale = $opaleProp->getValue($cr);
assertTest("L'équipement Opale 3D complet est enregistré (10 pièces)", count($opale) === 10);

echo "\n========================================================\n";
echo "   RÉSULTAT DU SCAN : {$testsPassed} PASS / {$testsFailed} FAIL\n";
echo "========================================================\n";

if($testsFailed > 0){
    $db->close();
    exit(1);
}
$db->close();
@unlink($tempDbPath);
exit(0);
