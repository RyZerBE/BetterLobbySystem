<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use baubolp\ryzerbe\lobbycore\provider\ItemProvider;
use pocketmine\block\BlockIds;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use function in_array;

class PlayerInteractListener implements Listener {
    private const NOT_INTERACTABLE_BLOCKS = [
        BlockIds::DRAGON_EGG,
        BlockIds::CHEST,
        BlockIds::FURNACE,
    ];

    public function onPlayerInteract(PlayerInteractEvent $event): void{
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if(ItemProvider::execItem($player) || in_array($block->getId(), self::NOT_INTERACTABLE_BLOCKS)){
            $event->setCancelled();
        }
        if($lobbyPlayer !== null && $lobbyPlayer->enabledBuildMode()){
            $event->setCancelled(false);
        }
    }
}