<?php


namespace baubolp\ryzerbe\lobbycore\task;


use baubolp\ryzerbe\lobbycore\animation\Animation;
use baubolp\ryzerbe\lobbycore\animation\AnimationProvider;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use baubolp\ryzerbe\lobbycore\util\BlockQueue;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use function count;
use function is_null;

class AnimationTask extends Task
{

    /**
     * @inheritDoc
     */
    public function onRun(int $currentTick): void
    {
        foreach (array_values(AnimationProvider::$activeAnimation) as $animation) {
            if($animation instanceof Animation){
                $animation->tick();
            }
        }

        foreach(Server::getInstance()->getOnlinePlayers() as $player) {
            if($player->isRiding()) continue;
            $rbePlayer = LobbyPlayerCache::getLobbyPlayer($player);
            if(is_null($rbePlayer)) continue;
            foreach($rbePlayer->getActiveCosmetics() as $cosmetic){
                $cosmetic->onUpdate($player, $currentTick);
            }
        }

        BlockQueue::onUpdate($currentTick);
    }
}