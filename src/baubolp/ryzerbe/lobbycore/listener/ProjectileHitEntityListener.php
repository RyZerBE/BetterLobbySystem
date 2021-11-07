<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\ryzerbe\lobbycore\cosmetic\type\special\SpidermanGunSpecialCosmetic;
use baubolp\ryzerbe\lobbycore\util\BlockQueue;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class ProjectileHitEntityListener implements Listener {
    public function onProjectileHitEntity(ProjectileHitEntityEvent $event): void{
        $hitEntity = $event->getEntityHit();
        $entity = $event->getEntity();
        if (!$hitEntity instanceof Player) return;

        if ($entity->namedtag->getString("identifier", "#CoVid19") == (new SpidermanGunSpecialCosmetic())->getIdentifier()) {
            BlockQueue::addBlock($entity->getLevel()->getBlock($entity->asVector3()->add(0, -1)), 60);
            $entity->getLevel()->setBlock($entity->asVector3()->add(0, -1), Block::get(BlockIds::WEB));
        }
    }
}