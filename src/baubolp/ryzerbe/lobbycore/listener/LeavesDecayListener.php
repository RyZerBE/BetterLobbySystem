<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\Listener;

class LeavesDecayListener implements Listener
{

    public function decay(LeavesDecayEvent $event)
    {
        $event->setCancelled();
    }
}