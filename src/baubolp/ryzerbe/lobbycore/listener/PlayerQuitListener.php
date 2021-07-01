<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerQuitListener implements Listener
{

    public function quit(PlayerQuitEvent $event)
    {
        $event->setQuitMessage("");
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($event->getPlayer()->getName());
        if($lobbyPlayer === null) return;

        $lobbyPlayer->unregister();
    }
}