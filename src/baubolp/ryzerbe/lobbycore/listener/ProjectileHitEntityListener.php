<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\ryzerbe\lobbycore\cosmetic\type\special\SpidermanGunSpecialCosmetic;
use baubolp\ryzerbe\lobbycore\util\BlockQueue;
use pocketmine\block\Block;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class ProjectileHitEntityListener implements Listener
{

    public function projectileEntity(ProjectileHitEntityEvent $event)
    {
        $hitEntity = $event->getEntityHit();
        $entity = $event->getEntity();
        if (!$hitEntity instanceof Player) return;

        if ($entity->namedtag->getString("identifier", "#CoVid19") == (new SpidermanGunSpecialCosmetic())->getIdentifier()) {
            $entity->getLevel()->setBlock($entity->asVector3(), Block::get(Block::WEB));
            BlockQueue::addBlock(Block::get(Block::AIR), 60);
        }
    }
}