<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\category\type;

use baubolp\ryzerbe\lobbycore\cosmetic\category\CosmeticCategory;
use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\special\HeadCanonSpecialCosmetic;
use baubolp\ryzerbe\lobbycore\cosmetic\type\special\SpidermanGunSpecialCosmetic;

class SpecialsCategory extends CosmeticCategory {
    public function loadCosmetics(): void{
        $cosmetics = [
            new SpidermanGunSpecialCosmetic(),
            new HeadCanonSpecialCosmetic(),
        ];
        foreach($cosmetics as $cosmetic){
            $this->registerCosmetic($cosmetic);
        }
    }

    /**
     * @return int
     */
    public function getId(): int{
        return CosmeticManager::CATEGORY_SPECIALS;
    }

    /**
     * @return string
     */
    public function getName(): string{
        return "Specials";
    }
}