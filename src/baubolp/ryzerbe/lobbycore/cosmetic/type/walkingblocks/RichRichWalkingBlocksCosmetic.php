<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\walkingblocks;

use pocketmine\block\Block;

class RichRichWalkingBlocksCosmetic extends WalkingBlocksCosmetic {

    /**
     * @return string
     */
    public function getName(): string{
        return "Rich Rich";
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
        return "walking_blocks:rich_rich";
    }

    /**
     * @return Block[]
     */
    public function getBlocks(): array{
        return [
            Block::get(Block::DIAMOND_BLOCK),
            Block::get(Block::EMERALD_BLOCK),
            Block::get(Block::GOLD_BLOCK),
        ];
    }
}