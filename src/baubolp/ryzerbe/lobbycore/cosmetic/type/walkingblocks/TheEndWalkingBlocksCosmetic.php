<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\walkingblocks;

use pocketmine\block\Block;

class TheEndWalkingBlocksCosmetic extends WalkingBlocksCosmetic {

    /**
     * @return string
     */
    public function getName(): string{
        return "The End";
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
        return "walking_blocks:the_end";
    }

    /**
     * @return Block[]
     */
    public function getBlocks(): array{
        return [
            Block::get(Block::END_STONE),
            Block::get(Block::OBSIDIAN),
            Block::get(Block::DRAGON_EGG),
            Block::get(Block::PURPUR_BLOCK)
        ];
    }

    /**
     * @return array
     */
    public function getSlabBlocks(): array{
        return [
            Block::get(Block::STONE_SLAB2, 1),
        ];
    }
}