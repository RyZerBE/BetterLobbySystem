<?php


namespace baubolp\ryzerbe\lobbycore\task;


use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\block\Block;
use pocketmine\scheduler\Task;

class LobbyTask extends Task
{

    /**
     * @inheritDoc
     */
    public function onRun(int $currentTick)
    {
        foreach (LobbyPlayerCache::getPlayers() as $lobbyPlayer) {
            $player = $lobbyPlayer->getPlayer();
            if (!$lobbyPlayer->enabledFlyMode() && !$player->getAllowFlight()) {
                if ($lobbyPlayer->getPlayer()->getServer()->getDefaultLevel()->getBlock($lobbyPlayer->getPlayer()->getSide(0))->getId() != Block::AIR)
                    $lobbyPlayer->getPlayer()->setAllowFlight(true);
            }else {
                if($player->isFlying() && !$lobbyPlayer->enabledFlyMode() && !$player->isCreative()) {
                    $player->playSound('mob.enderdragon.growl', 5, 1.0, [$player]);
                    $player->knockBack($player, 0, $player->getDirectionVector()->getX(), $player->getDirectionVector()->getZ(), 1.8); //2.4 current in the lobby but it is too much
                    $player->setFlying(false);
                    $player->setAllowFlight(false);
                }
            }
        }
    }
}