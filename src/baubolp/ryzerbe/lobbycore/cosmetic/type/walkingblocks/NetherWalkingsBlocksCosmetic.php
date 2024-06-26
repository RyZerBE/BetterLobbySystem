<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\walkingblocks;

use pocketmine\block\Block;

class NetherWalkingsBlocksCosmetic extends WalkingBlocksCosmetic {
    /**
     * @return string
     */
    public function getName(): string{
        return "Nether";
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
        return "walking_blocks:nether";
    }

    /**
     * @return Block[]
     */
    public function getBlocks(): array{
        return [
            Block::get(Block::GLOWSTONE),
            Block::get(Block::NETHERRACK),
            Block::get(Block::QUARTZ_ORE),
        ];
    }

    /**
     * @return array
     */
    public function getSlabBlocks(): array{
        return [
            Block::get(Block::STONE_SLAB, 7),
        ];
    }
}