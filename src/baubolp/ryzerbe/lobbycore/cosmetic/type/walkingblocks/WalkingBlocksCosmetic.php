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
    public function getMaxSecondLayerBlocks(): int {
        return 1;
    }

    /**
     * @return int
     */
    public function getCategory(): int{
        return CosmeticManager::CATEGORY_WALKING_BLOCKS;
    }

    /**
     * @param Player $player
     * @param int $currentTick
     */
    public function onUpdate(Player $player, int $currentTick): void{
        if($currentTick % 5 !== 0 || $this->getBlocks() === []) return;
        $this->placeBlocks($player, $this->getBlocks(), ($player->getFloorY() - 1));
        if($this->getSecondBlockLayer() !== []) {
            $this->placeBlocks($player, $this->getSecondBlockLayer(), $player->getFloorY(), false, 10, $this->getMaxSecondLayerBlocks());
        }
    }

    /**
     * @param Player $player
     * @param array $blocks
     * @param int $y
     * @param bool $solid
     * @param int $rarity
     * @param int $max
     */
    protected function placeBlocks(Player $player, array $blocks, int $y, bool $solid = true, int $rarity = 60, int $max = 9999): void {
        $level = $player->getLevel();
        $vector3 = $player->asVector3()->floor();

        $blockIds = array_map(function(Block $block): int {
            return $block->getId();
        }, $this->getBlocks());

        for($x = -1; $x <= 1; $x++) {
            for($z = -1; $z <= 1; $z++) {
                if($max <= 0) return;
                if(mt_rand(1, 100) > $rarity) continue;
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
                $max--;
            }
        }
    }
}