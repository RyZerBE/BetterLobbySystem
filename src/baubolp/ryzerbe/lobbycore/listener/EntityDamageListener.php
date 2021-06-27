<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class EntityDamageListener implements Listener
{

    public function damage(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();
        if($entity instanceof Player)
        $event->setCancelled();
    }
}