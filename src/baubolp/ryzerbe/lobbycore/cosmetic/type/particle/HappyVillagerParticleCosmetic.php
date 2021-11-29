<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\particle;

use pocketmine\level\particle\HappyVillagerParticle;
use pocketmine\level\particle\Particle;
use pocketmine\math\Vector3;

class HappyVillagerParticleCosmetic extends ParticleCosmetic {
    public function getName(): string{
        return "Happy Villager";
    }

    public function getPrice(): int{
        return 10000;
    }

    public function getIdentifier(): string{
        return "particle:happy_villager";
    }

    public function getParticle(Vector3 $vector3): Particle{
        return new HappyVillagerParticle($vector3);
    }
}