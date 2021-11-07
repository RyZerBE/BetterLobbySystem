<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;

class BlockBreakListener implements Listener
{

    public function onBlockBreak(BlockBreakEvent $event): void{
        $player = $event->getPlayer();
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if($lobbyPlayer === null) {
            $event->setCancelled();
            return;
        }
        if(!$lobbyPlayer->enabledBuildMode()) $event->setCancelled();
    }
}