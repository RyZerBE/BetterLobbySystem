<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\category\type;

use baubolp\ryzerbe\lobbycore\cosmetic\category\CosmeticCategory;
use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use matze\chestopening\reward\types\lobby\particle\HeartParticleCosmetic;

class SpecialsCategory extends CosmeticCategory {

    public function loadCosmetics(): void{
        $cosmetics = [

        ];
        foreach($cosmetics as $cosmetic) {
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