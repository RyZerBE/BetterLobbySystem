<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use function in_array;

class EntityDamageListener implements Listener  {
    public function onEntityDamage(EntityDamageEvent $event): void{
        $player = $event->getEntity();
        if(!$player instanceof Player) return;
        $cause = $event->getCause();
        if(in_array($cause, [EntityDamageEvent::CAUSE_VOID, EntityDamageEvent::CAUSE_SUFFOCATION])) {
            $player->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn()->add(0.5, 1, 0.5));
        }
        $event->setCancelled();
    }
}