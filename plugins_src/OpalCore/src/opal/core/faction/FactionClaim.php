<?php

declare(strict_types=1);

namespace opal\core\faction;

use opal\core\database\DatabaseManager;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;
use pocketmine\world\Position;

class FactionClaim implements Listener{

    private static ?self $instance = null;
    private DatabaseManager $database;
    private FactionManager $factionManager;

    public function __construct(DatabaseManager $database, FactionManager $factionManager){
        self::$instance = $this;
        $this->database = $database;
        $this->factionManager = $factionManager;
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function getClaimAt(string $worldName, int $chunkX, int $chunkZ) : ?array{
        $db = $this->database->getDb();
        $stmt = $db->prepare("
            SELECT fc.*, f.name as faction_name 
            FROM faction_claims fc 
            JOIN factions f ON f.id = fc.faction_id 
            WHERE fc.world_name = :w AND fc.chunk_x = :cx AND fc.chunk_z = :cz 
            LIMIT 1
        ");
        $stmt->bindValue(":w", $worldName, SQLITE3_TEXT);
        $stmt->bindValue(":cx", $chunkX, SQLITE3_INTEGER);
        $stmt->bindValue(":cz", $chunkZ, SQLITE3_INTEGER);
        $res = $stmt->execute();
        $row = $res->fetchArray(SQLITE3_ASSOC);
        return $row !== false ? $row : null;
    }

    public function claimChunk(Player $player) : bool{
        $fac = $this->factionManager->getPlayerFaction($player);
        if($fac === null){
            $player->sendMessage("§c❌ Vous devez être dans une faction pour claim !");
            return false;
        }

        if(!in_array($fac["rank"], ["OFFICIER", "CHEF"], true)){
            $player->sendMessage("§c❌ Seuls les Officiers et le Chef peuvent revendiquer des terres !");
            return false;
        }

        $world = $player->getWorld()->getFolderName();
        if(strtolower($world) === "hub" || strtolower($world) === "warzone"){
            $player->sendMessage("§c❌ Impossible de claim dans cette dimension !");
            return false;
        }

        $db = $this->database->getDb();

        // Vérification de la limite de claims par rapport au Power de la faction
        $countStmt = $db->prepare("SELECT COUNT(*) as cnt FROM faction_claims WHERE faction_id = :fid");
        if($countStmt !== false){
            $countStmt->bindValue(":fid", (int)$fac["id"], SQLITE3_INTEGER);
            $resCount = $countStmt->execute();
            if($resCount !== false){
                $row = $resCount->fetchArray(SQLITE3_ASSOC);
                $claims = (int)($row["cnt"] ?? 0);
                $power = (int)($fac["power"] ?? 10);
                if($claims >= $power){
                    $player->sendMessage("§c❌ Power insuffisant ! Votre faction possède {$claims}/{$power} claims maximum.");
                    return false;
                }
            }
        }

        $cx = $player->getPosition()->getFloorX() >> 4;
        $cz = $player->getPosition()->getFloorZ() >> 4;

        if($this->getClaimAt($world, $cx, $cz) !== null){
            $player->sendMessage("§c❌ Ce chunk est déjà revendiqué !");
            return false;
        }

        $stmt = $db->prepare("INSERT INTO faction_claims (faction_id, chunk_x, chunk_z, world_name) VALUES (:fid, :cx, :cz, :w)");
        if($stmt === false) return false;

        $stmt->bindValue(":fid", $fac["id"], SQLITE3_INTEGER);
        $stmt->bindValue(":cx", $cx, SQLITE3_INTEGER);
        $stmt->bindValue(":cz", $cz, SQLITE3_INTEGER);
        $stmt->bindValue(":w", $world, SQLITE3_TEXT);
        $success = @$stmt->execute();

        if($success){
            $player->sendMessage("§a🚩 Chunk [§e{$cx}, {$cz}§a] revendiqué au nom de la faction §e" . $fac["name"] . " §a!");
            return true;
        }
        return false;
    }

    public function unclaimChunk(Player $player) : bool{
        $fac = $this->factionManager->getPlayerFaction($player);
        if($fac === null || !in_array($fac["rank"], ["OFFICIER", "CHEF"], true)){
            $player->sendMessage("§c❌ Seuls les Officiers et le Chef peuvent unclaim !");
            return false;
        }

        $world = $player->getWorld()->getFolderName();
        $cx = $player->getPosition()->getFloorX() >> 4;
        $cz = $player->getPosition()->getFloorZ() >> 4;

        $claim = $this->getClaimAt($world, $cx, $cz);
        if($claim === null || (int)$claim["faction_id"] !== (int)$fac["id"]){
            $player->sendMessage("§c❌ Ce chunk n'appartient pas à votre faction !");
            return false;
        }

        $db = $this->database->getDb();
        $stmt = $db->prepare("DELETE FROM faction_claims WHERE world_name = :w AND chunk_x = :cx AND chunk_z = :cz AND faction_id = :fid");
        if($stmt === false) return false;

        $stmt->bindValue(":w", $world, SQLITE3_TEXT);
        $stmt->bindValue(":cx", $cx, SQLITE3_INTEGER);
        $stmt->bindValue(":cz", $cz, SQLITE3_INTEGER);
        $stmt->bindValue(":fid", (int)$fac["id"], SQLITE3_INTEGER);
        $stmt->execute();

        $player->sendMessage("§e🚩 Chunk [§6{$cx}, {$cz}§e] abandonné.");
        return true;
    }

    public function canInteract(Player $player, Position $pos) : bool{
        if($player->hasPermission("opal.admin") || $player->hasPermission("pocketmine.group.operator")){
            return true;
        }

        $world = $pos->getWorld()?->getFolderName();
        if($world === null) return true;

        $cx = $pos->getFloorX() >> 4;
        $cz = $pos->getFloorZ() >> 4;

        $claim = $this->getClaimAt($world, $cx, $cz);
        if($claim === null){
            return true; // Zone sauvage
        }

        $fac = $this->factionManager->getPlayerFaction($player);
        if($fac !== null && (int)$fac["id"] === (int)$claim["faction_id"]){
            return true; // Membre de la faction
        }

        return false;
    }

    public function onBreak(BlockBreakEvent $event) : void{
        $player = $event->getPlayer();
        if(!$this->canInteract($player, $event->getBlock()->getPosition())){
            $event->cancel();
            $player->sendActionBarMessage("§c❌ Territoire protégé par une faction ennemie !");
        }
    }

    public function onPlace(BlockPlaceEvent $event) : void{
        $player = $event->getPlayer();
        if(!$this->canInteract($player, $event->getBlock()->getPosition())){
            $event->cancel();
            $player->sendActionBarMessage("§c❌ Territoire protégé par une faction ennemie !");
        }
    }

    public function onInteract(PlayerInteractEvent $event) : void{
        // Ne pas bloquer l'utilisation d'items en l'air (nourriture, potions, arcs, perles)
        if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR){
            return;
        }

        $player = $event->getPlayer();
        $pos = $event->getBlock()->getPosition();
        if(!$this->canInteract($player, $pos)){
            $event->cancel();
            $player->sendActionBarMessage("§c❌ Coffre / Conteneur verrouillé par le claim ennemi !");
        }
    }

    public function renderMap(Player $player) : void{
        $world = $player->getWorld()->getFolderName();
        $curCx = $player->getPosition()->getFloorX() >> 4;
        $curCz = $player->getPosition()->getFloorZ() >> 4;
        $myFac = $this->factionManager->getPlayerFaction($player);
        $myFacId = $myFac !== null ? (int)$myFac["id"] : -1;

        $radius = 4;
        $minX = $curCx - $radius;
        $maxX = $curCx + $radius;
        $minZ = $curCz - $radius;
        $maxZ = $curCz + $radius;

        // Requête SQLite optimisée : 1 seule requête pour toute la zone au lieu de 81
        $claimsMap = [];
        $db = $this->database->getDb();
        $stmt = $db->prepare("
            SELECT chunk_x, chunk_z, faction_id 
            FROM faction_claims 
            WHERE world_name = :w AND chunk_x BETWEEN :minX AND :maxX AND chunk_z BETWEEN :minZ AND :maxZ
        ");
        if($stmt !== false){
            $stmt->bindValue(":w", $world, SQLITE3_TEXT);
            $stmt->bindValue(":minX", $minX, SQLITE3_INTEGER);
            $stmt->bindValue(":maxX", $maxX, SQLITE3_INTEGER);
            $stmt->bindValue(":minZ", $minZ, SQLITE3_INTEGER);
            $stmt->bindValue(":maxZ", $maxZ, SQLITE3_INTEGER);
            $res = $stmt->execute();
            if($res !== false){
                while($row = $res->fetchArray(SQLITE3_ASSOC)){
                    $claimsMap[$row["chunk_x"] . ":" . $row["chunk_z"]] = (int)$row["faction_id"];
                }
            }
        }

        $player->sendMessage("§e==== §6Carte des Factions [§f{$curCx}, {$curCz}§6] §e====");
        for($z = $minZ; $z <= $maxZ; $z++){
            $line = "";
            for($x = $minX; $x <= $maxX; $x++){
                if($x === $curCx && $z === $curCz){
                    $line .= "§b+"; // Joueur
                    continue;
                }
                $facId = $claimsMap[$x . ":" . $z] ?? null;
                if($facId === null){
                    $line .= "§7-"; // Libre
                }elseif($facId === $myFacId){
                    $line .= "§a#"; // Votre faction
                }else{
                    $line .= "§c#"; // Ennemi
                }
            }
            $player->sendMessage("  " . $line);
        }
        $player->sendMessage("§7Légende: §b+ Toi §7| §a# Ta faction §7| §c# Ennemi §7| §7- Sauvage");
    }
}
