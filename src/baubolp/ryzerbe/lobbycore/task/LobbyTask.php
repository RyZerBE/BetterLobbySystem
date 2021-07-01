<?php


namespace baubolp\ryzerbe\lobbycore\task;

use baubolp\core\player\RyzerPlayerProvider;
use baubolp\core\provider\RankProvider;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\Player;
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
                if ($lobbyPlayer->getPlayer()->isOnGround()){
                    $lobbyPlayer->getPlayer()->setAllowFlight(true);
                }
            } else {
                if ($player->isFlying() && !$lobbyPlayer->enabledFlyMode() && !$player->isCreative()) {
                    $player->playSound('mob.enderdragon.growl', 5, 1.0, [$player]);
                    $player->knockBack($player, 0, $player->getDirectionVector()->getX(), $player->getDirectionVector()->getZ(), 1.8);
                    $player->setFlying(false);
                    $player->setAllowFlight(false);
                }
            }

            if ($lobbyPlayer->enabledShield()) {
                foreach ($lobbyPlayer->getPlayer()->getLevel()->getNearbyEntities($lobbyPlayer->getPlayer()->getBoundingBox()->expandedCopy(5, 5, 5)) as $nearbyEntity) {
                    if (!$nearbyEntity instanceof Player) continue;
                    if ($nearbyEntity->distance($lobbyPlayer->getPlayer()) > 5) continue;

                    $nearbyRP = RyzerPlayerProvider::getRyzerPlayer($nearbyEntity->getName());
                    $playerRP = $lobbyPlayer->asRyZerPlayer();
                    if (is_null($playerRP) || is_null($nearbyRP)) continue;

                    if (RankProvider::getRankJoinPower($nearbyRP->getRank()) > RankProvider::getRankJoinPower($playerRP->getRank())) continue;
                    $nearbyEntity->knockBack($player, 0, $nearbyEntity->getX() - $player->getX(), $nearbyEntity->getZ() - $player->getZ(), 1.5);
                }
            }
        }
    }
}