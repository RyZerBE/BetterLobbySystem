<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\ryzerbe\lobbycore\provider\ItemProvider;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class PlayerInteractListener implements Listener
{

    public function interact(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        if (ItemProvider::execItem($player)) {
            $event->setCancelled();
        }
    }
}