<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\particle;

use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\Particle;
use pocketmine\math\Vector3;

class HeartParticleCosmetic extends ParticleCosmetic {

    /**
     * @return string
     */
    public function getName(): string{
        return "Heart Particle";
    }

    /**
     * @return int
     */
    public function getPrice(): int{
        return 100;//todo
    }

    /**
     * @return string
     */
    public function getIdentifier(): string{
        return "particles:hearts";
    }

    /**
     * @param Vector3 $vector3
     * @return Particle
     */
    public function getParticle(Vector3 $vector3): Particle{
        return new HeartParticle($vector3);
    }
}