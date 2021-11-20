<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\ryzerbe\lobbycore\animation\AnimationProvider;
use baubolp\ryzerbe\lobbycore\animation\type\CoinbombAnimation;
use baubolp\ryzerbe\lobbycore\cosmetic\type\vehicle\hypetrain\HypeTrain;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use ryzerbe\core\util\ItemUtils;

class PlayerDropItemListener implements Listener {
    public function onPlayerDropItem(PlayerDropItemEvent $event): void{
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