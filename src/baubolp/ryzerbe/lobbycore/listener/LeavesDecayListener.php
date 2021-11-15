<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\Listener;

class LeavesDecayListener implements Listener {
    public function onLeavesDecay(LeavesDecayEvent $event): void{
        $event->setCancelled();
    }
}