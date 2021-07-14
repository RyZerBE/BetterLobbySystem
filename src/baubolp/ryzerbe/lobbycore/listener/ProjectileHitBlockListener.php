<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use baubolp\ryzerbe\lobbycore\cosmetic\type\special\SpidermanGunSpecialCosmetic;
use baubolp\ryzerbe\lobbycore\util\BlockQueue;
use pocketmine\block\Block;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;

class ProjectileHitBlockListener implements Listener
{

    public function projectile(ProjectileHitBlockEvent $event)
    {
        $NOT_REPLACEABLE_BLOCKS = [
            Block::LADDER,
            Block::SIGN_POST,
            Block::WALL_SIGN
        ];

        $block = $event->getBlockHit();
        $entity = $event->getEntity();
        if ($entity->namedtag->getString("identifier", "#CoVid19") == (new SpidermanGunSpecialCosmetic())->getIdentifier()) {
            if (BlockQueue::isUsed($block->asVector3()) || in_array($block->getId(), $NOT_REPLACEABLE_BLOCKS)) return;
            BlockQueue::addBlock($block, 60);

            $block->getLevel()->setBlock($block->asVector3(), Block::get(Item::WEB));
        }
    }
}