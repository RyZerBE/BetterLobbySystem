<?php

namespace baubolp\ryzerbe\lobbycore\entity\hypetrain;

use pocketmine\math\Vector3;
use function is_null;

class HypeTrainWagonEntity extends BaseHypeTrain {
    public const NETWORK_ID = self::MINECART;

    /** @var float  */
    public $height = 0.7;
    /** @var float  */
    public $width = 0.98;

    /** @var int  */
    public $wagonNumber = -1;

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool{
        $hypeTrain = $this->getOwningEntity();
        if(is_null($hypeTrain) || !$hypeTrain instanceof HypeTrainEntity || $hypeTrain->isClosed()) {
            $this->flagForDespawn();

            if(!is_null($this->getRider())) {
                $this->onRiderLeave($this->getRider());
            }
            return false;
        }
        $entry = $hypeTrain->getCacheEntry(($currentTick - (HypeTrainEntity::TICK_DELAY * $this->wagonNumber)));
        if(is_null($entry)) return true;

        $this->yaw = $entry->getYaw();
        $this->pitch = $entry->getPitch();
        $this->motion = new Vector3(
            $entry->getVector3()->x - $this->x,
            $entry->getVector3()->y - $this->y,
            $entry->getVector3()->z - $this->z
        );

        $this->yaw += 270;//Fix rotation
        if($this->yaw > 360) $this->yaw -= 360;

        $rider = $this->getRider();
        if($this->distanceSquared($entry->getVector3()) > 0.5) {
            $this->teleport($entry->getVector3());
            if(!is_null($rider)){
                $this->sendEntityLink($rider);
                $this->setRider($rider);
            }
        }
        $this->updateMovement();
        return parent::onUpdate($currentTick);
    }
}