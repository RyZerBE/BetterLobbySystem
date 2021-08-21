<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use baubolp\core\event\PlayerLevelUpEvent;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\Listener;

class PlayerLevelUpListener implements Listener {

    /**
     * @param PlayerLevelUpEvent $event
     */
    public function onLevelUp(PlayerLevelUpEvent $event): void {
        LobbyPlayerCache::getLobbyPlayer($event->getPlayer())?->updateScoreboard();
    }
}