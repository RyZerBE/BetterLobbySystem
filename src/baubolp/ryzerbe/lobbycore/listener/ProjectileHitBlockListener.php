<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use baubolp\ryzerbe\lobbycore\cosmetic\type\special\SpidermanGunSpecialCosmetic;
use baubolp\ryzerbe\lobbycore\util\BlockQueue;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\Listener;

class ProjectileHitBlockListener implements Listener {
    public function onProjectileHitBlock(ProjectileHitBlockEvent $event): void{
        $NOT_REPLACEABLE_BLOCKS = [
            BlockIds::LADDER,
            BlockIds::SIGN_POST,
            BlockIds::WALL_SIGN,
        ];
        $block = $event->getBlockHit();
        $entity = $event->getEntity();
        if($entity->namedtag->getString("identifier", "#CoVid19") == (new SpidermanGunSpecialCosmetic())->getIdentifier()){
            if(BlockQueue::isUsed($block->asVector3()) || in_array($block->getId(), $NOT_REPLACEABLE_BLOCKS)) return;
            BlockQueue::addBlock($block, 60);
            $block->getLevel()->setBlock($block->asVector3(), Block::get(BlockIds::WEB));
        }
    }
}