<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\walkingblocks;

use pocketmine\block\Block;

class GrasslandWalkingBlocksCosmetic extends WalkingBlocksCosmetic {
    /**
     * @return string
     */
    public function getName(): string{
        return "Grassland";
    }

    /**
     * @return int
     */
    public function getPrice(): int{
        return 1000;//todo
    }

    /**
     * @return string
     */
    public function getIdentifier(): string{
        return "walking_blocks:grassland";
    }

    /**
     * @return array
     */
    public function getBlocks(): array{
        return [
            Block::get(Block::GRASS),
            Block::get(Block::DIRT, 1),
        ];
    }

    /**
     * @return array
     */
    public function getSecondBlockLayer(): array{
        return [
            Block::get(Block::TALLGRASS),
        ];
    }
}