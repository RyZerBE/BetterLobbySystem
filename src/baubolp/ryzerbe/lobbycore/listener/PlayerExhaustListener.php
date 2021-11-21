<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;

class PlayerExhaustListener implements Listener {
    public function onPlayerExhaust(PlayerExhaustEvent $event): void{
        $event->getPlayer()->setFood(20.0);
    }
}