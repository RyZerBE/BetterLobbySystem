<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use pocketmine\event\block\BlockGrowEvent;
use pocketmine\event\Listener;

class BlockGrowListener implements Listener
{

    public function grow(BlockGrowEvent $event)
    {
        $event->setCancelled();
    }
}