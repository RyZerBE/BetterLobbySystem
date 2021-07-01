<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\category\type;

use baubolp\ryzerbe\lobbycore\cosmetic\category\CosmeticCategory;
use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\walkingblocks\GrasslandWalkingBlocksCosmetic;
use baubolp\ryzerbe\lobbycore\cosmetic\type\walkingblocks\RichRichWalkingBlocksCosmetic;
use baubolp\ryzerbe\lobbycore\cosmetic\type\walkingblocks\TheEndWalkingBlocksCosmetic;

class WalkingBlocksCategory extends CosmeticCategory {

    public function loadCosmetics(): void{
        $cosmetics = [
            new GrasslandWalkingBlocksCosmetic(),
            new TheEndWalkingBlocksCosmetic(),
            new RichRichWalkingBlocksCosmetic()
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