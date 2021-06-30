<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\category\type;

use baubolp\ryzerbe\lobbycore\cosmetic\category\CosmeticCategory;
use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\walkingblocks\GrasslandWalkingBlocksCosmetic;

class WalkingBlocksCategory extends CosmeticCategory {

    public function loadCosmetics(): void{
        $cosmetics = [
            new GrasslandWalkingBlocksCosmetic()
        ];
        foreach($cosmetics as $cosmetic) {
            $this->registerCosmetic($cosmetic);
        }
    }

    /**
     * @return int
     */
    public function getId(): int{
        return CosmeticManager::CATEGORY_WALKING_BLOCKS;
    }

    /**
     * @return string
     */
    public function getName(): string{
        return "Walking Blocks";
    }
}