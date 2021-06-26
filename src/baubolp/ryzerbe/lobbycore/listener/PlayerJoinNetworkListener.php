<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use BauboLP\Cloud\Events\PlayerJoinNetworkEvent;
use baubolp\ryzerbe\lobbycore\animation\AnimationProvider;
use baubolp\ryzerbe\lobbycore\animation\type\PlayerJoinAnimation;
use baubolp\ryzerbe\lobbycore\Loader;
use pocketmine\event\Listener;

class PlayerJoinNetworkListener implements Listener
{

    public function joinNetwork(PlayerJoinNetworkEvent $event)
    {
        if (($player = Loader::getInstance()->getServer()->getPlayerExact($event->getPlayerName())) != null) {
            AnimationProvider::addActiveAnimation(new PlayerJoinAnimation($player));
        }
    }
}