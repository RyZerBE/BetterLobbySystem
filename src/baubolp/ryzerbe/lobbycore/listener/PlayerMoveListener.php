<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\Server;
use function is_null;

class PlayerMoveListener implements Listener {

    /**
     * @param PlayerMoveEvent $event
     */
    public function onMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);

        if($player->getY() <= 0) {
            $player->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn()->add(0, 1));
        }
        if($lobbyPlayer === null) return;
        $lobbyPlayer->setHasMoved();
    }
}