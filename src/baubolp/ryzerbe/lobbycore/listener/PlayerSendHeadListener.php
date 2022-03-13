<?php

declare(strict_types=1);

namespace baubolp\ryzerbe\lobbycore\listener;

use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use javamapconverter\event\PlayerSendHeadEvent;
use pocketmine\event\Listener;

class PlayerSendHeadListener implements Listener {
    public function onPlayerSendHead(PlayerSendHeadEvent $event): void {
        $event->setCancelled(!(LobbyPlayerCache::getLobbyPlayer($event->getPlayer())?->isHeads() ?? true));
    }
}