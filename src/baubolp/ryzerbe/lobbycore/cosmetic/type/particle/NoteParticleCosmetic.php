<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\particle;

use pocketmine\level\particle\GenericParticle;
use pocketmine\level\particle\Particle;
use pocketmine\math\Vector3;

class NoteParticleCosmetic extends ParticleCosmetic {

    public function getName(): string{
        return "Music Notes";
    }

    public function getPrice(): int{
        return 8000;
    }

    public function getIdentifier(): string{
        return "particle:music_notes";
    }

    public function getParticle(Vector3 $vector3): Particle{
        return new GenericParticle($vector3, Particle::TYPE_NOTE);
    }
}