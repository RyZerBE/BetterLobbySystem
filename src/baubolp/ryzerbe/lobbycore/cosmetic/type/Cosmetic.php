<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type;

use pocketmine\Player;
use function mt_rand;

abstract class Cosmetic {

    abstract public function getName(): string;
    abstract public function getPrice(): int;
    abstract public function getIdentifier(): string;
    abstract public function getCategory(): int;

    /**
     * @return string
     */
    public function getIcon(): string {
        return "";
    }

    /**
     * @return int
     */
    public function getIconType(): int {
        return -1;
    }


    /**
     * @param Player $player
     * @param int $currentTick
     */
    public function onUpdate(Player $player, int $currentTick): void {}

    /**
     * @param Player $player
     */
    public function onActivate(Player $player): void {}
    public function onDeactivate(Player $player): void {}

    /**
     * @param int $min
     * @param int $max
     * @param int $number
     * @return float
     */
    protected function randomFloat(int $min = -1, int $max = 1, int $number = 10): float {
        return mt_rand(($min * $number), ($max * $number)) / $number;
    }
}