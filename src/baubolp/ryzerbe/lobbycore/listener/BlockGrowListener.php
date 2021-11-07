<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use pocketmine\event\block\BlockGrowEvent;
use pocketmine\event\Listener;

class BlockGrowListener implements Listener {
    public function onBlockGrow(BlockGrowEvent $event): void{
        $event->setCancelled();
    }
}