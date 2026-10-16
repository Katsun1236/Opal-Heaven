<?php

declare(strict_types=1);

namespace opal\core\economy;

use opal\core\database\DatabaseManager;
use pocketmine\player\Player;

class EconomyManager{

    public function __construct(private DatabaseManager $databaseManager){}

    private function resolveKey(Player|string $player) : string{
        if($player instanceof Player){
            $xuid = $player->getXuid();
            return !empty($xuid) ? $xuid : strtolower($player->getName());
        }
        return $player;
    }

    public function registerPlayer(Player|string $player, ?string $username = null) : void{
        if($player instanceof Player){
            $xuid = !empty($player->getXuid()) ? $player->getXuid() : strtolower($player->getName());
            $name = $player->getName();
        }else{
            $xuid = $player;
            $name = $username ?? $player;
        }

        $stmt = $this->databaseManager->getDb()->prepare("
            INSERT OR IGNORE INTO players (xuid, username, money) VALUES (:xuid, :username, 1000)
        ");
        $stmt->bindValue(':xuid', $xuid, SQLITE3_TEXT);
        $stmt->bindValue(':username', $name, SQLITE3_TEXT);
        $stmt->execute();

        // Mettre à jour le username et le last_join
        $update = $this->databaseManager->getDb()->prepare("
            UPDATE players SET username = :username, last_join = CURRENT_TIMESTAMP WHERE xuid = :xuid
        ");
        $update->bindValue(':xuid', $xuid, SQLITE3_TEXT);
        $update->bindValue(':username', $name, SQLITE3_TEXT);
        $update->execute();
    }

    public function getMoney(Player|string $player) : int{
        $key = $this->resolveKey($player);
        $stmt = $this->databaseManager->getDb()->prepare("SELECT money FROM players WHERE xuid = :key OR username = :key COLLATE NOCASE LIMIT 1");
        $stmt->bindValue(':key', $key, SQLITE3_TEXT);
        $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        return $res !== false ? (int)$res['money'] : 0;
    }

    public function getBalance(Player|string $player) : int{
        return $this->getMoney($player);
    }

    public function setMoney(Player|string $player, int $amount) : void{
        $key = $this->resolveKey($player);
        $stmt = $this->databaseManager->getDb()->prepare("UPDATE players SET money = :money WHERE xuid = :key OR username = :key COLLATE NOCASE");
        $stmt->bindValue(':money', max(0, $amount), SQLITE3_INTEGER);
        $stmt->bindValue(':key', $key, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function addMoney(Player|string $player, int $amount) : void{
        if($amount <= 0) return;
        $this->setMoney($player, $this->getMoney($player) + $amount);
    }

    public function reduceMoney(Player|string $player, int $amount) : bool{
        if($amount <= 0) return false;
        $current = $this->getMoney($player);
        if($current < $amount){
            return false;
        }
        $this->setMoney($player, $current - $amount);
        return true;
    }

    public function transfer(Player|string $from, Player|string $to, int $amount) : bool{
        if($amount <= 0) return false;
        if($this->getMoney($from) < $amount) return false;
        $this->reduceMoney($from, $amount);
        $this->addMoney($to, $amount);
        return true;
    }

    /**
     * @return array<int, array{username: string, money: int}>
     */
    public function getTopBalances(int $limit = 10) : array{
        $res = $this->databaseManager->getDb()->query("SELECT username, money FROM players ORDER BY money DESC LIMIT " . (int)$limit);
        $list = [];
        while($row = $res->fetchArray(SQLITE3_ASSOC)){
            $list[] = [
                'username' => (string)$row['username'],
                'money' => (int)$row['money']
            ];
        }
        return $list;
    }
}
