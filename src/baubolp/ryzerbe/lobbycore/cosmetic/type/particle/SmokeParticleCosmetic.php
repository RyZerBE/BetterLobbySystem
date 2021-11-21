<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\particle;

use pocketmine\level\particle\MobSpawnParticle;
use pocketmine\level\particle\Particle;
use pocketmine\math\Vector3;

class SmokeParticleCosmetic extends ParticleCosmetic {
    public function getName(): string{
        return "Smoke";
    }

    public function getPrice(): int{
        return 1000;
    }

    public function getIdentifier(): string{
        return "particle:smoke";
    }

    public function getParticle(Vector3 $vector3): Particle{
        return new MobSpawnParticle($vector3);
    }
}