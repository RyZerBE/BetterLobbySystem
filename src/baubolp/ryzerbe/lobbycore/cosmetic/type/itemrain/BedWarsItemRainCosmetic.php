<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\itemrain;

use pocketmine\item\Item;

class BedWarsItemRainCosmetic extends ItemRainCosmetic {
    public function getName(): string{
        return "BedWars";
    }

    public function getPrice(): int{
        return 25000;
    }

    public function getIdentifier(): string{
        return "item_rain:bedwars";
    }

    /**
     * @inheritDoc
     */
    public function getItems(): array{
        return [
            Item::get(Item::STICK),
            Item::get(Item::SANDSTONE),
            Item::get(Item::WEB),
        ];
    }
}