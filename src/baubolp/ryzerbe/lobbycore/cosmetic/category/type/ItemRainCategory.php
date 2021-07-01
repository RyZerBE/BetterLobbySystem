<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\category\type;

use baubolp\ryzerbe\lobbycore\cosmetic\category\CosmeticCategory;
use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\itemrain\BedWarsItemRainCosmetic;
use baubolp\ryzerbe\lobbycore\cosmetic\type\itemrain\EndItemRainCosmetic;
use baubolp\ryzerbe\lobbycore\cosmetic\type\itemrain\UHCItemRainCosmetic;

class ItemRainCategory extends CosmeticCategory {

    public function loadCosmetics(): void{
        $cosmetics = [
            new BedWarsItemRainCosmetic(),
            new EndItemRainCosmetic(),
            new UHCItemRainCosmetic()
        ];
        foreach($cosmetics as $cosmetic) {
            $this->registerCosmetic($cosmetic);
        }
    }

    /**
     * @return int
     */
    public function getId(): int{
        return CosmeticManager::CATEGORY_ITEM_RAIN;
    }

    /**
     * @return string
     */
    public function getName(): string{
        return "Item Rain";
    }
}