<?php

namespace baubolp\ryzerbe\lobbycore\entity;

use pocketmine\entity\object\ItemEntity;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use function floor;
use function mt_rand;

class ItemRainItemEntity extends ItemEntity {
    /** @var int */
    private int $groundTicks = 0;
    /** @var int */
    private int $lifetime;

    /**
     * ItemRainItemEntity constructor.
     *
     * @param Level $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);
        $this->lifetime = mt_rand(10, 20);
    }

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool{
        if($this->isClosed() || $this->isFlaggedForDespawn()) return false;
        if($this->isOnGround() || $this->isInsideOfWater()){
            $this->groundTicks++;
        }
        if($this->groundTicks > $this->lifetime || $this->y <= 0) $this->flagForDespawn();
        return parent::onUpdate($currentTick);
    }

    /**
     * @param int $tickDiff
     * @return bool
     */
    public function entityBaseTick(int $tickDiff = 1): bool{
        return true;
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
    public function onCollideWithPlayer(Player $player): void{
    }

    protected function tryChangeMovement(): void{
        $friction = 1 - $this->drag;
        if($this->applyDragBeforeGravity()){
            $this->motion->y *= $friction;
        }
        $this->applyGravity();
        if(!$this->applyDragBeforeGravity()){
            $this->motion->y *= $friction;
        }
        if($this->onGround){
            $friction *= $this->level->getBlockAt((int)floor($this->x), (int)floor($this->y - 1), (int)floor($this->z))->getFrictionFactor();
        }
        $this->motion->x *= $friction;
        $this->motion->z *= $friction;
    }

    public function applyGravity(): void{
        $this->motion->y -= $this->gravity;
    }
}