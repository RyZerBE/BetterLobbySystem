<?php

namespace baubolp\ryzerbe\lobbycore\shop;

use pocketmine\Player;

abstract class ShopArticle {
    abstract public function getName(): string;

    abstract public function getDescription(): string;

    abstract public function getPrice(): int;

    abstract public function buyArticle(Player $player): void;
}