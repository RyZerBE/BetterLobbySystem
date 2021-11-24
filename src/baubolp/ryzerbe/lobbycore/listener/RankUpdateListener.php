<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\Listener;
use ryzerbe\core\event\player\rank\PlayerRankUpdateEvent;

class RankUpdateListener implements Listener {

    public function rank(PlayerRankUpdateEvent $event){
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($event->getPlayer());
        if($lobbyPlayer === null) return;
        $lobbyPlayer->updateScoreboard();
    }
}