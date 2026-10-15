<?php

declare(strict_types=1);

namespace opalheaven\core\faction;

class Faction {

    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $leaderXuid,
        public float $power,
        public float $maxPower,
        public int $level,
        public int $xp,
        public int $bankBalance,
        public ?string $heartWorld,
        public ?float $heartX,
        public ?float $heartY,
        public ?float $heartZ,
        public int $heartHealth,
        public bool $isUnderSiege,
        public ?int $siegeExpiresAt
    ) {}

    public function isHeartSet() : bool {
        return $this->heartWorld !== null && $this->heartX !== null;
    }

    public function isUnderSiege() : bool {
        if (!$this->isUnderSiege) {
            return false;
        }
        if ($this->siegeExpiresAt !== null && time() > $this->siegeExpiresAt) {
            return false;
        }
        return true;
    }
}
