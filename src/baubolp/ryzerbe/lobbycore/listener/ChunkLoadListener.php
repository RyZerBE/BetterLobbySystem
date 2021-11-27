<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use pocketmine\event\level\ChunkLoadEvent;
use pocketmine\event\Listener;
use pocketmine\level\biome\Biome;

class ChunkLoadListener implements Listener {

    public function onSnow(ChunkLoadEvent $event){
        for($x = 0; $x < 16; ++$x)
            for($z = 0; $z < 16; ++$z)
                $event->getChunk()->setBiomeId($x, $z, Biome::ICE_PLAINS);
    }
}