<?php


namespace baubolp\ryzerbe\lobbycore\animation\type;


use baubolp\ryzerbe\lobbycore\animation\Animation;
use baubolp\ryzerbe\lobbycore\entity\CoinBombMinecartEntity;
use baubolp\ryzerbe\lobbycore\util\BlockQueue;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\level\sound\PopSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use function mt_rand;

class CoinbombAnimation extends Animation
{

    /** @var array  */
    private $blocks = [];
    /** @var array  */
    private $blockPlaceQueue = [];
    /** @var Player  */
    private $player;
    /** @var Vector3 */
    private $center;

    /** @var int  */
    private $totalMinecarts = 20;
    /** @var int  */
    private $nextMinecartTick = 0;

    /**
     * CoinbombAnimation constructor.
     * @param Player $player
     */
    public function __construct(Player $player){
        $this->player = $player;
        $this->center = $player->asVector3();
        parent::__construct();
    }

    public function tick(): void {
        $player = $this->player;
        if(!$player->isConnected()) {
            $this->stop();
            return;
        }
        $level = $player->getLevel();
        switch($this->getCurrentTick()) {
            case 0: {
                $yDown = $player->getFloorY() - 1;
                $p_x = $player->getFloorX();
                $p_z = $player->getFloorZ();
                for($x = -4; $x <= 4; $x++) {
                    for($z = -4; $z <= 4; $z++) {
                        if(mt_rand(0, 100) > 50) continue;
                        $block = $level->getBlockAt($p_x + $x, $yDown, $p_z + $z);
                        if(!$block->isSolid() || $block->getId() === Block::GOLD_BLOCK) continue;
                        $this->blockPlaceQueue[$this->getCurrentTick() + mt_rand(5, 40)][] = $block;
                    }
                }
                break;
            }
        }

        if($this->getCurrentTick() > 40) {
            if($this->totalMinecarts > 0 && -$this->nextMinecartTick <= 0) {
                $this->nextMinecartTick = mt_rand(15, 60);

                $position = $this->center->asVector3()->add(mt_rand(-4, 4), mt_rand(3, 8), mt_rand(-4, 4));
                $nbt = Entity::createBaseNBT($position);
                $minecart = new CoinBombMinecartEntity($level, $nbt);
                $minecart->dropperName = $player->getName();
                $minecart->spawnToAll();

                $this->totalMinecarts--;
            }
        }

        if(isset($this->blockPlaceQueue[$this->getCurrentTick()])) {
            foreach($this->blockPlaceQueue[$this->getCurrentTick()] as $block) {
                BlockQueue::addBlock($block, mt_rand(5, 10) * 20);

                $level->setBlockIdAt($block->x, $block->y, $block->z, Block::GOLD_BLOCK);
                $level->setBlockDataAt($block->x, $block->y, $block->z, 0);

                $player->getLevel()->addSound(new PopSound($player), [$player]);
            }
        }

        if($this->totalMinecarts <= 0) $this->stop();
        parent::tick();
    }
}