<?php

namespace baubolp\ryzerbe\lobbycore\task;

use baubolp\ryzerbe\lobbycore\animation\AnimationProvider;
use baubolp\ryzerbe\lobbycore\animation\type\PlayerAFKAnimation;
use baubolp\ryzerbe\lobbycore\entity\EventPortalEntity;
use baubolp\ryzerbe\lobbycore\entity\NPCEntity;
use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use baubolp\ryzerbe\lobbycore\provider\EventProvider;
use matze\gommejar\session\SessionManager;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use ryzerbe\core\player\RyZerPlayerProvider;

class LobbyTask extends Task {
    private const AFK_TIME = (20 * 60 * 5);// 5 Minutes

    /**
     * @inheritDoc
     */
    public function onRun(int $currentTick){
        foreach(LobbyPlayerCache::getPlayers() as $lobbyPlayer){
            $player = $lobbyPlayer->getPlayer();
            if(Loader::$jumpAndRunEnabled && SessionManager::getInstance()->getSession($player) !== null){
                if($player->getAllowFlight() && !$player->isCreative(true)){
                    $player->setAllowFlight(false);
                }
                continue;
            }
            if(!$lobbyPlayer->enabledFlyMode() && !$player->getAllowFlight()){
                if($lobbyPlayer->getPlayer()->isOnGround() && $lobbyPlayer->isDoublejumpEnabled()){
                    $lobbyPlayer->getPlayer()->setAllowFlight(true);
                }
            }
            else{
                if($player->isFlying() && !$lobbyPlayer->enabledFlyMode() && !$player->isCreative()){
                    $player->playSound('mob.enderdragon.growl', 5, 1.0, [$player]);
                    $player->knockBack($player, 0, $player->getDirectionVector()->getX(), $player->getDirectionVector()->getZ(), 1.8);
                    $player->setFlying(false);
                    $player->setAllowFlight(false);
                }
            }
            if($lobbyPlayer->enabledShield()){
                foreach($lobbyPlayer->getPlayer()->getLevel()->getNearbyEntities($lobbyPlayer->getPlayer()->getBoundingBox()->expandedCopy(5, 5, 5)) as $nearbyEntity){
                    if(!$nearbyEntity instanceof Player) continue;
                    if($nearbyEntity->distance($lobbyPlayer->getPlayer()) > 5) continue;
                    $nearbyRP = RyzerPlayerProvider::getRyzerPlayer($nearbyEntity->getName());
                    $playerRP = $lobbyPlayer->asRyZerPlayer();
                    if(is_null($playerRP) || is_null($nearbyRP)) continue;
                    if($nearbyRP->getRank()->getJoinPower() >= $playerRP->getRank()->getJoinPower() || $lobbyPlayer->isNearCloudSign()) continue;
                    $nearbyEntity->knockBack($player, 0, $nearbyEntity->getX() - $player->getX(), $nearbyEntity->getZ() - $player->getZ(), 1.5);
                }
            }
            if(!$lobbyPlayer->hasMoved()){
                $lobbyPlayer->addAfkTicks($this->getHandler()->getPeriod());
                if($lobbyPlayer->getAfkTicks() > self::AFK_TIME && $lobbyPlayer->isAFKAnimationEnabled()){
                    if(!$lobbyPlayer->isAfk()){
                        $lobbyPlayer->setAfk();
                        AnimationProvider::addActiveAnimation(new PlayerAFKAnimation($player));
                    }
                }
            }
            else{
                $lobbyPlayer->setAfk(false);
                $lobbyPlayer->resetAfkTicks();
            }
            $lobbyPlayer->setHasMoved(false);
            foreach(Server::getInstance()->getDefaultLevel()->getEntities() as $entity){
                if($entity instanceof EventPortalEntity && count(Server::getInstance()->getOnlinePlayers()) > 0){
                    $entity->updateTitle();
                }
            }
        }
        foreach(array_keys(Loader::$entityCheckQueue) as $npcEntityId){
            if(Loader::getInstance()->getServer()->getDefaultLevel()->getEntity($npcEntityId) != null) continue;
            $data = Loader::$entityCheckQueue[$npcEntityId];
            $npcEntity = new NPCEntity($data["location"], $data["skin"]);
            $npcEntity->setEmotes($data["emotes"]);
            $npcEntity->setAttackClosure($data["attackClosure"]);
            $npcEntity->setInteractClosure($data["interactClosure"]);
            $npcEntity->setScale($data["scale"]);
            $npcEntity->setNameTag($data["nameTag"]);
            $npcEntity->setLookAtPlayer($data["lookToPlayer"]);
            $npcEntity->setNameTagAlwaysVisible();
            $npcEntity->namedtag->setString("warpName", $data["warp"]);
            if($data["directConnect"] !== "N/A"){
                $npcEntity->namedtag->setString("directConnect", $data["directConnect"]);
            }
            $npcEntity->spawnToAll();
            unset(Loader::$entityCheckQueue[$npcEntityId]);
        }
        if(Loader::getInstance()->getServer()->getDefaultLevel()->getEntity(EventProvider::$id) == null) EventProvider::spawnPortal();
    }
}