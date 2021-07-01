<?php


namespace baubolp\ryzerbe\lobbycore\animation\type;


use baubolp\ryzerbe\lobbycore\animation\Animation;
use baubolp\ryzerbe\lobbycore\form\NavigatorForm;
use baubolp\ryzerbe\lobbycore\util\Warp;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class NavigatorTeleportAnimation extends Animation
{
    /** @var Warp */
    private $warp;
    /** @var string */
    private $player;
    /** @var string */
    private $gameMode;
    /** @var int  */
    private $count = 0;
    /** @var string  */
    private $title = "";

    /**
     * NavigatorTeleportAnimation constructor.
     *
     * @param \pocketmine\Player $player
     * @param \baubolp\ryzerbe\lobbycore\util\Warp $warp
     * @param string $gameMode
     */
    public function __construct(Player $player, Warp $warp, string $gameMode)
    {
        $this->player = $player->getName();
        $this->warp = $warp;
        $this->gameMode = $gameMode;
        parent::__construct();
    }

    public function tick(): void
    {
        parent::tick();
        if($this->getCurrentTick() % 3 !== 0) return;

        $player = Server::getInstance()->getPlayerExact($this->player);
        if($player === null) {
            $this->stop();
            return;
        }

        if($this->count === 0) {
            $player->addEffect(new EffectInstance(Effect::getEffect(Effect::LEVITATION), 2000, 2, false));
            $player->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 2000, 2, false));
        }

        $_count = strlen($this->gameMode) - $this->count;
        $letters = $this->gameMode[$this->count] ?? "";
        $this->title .= $letters;

        $player->sendTitle($this->title.str_repeat("_", ($_count-1 < 0) ? 0 : $_count-1), TextFormat::WHITE."RyZer".TextFormat::RED."BE");
        $player->playSound('jump.slime', 5, 1.0, [$player]);
        if($_count === 0) {
            $player->removeAllEffects();
            $player->teleport($this->warp->getLocation());
            $player->playSound('firework.blast', 5, 1.0, [$player]);
            $player->sendTitle($this->gameMode, TextFormat::WHITE."RyZer".TextFormat::RED."BE");
            $this->stop();
            return;
        }

        $this->count++;
    }
}