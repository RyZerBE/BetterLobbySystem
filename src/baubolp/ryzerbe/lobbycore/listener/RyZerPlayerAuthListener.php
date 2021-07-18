<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\core\listener\own\RyZerPlayerAuthEvent;
use baubolp\ryzerbe\lobbycore\animation\AnimationProvider;
use baubolp\ryzerbe\lobbycore\animation\type\PlayerJoinAnimation;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayer;
use pocketmine\event\Listener;

class RyZerPlayerAuthListener implements Listener
{
    /**
     * @param \baubolp\core\listener\own\RyZerPlayerAuthEvent $event
     */
    public function auth(RyZerPlayerAuthEvent $event)
    {
        $player = $event->getRyZerPlayer()->getPlayer();
        $lobbyPlayer = new LobbyPlayer($player);
        $lobbyPlayer->load();
        $lobbyPlayer->register();

        if (in_array($player->getName(), PlayerJoinNetworkListener::$willPlay)) {
            if ($lobbyPlayer->isJoinAnimationEnabled())
                AnimationProvider::addActiveAnimation(new PlayerJoinAnimation($player));
            unset(PlayerJoinNetworkListener::$willPlay[array_search($player->getName(), PlayerJoinNetworkListener::$willPlay)]);
        }
    }
}