<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\ryzerbe\lobbycore\player\LobbyPlayer;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class PlayerJoinListener implements Listener
{

    public function join(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $lobbyPlayer = new LobbyPlayer($player);
        LobbyPlayerCache::registerLobbyPlayer($lobbyPlayer);
        $lobbyPlayer->load();
    }
}