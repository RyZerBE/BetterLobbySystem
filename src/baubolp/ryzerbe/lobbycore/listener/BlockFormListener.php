<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\Listener;

class BlockFormListener implements Listener
{

    public function form(BlockFormEvent $event)
    {
        $event->setCancelled();
    }
}