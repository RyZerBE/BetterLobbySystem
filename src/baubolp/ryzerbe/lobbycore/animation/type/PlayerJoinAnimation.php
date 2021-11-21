<?php

namespace baubolp\ryzerbe\lobbycore\animation\type;

use baubolp\ryzerbe\lobbycore\animation\Animation;
use baubolp\ryzerbe\lobbycore\Loader;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class PlayerJoinAnimation extends Animation {
    /**
     * @var array
     */
    public $joinAnimation = [
        TextFormat::BLACK . '*      ' . TextFormat::RED . "*******      " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::DARK_RED . '*' . '   ' . TextFormat::RED . "*******      " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::DARK_RED . ' *' . '  ' . TextFormat::RED . "*******      " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::DARK_RED . '  *' . ' ' . TextFormat::RED . "*******      " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::DARK_RED . '   *' . TextFormat::RED . "*******      " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::DARK_RED . "*" . TextFormat::RED . "******      " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::WHITE . "R" . TextFormat::DARK_RED . "*" . TextFormat::RED . "*****      " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::WHITE . "Ry" . TextFormat::DARK_RED . "*" . TextFormat::RED . "****      " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::WHITE . "RyZ" . TextFormat::DARK_RED . "*" . TextFormat::RED . "***      " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::WHITE . "RyZe" . TextFormat::DARK_RED . "*" . TextFormat::RED . "**      " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::WHITE . "RyZer" . TextFormat::DARK_RED . "*" . TextFormat::RED . "*      " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::WHITE . "RyZerB" . TextFormat::DARK_RED . "*" . TextFormat::RED . "      " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::WHITE . "RyZerBE" . TextFormat::RED . "      " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::WHITE . "RyZerBE" . TextFormat::DARK_RED . '*' . "      " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::WHITE . "RyZerBE" . TextFormat::DARK_RED . ' *' . "     " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::WHITE . "RyZerBE" . TextFormat::DARK_RED . '  *' . "    " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::WHITE . "RyZerBE" . TextFormat::DARK_RED . '   *' . "   " . TextFormat::BLACK . "*",
        TextFormat::BLACK . '*      ' . TextFormat::WHITE . "RyZer" . TextFormat::RED . "BE" . TextFormat::RED . "      " . TextFormat::BLACK . "*",
    ];
    /** @var string */
    private $playerName;

    public function __construct(Player $player){
        $this->playerName = $player->getName();
        parent::__construct();
    }

    public function tick(): void{
        if(($player = Loader::getInstance()->getServer()->getPlayerExact($this->playerName)) != null){
            $title = $this->joinAnimation[$this->getCurrentTick()] ?? null;
            if($title === null){
                $this->stop();
                $player->playSound("mob.wither.death", 5.0, 1.0, [$player]);
                return;
            }
            $player->sendTitle($title, TextFormat::YELLOW . "Welcome", 0, 20, 0);
            $player->playSound("note.bass", 5.0, 2.0, [$player]);
        }
        else{
            $this->stop();
        }
        parent::tick();
    }
}