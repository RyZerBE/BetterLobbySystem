<?php

namespace baubolp\ryzerbe\lobbycore\entity\hypetrain;

use baubolp\ryzerbe\lobbycore\provider\ItemProvider;
use pocketmine\entity\Entity;
use pocketmine\entity\Vehicle;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;
use pocketmine\Server;
use function is_null;

abstract class BaseHypeTrain extends Vehicle {

    /** @var Entity|null  */
    protected $rider = null;

    /** @var bool  */
    protected $wasRiderFixTeleport = false;

    /**
     * @param Entity|null $rider
     */
    public function setRider(?Entity $rider): void {
        $this->rider = $rider;
        $this->setRiddenByEntity($rider);

        if(!is_null($rider)) {
            $rider->setRiding(true);
            $rider->setRidingEntity($this);
        }
    }

    /**
     * @return Entity|null
     */
    public function getRider(): ?Entity {
        return $this->rider;
    }

    /**
     * @param Entity $rider
     */
    public function sendEntityLink(Entity $rider): void {
        $rider->getDataPropertyManager()->setVector3(Entity::DATA_RIDER_SEAT_POSITION, $this->getRiderSeatPosition(), true);
        $rider->getDataPropertyManager()->setByte(Entity::DATA_CONTROLLING_RIDER_SEAT_NUMBER, 0);

        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($this->getId(), $rider->getId(), EntityLink::TYPE_RIDER, true, true);
        Server::getInstance()->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk);
    }

    /**
     * @param Entity $entity
     */
    public function removeLink(Entity $entity): void {
        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($this->getId(), $entity->getId(), EntityLink::TYPE_REMOVE, true, true);
        Server::getInstance()->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk);
    }

    public function updateRidingState(): void {
        $rider = $this->getRider();
        if(is_null($rider) || $rider->isClosed()) return;

        $rider->setPosition($this->add($this->getRiderSeatPosition()));
    }

    /**
     * @param int $seatNumber
     * @return Vector3
     */
    public function getRiderSeatPosition(int $seatNumber = 0): Vector3 {
        return new Vector3(0, 1, 0);
    }

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool{
        if(is_null($this->getRider())) return parent::onUpdate($currentTick);
        $this->updateRidingState();
        if($currentTick % 15 === 0) {
            $this->sendEntityLink($this->getRider());
        }
        $this->getRider()->resetFallDistance();
        return parent::onUpdate($currentTick);
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void{
        if(!$source instanceof EntityDamageByEntityEvent) return;
        $attacker = $source->getDamager();
        if(!$attacker instanceof Player || !is_null($this->getRider()) || $attacker->isRiding()) return;

        $this->setRider($attacker);
        $this->sendEntityLink($attacker);
    }

    /**
     * @param Entity $entity
     */
    public function onRiderLeave(Entity $entity): void {
        $rider = $this->getRider();
        if(is_null($rider) || $rider->getId() !== $entity->getId() || $this->wasRiderFixTeleport) return;
        $this->setRider(null);
        $this->removeLink($entity);

        $this->setRiddenByEntity(null);
        $rider->setRidingEntity(null);

        $rider->setRiding(false);
        $entity->teleport($entity);

        if($entity instanceof Player){
            ItemProvider::giveLobbyItems($entity);
        }
    }
}