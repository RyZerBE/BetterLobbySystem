<?php

namespace baubolp\ryzerbe\lobbycore\util\cache;

use pocketmine\math\Vector3;

class HypeTrainCacheEntry {

    /** @var float  */
    private $yaw;
    /** @var float  */
    private $pitch;
    /** @var Vector3  */
    private $vector3;

    /**
     * HypeTrainCacheEntry constructor.
     * @param float $yaw
     * @param float $pitch
     * @param Vector3 $vector3
     */
    public function __construct(float $yaw, float $pitch, Vector3 $vector3){
        $this->yaw = $yaw;
        $this->pitch = $pitch;
        $this->vector3 = $vector3;
    }

    /**
     * @return float
     */
    public function getPitch(): float{
        return $this->pitch;
    }

    /**
     * @return Vector3
     */
    public function getVector3(): Vector3{
        return $this->vector3;
    }

    /**
     * @return float
     */
    public function getYaw(): float{
        return $this->yaw;
    }

    /**
     * @param float $yaw
     * @param float $pitch
     * @param Vector3 $vector3
     */
    public function setComponents(float $yaw, float $pitch, Vector3 $vector3): void {
        $this->yaw = $yaw;
        $this->pitch = $pitch;
        $this->vector3 = $vector3;
    }
}