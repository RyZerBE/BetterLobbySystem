<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\Listener;
use ryzerbe\core\event\player\networklevel\PlayerLevelUpEvent;

class PlayerLevelUpListener implements Listener {
    public function onLevelUp(PlayerLevelUpEvent $event): void {
        LobbyPlayerCache::getLobbyPlayer($event->getPlayer())?->updateScoreboard();
    }
}