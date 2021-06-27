<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;

class BlockPlaceListener implements Listener
{

    public function place(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);

        if($lobbyPlayer === null) {
            $event->setCancelled();
            return;
        }

        if(!$lobbyPlayer->enabledBuildMode()) $event->setCancelled();
    }

}