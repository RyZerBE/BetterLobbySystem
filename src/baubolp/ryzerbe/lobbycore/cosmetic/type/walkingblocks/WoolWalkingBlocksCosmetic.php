<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\walkingblocks;

use pocketmine\block\Block;
use function mt_rand;

class WoolWalkingBlocksCosmetic extends WalkingBlocksCosmetic {
    /**
     * @return string
     */
    public function getName(): string{
        return "Wool";
    }

    /**
     * @return int
     */
    public function getPrice(): int{
        return 20000;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string{
        return "walking_blocks:wool";
    }

    /**
     * @return Block[]
     */
    public function getBlocks(): array{
        return [
            Block::get(Block::WOOL, mt_rand(0, 15)),
        ];
    }

    /**
     * @return array
     */
    public function getSecondBlockLayer(): array{
        return [
            Block::get(Block::CARPET, mt_rand(0, 15)),
        ];
    }
}