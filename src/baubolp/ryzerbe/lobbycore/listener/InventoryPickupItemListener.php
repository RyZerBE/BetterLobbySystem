<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use ryzerbe\core\provider\CoinProvider;
use ryzerbe\core\util\ItemUtils;

class InventoryPickupItemListener implements Listener {
    public function pickup(InventoryPickupItemEvent $event): void {
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