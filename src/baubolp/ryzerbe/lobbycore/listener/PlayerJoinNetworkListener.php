<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use BauboLP\Cloud\Events\PlayerJoinNetworkEvent;
use pocketmine\event\Listener;

class PlayerJoinNetworkListener implements Listener
{
    /** @var array  */
    public static $willPlay = [];

    public function joinNetwork(PlayerJoinNetworkEvent $event)
    {
        self::$willPlay[] = $event->getPlayerName();
    }
}