<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\Listener;

class BlockUpdateListener implements Listener
{

    public function update(BlockUpdateEvent $event)
    {
        $event->setCancelled();
    }
}