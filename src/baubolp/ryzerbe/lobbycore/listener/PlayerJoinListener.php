<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;

class PlayerJoinListener implements Listener {
    public function onPlayerJoin(PlayerJoinEvent $event): void{
        $event->setJoinMessage("");
        $this->sendSnow($event->getPlayer());
    }

    public function sendSnow(Player $player): void{
        $pk = new LevelEventPacket();
        $pk->evid = 3001;
        $pk->data = 10000;
        $player->dataPacket($pk);
    }
}