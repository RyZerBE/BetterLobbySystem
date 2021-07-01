<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\itemrain;

use pocketmine\block\Block;
use pocketmine\item\Item;

class NetherItemRainCosmetic extends ItemRainCosmetic {

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
        return 2000;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string{
        return "itemrain:nether";
    }

    /**
     * @return Item[]
     */
    public function getItems(): array{
        return [
            Item::get(Item::NETHERRACK),
            Item::get(Item::QUARTZ_ORE),
            Item::get(-288),
            Item::get(-272),
            Item::get(-289),
            Item::get(Block::OBSIDIAN),
            Item::get(Block::MAGMA),
        ];
    }
}