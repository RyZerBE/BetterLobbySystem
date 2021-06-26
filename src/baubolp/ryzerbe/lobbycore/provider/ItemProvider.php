<?php


namespace baubolp\ryzerbe\lobbycore\provider;


use pocketmine\Player;

class ItemProvider
{

    public static function giveLobbyItems(Player $player): void
    {
        self::clearAllInventories($player);
        //TODO: IMPLEMENT ITEMS
    }

    public static function clearAllInventories(Player $player): void
    {
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->removeAllEffects();
        $player->getCursorInventory()->clearAll();
        $player->getCraftingGrid()->clearAll();
        $player->getOffHandInventory()->clearAll();
    }
}