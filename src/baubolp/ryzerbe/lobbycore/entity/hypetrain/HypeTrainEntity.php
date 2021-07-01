<?php

namespace baubolp\ryzerbe\lobbycore\entity\hypetrain;

use baubolp\ryzerbe\lobbycore\cosmetic\type\vehicle\hypetrain\HypeTrain;
use baubolp\ryzerbe\lobbycore\util\cache\HypeTrainCacheEntry;
use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\Server;
use function count;
use function is_null;

class HypeTrainEntity extends BaseHypeTrain {
    public const NETWORK_ID = self::MINECART;
    public const TICK_DELAY = 3;

    /** @var float  */
    public $height = 0.7;
    /** @var float  */
    public $width = 0.98;

    /** @var array  */
    private $cache = [];
    /** @var HypeTrainCacheEntry  */
    private $tempCacheEntry;

    /** @var array  */
    private $wagons = [];

    /** @var int  */
    private $totalWagons = 4;

    /** @var int  */
    private $noMovementTicks = 0;
    /** @var float  */
    private $lastRiderRotation = 0.0;

    /**
     * HypeTrainEntity constructor.
     * @param Level $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);
        $this->tempCacheEntry = new HypeTrainCacheEntry($this->yaw, $this->pitch, $this);
    }

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool{
        if($this->wasRiderFixTeleport) {
            $this->wasRiderFixTeleport = false;
            return parent::onUpdate($currentTick);
        }
        $rider = $this->getRider();
        if($rider === null || $rider->isClosed()){
            $this->flagForDespawn();
            return false;
        }
        $players = count(Server::getInstance()->getOnlinePlayers());
        if($this->totalWagons < $players) {
            $this->totalWagons = $players;
        }

        if(count($this->wagons) < $this->totalWagons) {
            $entry = $this->getCacheEntry(($currentTick - (HypeTrainEntity::TICK_DELAY * (count($this->wagons) + 1))));
            if(!is_null($entry)) {
                $vector3 = $this->asVector3();
                $vector3->y = 0;

                $wagon = new HypeTrainWagonEntity($this->getLevel(), Entity::createBaseNBT($vector3));
                $wagon->setOwningEntity($this);
                $wagon->wagonNumber = count($this->wagons) + 1;
                $wagon->spawnToAll();

                $this->addWagon($wagon);
            }
        }

        if(($rider->yaw + $rider->pitch) === $this->lastRiderRotation){
            $this->noMovementTicks++;
        } else {
            $this->noMovementTicks = 0;
        }
        $this->lastRiderRotation = $rider->yaw + $rider->pitch;
        if($this->noMovementTicks > 300) {
            $this->wasRiderFixTeleport = true;
            $this->noMovementTicks = 0;

            $this->flagForDespawn();
            $this->onRiderLeave($rider);

            $rider->teleport($rider);
            if($rider instanceof Player) HypeTrain::spawn($rider);
            return true;
        }

        $this->yaw = $rider->yaw;
        $this->pitch = $rider->pitch;
        $this->motion = $this->getDirectionVector()->divide(2);
        $this->addCacheEntry();

        $this->yaw += 270;//Fix rotation
        if($this->yaw > 360) $this->yaw -= 360;

        $this->updateMovement();
        return parent::onUpdate($currentTick);
    }

    /**
     * @param bool $immediate
     * @return bool
     */
    public function dismountEntity(bool $immediate = false): bool{
        if($this->wasRiderFixTeleport) return false;
        return parent::dismountEntity($immediate);
    }

    public function addCacheEntry(): void {
        if($this->totalWagons <= 0) return;
        $this->cache[Server::getInstance()->getTick()] = [
            "Yaw" => $this->yaw,
            "Pitch" => $this->pitch,
            "Vector3" => $this->asVector3()
        ];
    }

    /**
     * @param int $tick
     * @return HypeTrainCacheEntry|null
     */
    public function getCacheEntry(int $tick): ?HypeTrainCacheEntry {
        $data = $this->cache[$tick] ?? null;
        if(is_null($data)) return null;
        $this->tempCacheEntry->setComponents(
            $data["Yaw"], $data["Pitch"], $data["Vector3"]
        );
        //Prevent overflow
        if($tick === (Server::getInstance()->getTick() - (count($this->wagons) * self::TICK_DELAY))) {
            unset($this->cache[$tick]);
        }
        return $this->tempCacheEntry;
    }

    /**
     * @param HypeTrainWagonEntity $entity
     */
    public function addWagon(HypeTrainWagonEntity $entity): void {
        $this->wagons[] = $entity;
    }
}