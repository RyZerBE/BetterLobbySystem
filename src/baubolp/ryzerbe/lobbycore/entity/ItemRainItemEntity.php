<?php

namespace baubolp\ryzerbe\lobbycore\entity;

use pocketmine\entity\object\ItemEntity;
use pocketmine\Player;

class ItemRainItemEntity extends ItemEntity {

    /** @var int  */
    private $groundTicks = 0;

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool{
        if($this->isClosed() || $this->isFlaggedForDespawn()) return false;
        if($this->isOnGround()) {
            $this->groundTicks++;
        }
        if($this->groundTicks > 15) $this->flagForDespawn();
        return parent::onUpdate($currentTick);
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