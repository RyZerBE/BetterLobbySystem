<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\particle;

use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\Cosmetic;
use pocketmine\level\particle\Particle;
use pocketmine\math\Vector3;
use pocketmine\Player;

abstract class ParticleCosmetic extends Cosmetic {

    abstract public function getParticle(Vector3 $vector3): Particle;

    /**
     * @return int
     */
    public function getCategory(): int{
        return CosmeticManager::CATEGORY_PARTICLE;
    }

    /**
     * @param Player $player
     * @param int $currentTick
     */
    public function onUpdate(Player $player, int $currentTick): void{
        if($currentTick % 2 !== 0) return;
        $player->getLevel()->addParticle($this->getParticle(
            $player->asVector3()->add($this->randomFloat(), $this->randomFloat(0), $this->randomFloat())
        ));
    }
}