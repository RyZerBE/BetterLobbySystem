<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\core\util\ItemUtils;
use baubolp\ryzerbe\lobbycore\animation\AnimationProvider;
use baubolp\ryzerbe\lobbycore\animation\type\CoinbombAnimation;
use baubolp\ryzerbe\lobbycore\cosmetic\type\vehicle\hypetrain\HypeTrain;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;

class PlayerDropItemListener implements Listener
{

    public function dropItem(PlayerDropItemEvent $event)
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        $event->setCancelled();

        if (ItemUtils::hasItemTag($item, "lobby_item")) {
            switch(ItemUtils::getItemTag($item, "lobby_item")) {
                case "tag_coinbomb": {
                    $player = LobbyPlayerCache::getLobbyPlayer($player);
                    if ($player === null) return;

                    $player->removeCoinbomb();
                    $player->getPlayer()->getInventory()->clear($player->getPlayer()->getInventory()->getHeldItemIndex());
                    AnimationProvider::addActiveAnimation(new CoinbombAnimation($player->getPlayer()));
                    break;
                }
                case "tag_hypetrain": {
                    $player = LobbyPlayerCache::getLobbyPlayer($player);
                    if ($player === null) return;

                    $player->removeHypeTrains();
                    $player->getPlayer()->getInventory()->clear($player->getPlayer()->getInventory()->getHeldItemIndex());
                    HypeTrain::spawn($player->getPlayer());
                    break;
                }
            }
        }
    }
}