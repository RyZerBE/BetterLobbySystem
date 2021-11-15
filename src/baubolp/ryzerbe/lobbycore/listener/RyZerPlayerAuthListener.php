<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\ryzerbe\lobbycore\player\LobbyPlayer;
use pocketmine\event\Listener;
use ryzerbe\core\event\player\RyZerPlayerAuthEvent;

class RyZerPlayerAuthListener implements Listener
{
    /**
     * @param RyZerPlayerAuthEvent $event
     */
    public function auth(RyZerPlayerAuthEvent $event)
    {
        $player = $event->getRyZerPlayer()->getPlayer();
        $lobbyPlayer = new LobbyPlayer($player);
        $lobbyPlayer->load();
        $lobbyPlayer->register();
    }
}