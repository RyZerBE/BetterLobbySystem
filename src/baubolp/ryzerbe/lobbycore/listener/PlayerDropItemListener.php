<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\core\util\ItemUtils;
use baubolp\ryzerbe\lobbycore\animation\AnimationProvider;
use baubolp\ryzerbe\lobbycore\animation\type\CoinbombAnimation;
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
            if (ItemUtils::getItemTag($item, "lobby_item") === "tag_coinbomb") {
                $player = LobbyPlayerCache::getLobbyPlayer($player);
                if ($player === null) return;

                $player->removeCoinbomb();
                AnimationProvider::addActiveAnimation(new CoinbombAnimation($player->getPlayer()));
            }
        }
    }
}