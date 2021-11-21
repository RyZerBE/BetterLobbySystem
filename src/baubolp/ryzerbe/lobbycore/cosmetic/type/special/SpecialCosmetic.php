<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\special;

use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\Cosmetic;

abstract class SpecialCosmetic extends Cosmetic {
    /**
     * @return int
     */
    public function getCategory(): int{
        return CosmeticManager::CATEGORY_SPECIALS;
    }
}