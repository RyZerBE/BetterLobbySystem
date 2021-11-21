<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\Listener;

class BlockFormListener implements Listener {
    public function onBlockForm(BlockFormEvent $event): void{
        $event->setCancelled();
    }
}