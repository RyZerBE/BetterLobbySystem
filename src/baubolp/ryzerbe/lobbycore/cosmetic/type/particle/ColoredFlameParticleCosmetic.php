<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\particle;

use pocketmine\level\particle\GenericParticle;
use pocketmine\level\particle\Particle;
use pocketmine\math\Vector3;
use function mt_rand;

class ColoredFlameParticleCosmetic extends ParticleCosmetic {

    public function getName(): string{
        return "Colored Flames";
    }

    public function getPrice(): int{
        return 20000;
    }

    public function getIdentifier(): string{
        return "particle:cosmetic_colored_flame_particle";
    }

    public function getParticle(Vector3 $vector3): Particle{
        return new GenericParticle($vector3, Particle::TYPE_COLORED_FLAME, mt_rand()); //ParticleIds::COLORED_FLAME = 56; BedrockProtocol
    }
}