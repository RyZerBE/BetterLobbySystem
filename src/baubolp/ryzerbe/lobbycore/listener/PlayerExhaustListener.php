<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;

class PlayerExhaustListener implements Listener
{

    public function exhaust(PlayerExhaustEvent $event)
    {
        $event->getPlayer()->setFood(20.0);
    }
}