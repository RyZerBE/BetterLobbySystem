<?php

namespace baubolp\ryzerbe\lobbycore\listener;

use BauboLP\Cloud\Events\CloudPacketReceiveEvent;
use BauboLP\Cloud\Packets\NetworkInfoPacket;
use baubolp\ryzerbe\lobbycore\form\NavigatorForm;
use pocketmine\event\Listener;
use function json_decode;

class CloudPacketReceiveListener implements Listener {
    public function onCloudPacketReceive(CloudPacketReceiveEvent $event): void{
        $packet = $event->getCloudPacket();
        if($packet instanceof NetworkInfoPacket){
            $groupPlayerCount = json_decode($packet->getValue("group_player_count"), true);
            foreach(NavigatorForm::$games as $gameName => $gameData){
                NavigatorForm::$games[$gameName]["players"] = 0;
                foreach($gameData["groups"] as $groupName){
                    NavigatorForm::$games[$gameName]["players"] += $groupPlayerCount[$groupName] ?? 0;
                }
            }
        }
    }
}