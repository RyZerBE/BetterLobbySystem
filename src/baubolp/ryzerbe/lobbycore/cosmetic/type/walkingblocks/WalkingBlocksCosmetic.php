<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\walkingblocks;

use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\Cosmetic;
use baubolp\ryzerbe\lobbycore\util\BlockQueue;
use pocketmine\block\Block;
use pocketmine\Player;
use function array_map;
use function array_rand;
use function in_array;
use function mt_rand;

abstract class WalkingBlocksCosmetic extends Cosmetic {

    /**
     * @return Block[]
     */
    abstract public function getBlocks(): array;

    /**
     * @return array
     */
    public function getSecondBlockLayer(): array {
        return [];
    }

    /**
     * @return int
     */
    public function getCategory(): int{
        return CosmeticManager::CATEGORY_ITEM_RAIN;
    }

    /**
     * @param Player $player
     * @param int $currentTick
     */
    public function onUpdate(Player $player, int $currentTick): void{
        if($currentTick % 5 !== 0 || $this->getBlocks() === []) return;
        $this->placeBlocks($player, $this->getBlocks(), ($player->getFloorY() - 1));
        if($this->getSecondBlockLayer() !== [] && mt_rand(0, 100) > 85) {
            $this->placeBlocks($player, $this->getSecondBlockLayer(), $player->getFloorY(), false);
        }
    }

    /**
     * @param Player $player
     * @param array $blocks
     * @param int $y
     * @param bool $solid
     */
    protected function placeBlocks(Player $player, array $blocks, int $y, bool $solid = true): void {
        $level = $player->getLevel();
        $vector3 = $player->asVector3()->floor();

        $blockIds = array_map(function(Block $block): int {
            return $block->getId();
        }, $this->getBlocks());

        for($x = -1; $x <= 1; $x++) {
            for($z = -1; $z <= 1; $z++) {
                if(mt_rand(1, 100) > 60) continue;
                $block = $blocks[array_rand($blocks)];

                $tempX = $vector3->x + $x;
                $tempZ = $vector3->z + $z;
                $lBlock = $level->getBlockAt($tempX, $y, $tempZ);
                if(BlockQueue::isUsed($lBlock)) continue;
                if($solid) {
                    if(!$lBlock->isSolid()) continue;
                } else {
                    if(!$lBlock->canBeReplaced() || !in_array($level->getBlockIdAt($tempX, $y - 1, $tempZ), $blockIds)) continue;
                }
                BlockQueue::addBlock($lBlock, mt_rand(20, 60));

                $level->setBlockIdAt($tempX, $y, $tempZ, $block->getId());
                $level->setBlockDataAt($tempX, $y, $tempZ, $block->getDamage());
            }
        }
    }
}