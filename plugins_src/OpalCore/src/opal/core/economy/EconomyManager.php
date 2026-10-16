<?php

declare(strict_types=1);

namespace opal\core\economy;

use opal\core\database\DatabaseManager;

class EconomyManager{

    public function __construct(private DatabaseManager $databaseManager){}

    public function registerPlayer(string $xuid, string $username) : void{
        $stmt = $this->databaseManager->getDb()->prepare("
            INSERT OR IGNORE INTO players (xuid, username, money) VALUES (:xuid, :username, 1000)
        ");
        $stmt->bindValue(':xuid', $xuid, SQLITE3_TEXT);
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->execute();

        // Mettre à jour le username et le last_join
        $update = $this->databaseManager->getDb()->prepare("
            UPDATE players SET username = :username, last_join = CURRENT_TIMESTAMP WHERE xuid = :xuid
        ");
        $update->bindValue(':xuid', $xuid, SQLITE3_TEXT);
        $update->bindValue(':username', $username, SQLITE3_TEXT);
        $update->execute();
    }

    public function getMoney(string $xuid) : int{
        $stmt = $this->databaseManager->getDb()->prepare("SELECT money FROM players WHERE xuid = :xuid");
        $stmt->bindValue(':xuid', $xuid, SQLITE3_TEXT);
        $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        return $res !== false ? (int)$res['money'] : 0;
    }

    public function setMoney(string $xuid, int $amount) : void{
        $stmt = $this->databaseManager->getDb()->prepare("UPDATE players SET money = :money WHERE xuid = :xuid");
        $stmt->bindValue(':money', max(0, $amount), SQLITE3_INTEGER);
        $stmt->bindValue(':xuid', $xuid, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function addMoney(string $xuid, int $amount) : void{
        if($amount <= 0) return;
        $this->setMoney($xuid, $this->getMoney($xuid) + $amount);
    }

    public function reduceMoney(string $xuid, int $amount) : bool{
        if($amount <= 0) return false;
        $current = $this->getMoney($xuid);
        if($current < $amount){
            return false;
        }
        $this->setMoney($xuid, $current - $amount);
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
