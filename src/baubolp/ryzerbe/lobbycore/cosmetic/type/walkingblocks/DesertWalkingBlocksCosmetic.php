<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\walkingblocks;

use pocketmine\block\Block;

class DesertWalkingBlocksCosmetic extends WalkingBlocksCosmetic {

    /**
     * @return string
     */
    public function getName(): string{
        return "Desert";
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
        return "walking_blocks:desert";
    }

    /**
     * @return Block[]
     */
    public function getBlocks(): array{
        return [
            Block::get(Block::SAND),
            Block::get(Block::SANDSTONE),
        ];
    }

    /**
     * @return array
     */
    public function getSecondBlockLayer(): array{
        return [
            Block::get(Block::DEAD_BUSH),
        ];
    }
}