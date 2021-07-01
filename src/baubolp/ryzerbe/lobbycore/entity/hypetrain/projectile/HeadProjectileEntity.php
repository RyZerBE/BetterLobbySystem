<?php

namespace baubolp\ryzerbe\lobbycore\entity\hypetrain\projectile;

use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\level\particle\HugeExplodeSeedParticle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Server;
use function array_rand;
use function mt_rand;

class HeadProjectileEntity extends Human {
    public const HEAD_GEOMETRY = '{"format_version": "1.12.0", "minecraft:geometry": [{"description": {"identifier": "geometry.player_head", "texture_width": 64, "texture_height": 64, "visible_bounds_width": 2, "visible_bounds_height": 4, "visible_bounds_offset": [0, 0, 0]}, "bones": [{"name": "Head", "pivot": [0, 0, 0], "cubes": [{"origin": [-4, 0, -4], "size": [8, 8, 8], "uv": [0, 0]}, {"origin": [-4, 0, -4], "size": [8, 8, 8], "inflate": 0.5, "uv": [32, 0]}]}]}]}';

    /** @var float */
    public $width = 0.4;
    /** @var float */
    public $height = 0.4;

    /**
     * HeadProjectileEntity constructor.
     * @param Level $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt){
        $onlinePlayers = Server::getInstance()->getOnlinePlayers();
        if(empty($onlinePlayers)) return;
        $this->skin = $onlinePlayers[array_rand($onlinePlayers)]->getSkin();
        parent::__construct($level, $nbt);
    }

    protected function initEntity(): void{
        $this->setMaxHealth(1);
        $this->setSkin($this->getSkin());
        parent::initEntity();
    }

    /**
     * @param Skin $skin
     */
    public function setSkin(Skin $skin): void{
        parent::setSkin(
            new Skin(
                $skin->getSkinId(),
                $skin->getSkinData(),
                "",
                "geometry.player_head",
                self::HEAD_GEOMETRY
            )
        );
    }

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool{
        if($this->isClosed() || $this->isFlaggedForDespawn()){
            return false;
        }
        if($this->isOnGround() || $this->y <= 0){
            $this->getLevel()->addParticle(new HugeExplodeSeedParticle($this));
            $this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_EXPLODE);

            $this->flagForDespawn();
        }
        $this->yaw += mt_rand(20, 30);
        $this->pitch += mt_rand(20, 30);

        if($this->yaw > 360) $this->yaw -= 360;
        if($this->pitch > 180) $this->yaw -= 360;

        $this->updateMovement();
        return parent::onUpdate($currentTick);
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void{}
}