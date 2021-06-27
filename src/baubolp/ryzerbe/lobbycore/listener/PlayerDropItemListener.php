<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;

class PlayerDropItemListener implements Listener
{

    public function dropItem(PlayerDropItemEvent $event)
    {
        $event->setCancelled();
    }
}