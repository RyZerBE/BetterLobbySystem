<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\vehicle\hypetrain;

use baubolp\ryzerbe\lobbycore\entity\hypetrain\HypeTrainEntity;
use baubolp\ryzerbe\lobbycore\entity\hypetrain\projectile\HeadProjectileEntity;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\Player;
use ryzerbe\core\util\ItemUtils;
use function mt_rand;

class HypeTrain {
    /**
     * @param Player $player
     */
    public static function spawn(Player $player): void{
        $nbt = Entity::createBaseNBT($player);
        $train = new HypeTrainEntity($player->getLevel(), $nbt);
        $train->setOwningEntity($player);
        $train->setRider($player);
        $train->sendEntityLink($train);
        $train->spawnToAll();
        $player->getInventory()->setContents([
            4 => ItemUtils::addItemTag(Item::get(Item::SKULL, 3)->setCustomName("§r§aHead Canon"), "head_canon", "hypetrain_item"),
        ]);
    }

    /**
     * @param Player $player
     */
    public static function shootHead(Player $player): void{
        $nbt = Entity::createBaseNBT($player->add(0, $player->getEyeHeight()));
        $head = new HeadProjectileEntity($player->getLevel(), $nbt);
        $head->spawnToAll();
        $head->setMotion($player->getDirectionVector()->multiply((2 + (mt_rand(1, 10) / 10))));
    }
}