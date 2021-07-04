<?php

namespace baubolp\ryzerbe\lobbycore\entity;

use pocketmine\entity\object\ItemEntity;
use pocketmine\Player;
use function floor;

class ItemRainItemEntity extends ItemEntity {

    /** @var int  */
    private $groundTicks = 0;

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool{
        if($this->isClosed() || $this->isFlaggedForDespawn()) return false;
        if($this->isOnGround() || $this->isInsideOfWater()) {
            $this->groundTicks++;
        }
        if($this->groundTicks > 15 || $this->y <= 0) $this->flagForDespawn();
        return parent::onUpdate($currentTick);
    }

    /**
     * @param int $tickDiff
     * @return bool
     */
    public function entityBaseTick(int $tickDiff = 1): bool{
        return true;
    }

    public function applyGravity(): void{
        $this->motion->y -= $this->gravity;
    }

    protected function tryChangeMovement() : void{
        $friction = 1 - $this->drag;

        if($this->applyDragBeforeGravity()){
            $this->motion->y *= $friction;
        }

        $this->applyGravity();

        if(!$this->applyDragBeforeGravity()){
            $this->motion->y *= $friction;
        }

        if($this->onGround){
            $friction *= $this->level->getBlockAt((int) floor($this->x), (int) floor($this->y - 1), (int) floor($this->z))->getFrictionFactor();
        }

        $this->motion->x *= $friction;
        $this->motion->z *= $friction;
    }

    /**
     * @return bool
     */
    public function canSaveWithChunk(): bool{
        return false;
    }

    /**
     * @param Player $player
     */
    public function onCollideWithPlayer(Player $player): void{}
}