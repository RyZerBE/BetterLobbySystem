<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\particle;

use pocketmine\level\particle\AngryVillagerParticle;
use pocketmine\level\particle\Particle;
use pocketmine\math\Vector3;

class AngryVillagerParticleCosmetic extends ParticleCosmetic {
    public function getName(): string{
        return "Angry Villager";
    }

    public function getPrice(): int{
        return 1000;
    }

    public function getIdentifier(): string{
        return "particle:angry_villager";
    }

    public function getParticle(Vector3 $vector3): Particle{
        return new AngryVillagerParticle($vector3);
    }
}