<?php

namespace baubolp\ryzerbe\lobbycore\entity;

use ryzerbe\core\util\ItemUtils;
use pocketmine\entity\Vehicle;
use pocketmine\item\Item;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\sound\FizzSound;
use pocketmine\math\Vector3;
use function lcg_value;

class CoinBombMinecartEntity extends Vehicle {
    public const NETWORK_ID = self::CHEST_MINECART;

    /** @var float  */
    public $height = 0.7;
    /** @var float  */
    public $width = 0.98;
    /** @var string */
    public $dropperName = "";

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool{
        if($this->isClosed() || $this->isFlaggedForDespawn()) return false;
        if($this->isOnGround() || $this->ticksLived > (20 * 10)) {
            $this->flagForDespawn();
            $this->getLevel()->addSound(new FizzSound($this));
            $this->getLevel()->addParticle(new ExplodeParticle($this));

            $this->getLevel()->dropItem($this, ItemUtils::addItemTag(Item::get(Item::GOLD_NUGGET), $this->dropperName, "coinbomb_item"), new Vector3(lcg_value() * 0.2 - 0.1, 0.4, lcg_value() * 0.2 - 0.1));
        }
        $this->motion->y -= 0.025;
        $this->updateMovement();
        return parent::onUpdate($currentTick);
    }
}