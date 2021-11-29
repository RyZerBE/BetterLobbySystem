<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\itemrain;

use pocketmine\item\Item;

class UHCItemRainCosmetic extends ItemRainCosmetic {
    /**
     * @return string
     */
    public function getName(): string{
        return "UHC";
    }

    /**
     * @return int
     */
    public function getPrice(): int{
        return 30000;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string{
        return "itemrain:uhc";
    }

    /**
     * @return Item[]
     */
    public function getItems(): array{
        return [
            Item::get(Item::BUCKET, 10),
            Item::get(Item::BUCKET, 8),
            Item::get(Item::GOLDEN_APPLE),
            Item::get(Item::ENCHANTED_GOLDEN_APPLE),
            Item::get(Item::ENCHANTED_BOOK),
            Item::get(Item::ANVIL),
        ];
    }
}