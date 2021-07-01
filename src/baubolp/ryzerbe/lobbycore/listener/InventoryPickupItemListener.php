<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\core\provider\CoinProvider;
use baubolp\core\util\ItemUtils;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class InventoryPickupItemListener implements Listener
{

    public function pickup(InventoryPickupItemEvent $event)
    {
        $itemEntity = $event->getItem();
        $item = $itemEntity->getItem();
        $event->setCancelled();
        if (!ItemUtils::hasItemTag($item, "coinbomb_item")) return;
        $itemTag = ItemUtils::getItemTag($item, "coinbomb_item");

        foreach ($itemEntity->getViewers() as $human) {
            if ($human instanceof Player) {
                if ($human->distance($itemEntity) <= 1 && $itemTag != $human->getName()) {
                    $human->playSound("random.pop", 1.0, 1.0, [$human]);
                    CoinProvider::addCoins($human->getName(), mt_rand(5, 20));
                    $itemEntity->close();
                    break;
                }
            }
        }
    }
}