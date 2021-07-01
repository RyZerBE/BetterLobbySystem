<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\ryzerbe\lobbycore\animation\AnimationProvider;
use baubolp\ryzerbe\lobbycore\animation\type\PlayerJoinAnimation;
use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayer;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class PlayerJoinListener implements Listener
{

    public function join(PlayerJoinEvent $event)
    {
        $event->setJoinMessage("");
        $player = $event->getPlayer();
        $lobbyPlayer = new LobbyPlayer($player);
        LobbyPlayerCache::registerLobbyPlayer($lobbyPlayer);
        $lobbyPlayer->load();

        if(in_array($player->getName(), PlayerJoinNetworkListener::$willPlay))
            AnimationProvider::addActiveAnimation(new PlayerJoinAnimation($player));

        //todo: remove this
        foreach(CosmeticManager::getInstance()->getCategories() as $category) {
            foreach($category->getCosmetics() as $cosmetic) {
                $lobbyPlayer->activateCosmetic($cosmetic);
            }
        }
    }
}