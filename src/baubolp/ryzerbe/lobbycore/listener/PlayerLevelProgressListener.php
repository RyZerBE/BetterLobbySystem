<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\Listener;
use ryzerbe\core\event\player\networklevel\PlayerLevelProgressEvent;

class PlayerLevelProgressListener implements Listener {

    /**
     * @param PlayerLevelProgressEvent $event
     */
    public function onLevelProgress(PlayerLevelProgressEvent $event): void {
        LobbyPlayerCache::getLobbyPlayer($event->getPlayer())?->updateScoreboard();
    }
}