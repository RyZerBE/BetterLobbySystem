<?php


namespace baubolp\ryzerbe\lobbycore\task;


use baubolp\ryzerbe\lobbycore\animation\Animation;
use baubolp\ryzerbe\lobbycore\animation\AnimationProvider;
use pocketmine\scheduler\Task;

class AnimationTask extends Task
{

    /**
     * @inheritDoc
     */
    public function onRun(int $currentTick)
    {
        foreach (array_values(AnimationProvider::$activeAnimation) as $animation) {
            if($animation instanceof Animation)
                $animation->tick();
        }
    }
}