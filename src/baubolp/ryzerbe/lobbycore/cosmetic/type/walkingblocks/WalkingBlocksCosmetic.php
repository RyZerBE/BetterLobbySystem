<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\type\walkingblocks;

use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\Cosmetic;
use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\util\BlockQueue;
use matze\gommejar\session\SessionManager;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\block\Slab;
use pocketmine\block\Trapdoor;
use pocketmine\Player;
use function array_map;
use function array_rand;
use function in_array;
use function mt_rand;

abstract class WalkingBlocksCosmetic extends Cosmetic {
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
        $this->placeBlocks($player, $this->getBlocks(), ($player->y - 0.25));
        if($this->getSecondBlockLayer() !== []){
            $this->placeBlocks($player, $this->getSecondBlockLayer(), $player->getFloorY(), false, 10, $this->getMaxSecondLayerBlocks());
        }
    }

    /**
     * @return Block[]
     */
    abstract public function getBlocks(): array;

    /**
     * @param Player $player
     * @param array $blocks
     * @param float $y
     * @param bool $solid
     * @param int $rarity
     * @param int $max
     */
    protected function placeBlocks(Player $player, array $blocks, float $y, bool $solid = true, int $rarity = 60, int $max = 9999): void{
        $level = $player->getLevel();
        $vector3 = $player->asVector3()->floor();
        $blockIds = array_map(function(Block $block): int{
            return $block->getId();
        }, $this->getBlocks());
        for($x = -1; $x <= 1; $x++){
            for($z = -1; $z <= 1; $z++){
                if($max <= 0 || (!$solid && $x === 0 && $z === 0)) return;
                if(mt_rand(1, 100) > $rarity) continue;
                $block = $blocks[array_rand($blocks)];
                $tempX = $vector3->x + $x;
                $tempZ = $vector3->z + $z;
                $lBlock = $level->getBlockAt($tempX, $y, $tempZ);
                $jarBlock = false;
                if(Loader::$jumpAndRunEnabled){
                    foreach(SessionManager::getInstance()->getSessions() as $session){
                        if(($session->getTargetVector3() !== null && $session->getTargetVector3()->equals($lBlock->floor())) || ($session->getLastVector3() !== null && $session->getLastVector3()->equals($lBlock->floor()))){
                            $jarBlock = true;
                            break;
                        }
                    }
                }
                if(BlockQueue::isUsed($lBlock) || $jarBlock) continue;
                if($solid){
                    if($lBlock instanceof Slab && ($lBlock->getDamage()&0x08) === 0){
                        $slabs = $this->getSlabBlocks();
                        if(empty($slabs)) continue;
                        $block = $slabs[array_rand($slabs)];
                    }
                    elseif(!$this->canBeReplaced($lBlock)) continue;
                }
                else{
                    if(!$lBlock->canBeReplaced() || !in_array($level->getBlockIdAt($tempX, $y - 1, $tempZ), $blockIds)) continue;
                }
                BlockQueue::addBlock($lBlock, mt_rand(20, 60));
                $level->setBlockIdAt($tempX, $y, $tempZ, $block->getId());
                $level->setBlockDataAt($tempX, $y, $tempZ, $block->getDamage());
                $max--;
            }
        }
    }

    /**
     * @return array
     */
    public function getSlabBlocks(): array{
        return [];
    }

    /**
     * @param Block $block
     * @return bool
     */
    private function canBeReplaced(Block $block): bool{
        return $block->isSolid() && !$block instanceof Trapdoor && !in_array($block->getId(), [
                BlockIds::END_PORTAL_FRAME,
                BlockIds::CARPET,
            ]);
    }

    /**
     * @return array
     */
    public function getSecondBlockLayer(): array{
        return [];
    }

    /**
     * @return int
     */
    public function getMaxSecondLayerBlocks(): int{
        return 1;
    }
}