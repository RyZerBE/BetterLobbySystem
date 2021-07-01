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
        return 1000;//todo
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
}