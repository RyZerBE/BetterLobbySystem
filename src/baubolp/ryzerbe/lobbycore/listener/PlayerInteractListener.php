<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\ryzerbe\lobbycore\provider\ItemProvider;
use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use function in_array;

class PlayerInteractListener implements Listener
{
    private const NOT_INTERACTABLE_BLOCKS = [
        Block::DRAGON_EGG,
        Block::CHEST,
        Block::FURNACE
    ];

    public function interact(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if (ItemProvider::execItem($player) || in_array($block->getId(), self::NOT_INTERACTABLE_BLOCKS)) {
            $event->setCancelled();
        }
    }
}