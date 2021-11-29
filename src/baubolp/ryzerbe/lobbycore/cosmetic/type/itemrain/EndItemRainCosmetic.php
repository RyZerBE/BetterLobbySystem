<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\itemrain;

use pocketmine\item\Item;

class EndItemRainCosmetic extends ItemRainCosmetic {
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
        return 25000;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string{
        return "itemrain:the_end";
    }

    /**
     * @return Item[]
     */
    public function getItems(): array{
        return [
            Item::get(Item::END_ROD),
            Item::get(Item::DRAGON_EGG),
            Item::get(Item::DRAGON_BREATH),
        ];
    }
}