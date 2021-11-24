<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use baubolp\ryzerbe\lobbycore\form\PlayerQuickOverviewForm;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEntityEvent;
use pocketmine\Player;
use ryzerbe\core\player\PMMPPlayer;

class PlayerInteractEntityListener implements Listener {

    public function interactEntity(PlayerInteractEntityEvent $event){
        $player = $event->getPlayer();
        $entity = $event->getEntity();
        if(!$player instanceof PMMPPlayer) return;
        if(!$entity instanceof PMMPPlayer) return;

        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        $lobbyEntity = LobbyPlayerCache::getLobbyPlayer($entity);
        if($lobbyPlayer === null || $lobbyEntity === null) return;

        if((!$lobbyPlayer->isQuickPlayerOverview()) || (!$lobbyEntity->isQuickPlayerOverview())) return;

        PlayerQuickOverviewForm::open($player, $entity);
    }
}