<?php

namespace baubolp\ryzerbe\lobbycore\task;

use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Packets\NetworkInfoPacket;
use baubolp\ryzerbe\lobbycore\animation\Animation;
use baubolp\ryzerbe\lobbycore\animation\AnimationProvider;
use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use baubolp\ryzerbe\lobbycore\util\BlockQueue;
use matze\gommejar\session\SessionManager;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use function is_null;

class AnimationTask extends Task {

    /** @var string[]  */
    private array $christmas_sounds = [
        "block.composter.fill_success" => 3.0,
        "block.composter.fill" => 3.5
    ]; #Texturepack
    /**
     * @inheritDoc
     */
    public function onRun(int $currentTick): void{
        if($currentTick % (20 * 20) === 0){
            $pk = new NetworkInfoPacket();
            CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
        }

        /*
        if($currentTick % (20 * 45) === 0) {
            $sound = array_keys($this->christmas_sounds)[array_rand(array_keys($this->christmas_sounds))];
            foreach(LobbyPlayerCache::getPlayers() as $lobbyPlayer) {
                $lobbyPlayer->getPlayer()->playSound($sound, $this->christmas_sounds[$sound], 1.0, [$lobbyPlayer->getPlayer()]);
                #$lobbyPlayer->getPlayer()->sendMessage($sound);
            }
        }
        */

        foreach(array_values(AnimationProvider::$activeAnimation) as $animation){
            if($animation instanceof Animation){
                $animation->tick();
            }
        }
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            if($player->isRiding() || (Loader::$jumpAndRunEnabled && SessionManager::getInstance()->getSession($player) !== null)) continue;
            $rbePlayer = LobbyPlayerCache::getLobbyPlayer($player);
            if(is_null($rbePlayer) || $rbePlayer->isAfk() || $rbePlayer->isNearCloudSign()) continue;
            foreach($rbePlayer->getActiveCosmetics() as $cosmetic){
                $cosmetic->onUpdate($player, $currentTick);
            }
        }
        BlockQueue::onUpdate($currentTick);
    }
}