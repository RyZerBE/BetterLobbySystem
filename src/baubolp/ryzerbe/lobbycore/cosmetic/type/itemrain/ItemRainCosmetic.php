<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\itemrain;

use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\Cosmetic;
use baubolp\ryzerbe\lobbycore\entity\ItemRainItemEntity;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use function array_rand;
use function lcg_value;

abstract class ItemRainCosmetic extends Cosmetic {
    /**
     * @return int
     */
    public function getCategory(): int{
        return CosmeticManager::CATEGORY_ITEM_RAIN;
    }

    /**
     * @param Player $player
     * @param int $currentTick
     */
    public function onUpdate(Player $player, int $currentTick): void{
        if($currentTick % 4 !== 0 || empty($this->getItems())) return;
        $item = $this->getItems()[array_rand($this->getItems())];
        $motion = new Vector3(lcg_value() * 0.2 - 0.1, 0.3, lcg_value() * 0.2 - 0.1);
        $itemTag = $item->nbtSerialize();
        $itemTag->setName("Item");
        $nbt = Entity::createBaseNBT($player->add(0, $player->getEyeHeight() + 0.5), $motion, lcg_value() * 360, 0);
        $nbt->setShort("PickupDelay", -1);
        $nbt->setTag($itemTag);
        $itemEntity = new ItemRainItemEntity($player->getLevel(), $nbt);
        $itemEntity->spawnToAll();
    }

    /**
     * @return Item[]
     */
    abstract public function getItems(): array;
}