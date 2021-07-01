<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\category\type;

use baubolp\ryzerbe\lobbycore\cosmetic\category\CosmeticCategory;
use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\particle\HeartParticleCosmetic;

class ParticleCategory extends CosmeticCategory {


    public function loadCosmetics(): void{
        $cosmetics = [
            new HeartParticleCosmetic()
        ];
        foreach($cosmetics as $cosmetic) {
            $this->registerCosmetic($cosmetic);
        }
    }

    /**
     * @return int
     */
    public function getId(): int{
        return CosmeticManager::CATEGORY_PARTICLE;
    }

    /**
     * @return string
     */
    public function getName(): string{
        return "Particles";
    }
}