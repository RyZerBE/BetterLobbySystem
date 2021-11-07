<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use baubolp\core\event\PlayerCoinsAddEvent;
use baubolp\core\event\PlayerCoinsRemoveEvent;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\Listener;

class CoinUpdateListener implements Listener {
    public function onPlayerCoinsAdd(PlayerCoinsAddEvent $event): void{
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($event->getPlayer());
        if($lobbyPlayer === null) return;
        $lobbyPlayer->updateScoreboard();
    }

    public function onPlayerCoinsRemove(PlayerCoinsRemoveEvent $event): void{
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($event->getPlayer());
        if($lobbyPlayer === null) return;
        $lobbyPlayer->updateScoreboard();
    }
}