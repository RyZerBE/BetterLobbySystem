<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\core\listener\own\RyZerPlayerAuthEvent;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayer;
use pocketmine\event\Listener;

class RyZerPlayerAuthListener implements Listener {
    public function onRyZerPlayerAuth(RyZerPlayerAuthEvent $event): void{
        $player = $event->getRyZerPlayer()->getPlayer();
        $lobbyPlayer = new LobbyPlayer($player);
        $lobbyPlayer->load();
        $lobbyPlayer->register();
    }
}