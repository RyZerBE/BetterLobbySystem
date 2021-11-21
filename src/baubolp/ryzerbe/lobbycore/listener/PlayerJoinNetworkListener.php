<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use BauboLP\Cloud\Events\PlayerJoinNetworkEvent;
use pocketmine\event\Listener;

class PlayerJoinNetworkListener implements Listener {
    public static array $willPlay = [];

    public function onPlayerJoinNetwork(PlayerJoinNetworkEvent $event): void{
        self::$willPlay[] = $event->getPlayerName();
    }
}