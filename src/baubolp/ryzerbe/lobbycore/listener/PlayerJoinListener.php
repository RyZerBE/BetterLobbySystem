<?php


namespace baubolp\ryzerbe\lobbycore\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class PlayerJoinListener implements Listener
{

    public function join(PlayerJoinEvent $event)
    {
        $event->setJoinMessage("");
    }
}