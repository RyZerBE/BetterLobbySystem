<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use baubolp\ryzerbe\lobbycore\player\LobbyPlayer;
use pocketmine\event\Listener;
use ryzerbe\core\event\player\RyZerPlayerAuthEvent;

class RyZerPlayerAuthListener implements Listener {
    public function onRyZerPlayerAuth(RyZerPlayerAuthEvent $event): void{
        $player = $event->getRyZerPlayer()->getPlayer();
        $lobbyPlayer = new LobbyPlayer($player);
        $lobbyPlayer->load();
        $lobbyPlayer->register();
    }
}