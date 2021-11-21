<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\itemrain;

use pocketmine\item\Item;
use function mt_rand;

class AquaticItemRainCosmetic extends ItemRainCosmetic {
    /**
     * @return string
     */
    public function getName(): string{
        return "Aquatic";
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
        return "itemrain:aquatic";
    }

    /**
     * @return Item[]
     */
    public function getItems(): array{
        return [
            Item::get(-131, mt_rand(0, 15)),
            Item::get(-132, mt_rand(0, 15)),
            Item::get(-133, mt_rand(0, 15)),
            Item::get(-135, mt_rand(0, 15)),
            Item::get(-136, mt_rand(0, 15)),
            Item::get(-137, mt_rand(0, 15)),
        ];
    }
}