<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\Listener;
use ryzerbe\core\event\player\coin\PlayerCoinsAddEvent;
use ryzerbe\core\event\player\coin\PlayerCoinsRemoveEvent;

class CoinUpdateListener implements Listener {

    /**
     * @param PlayerCoinsAddEvent $event
     */
    public function add(PlayerCoinsAddEvent $event){
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($event->getPlayer());
        if($lobbyPlayer === null) return;
        $lobbyPlayer->updateScoreboard();
    }

    /**
     * @param PlayerCoinsRemoveEvent $event
     */
    public function remove(PlayerCoinsRemoveEvent $event){
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($event->getPlayer());
        if($lobbyPlayer === null) return;
        $lobbyPlayer->updateScoreboard();
    }
}