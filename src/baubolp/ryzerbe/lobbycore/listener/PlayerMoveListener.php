<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;

class PlayerMoveListener implements Listener {
    public function onPlayerMove(PlayerMoveEvent $event): void{
        $player = $event->getPlayer();
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if($lobbyPlayer === null) return;
        $lobbyPlayer->setHasMoved();
    }
}