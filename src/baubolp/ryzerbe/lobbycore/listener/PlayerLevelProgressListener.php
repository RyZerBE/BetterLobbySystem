<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use baubolp\core\event\PlayerLevelProgressEvent;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\Listener;

class PlayerLevelProgressListener implements Listener {
    public function onLevelProgress(PlayerLevelProgressEvent $event): void {
        LobbyPlayerCache::getLobbyPlayer($event->getPlayer())?->updateScoreboard();
    }
}