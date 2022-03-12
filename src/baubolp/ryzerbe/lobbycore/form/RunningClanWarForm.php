<?php

namespace baubolp\ryzerbe\lobbycore\form;

use BauboLP\Cloud\CloudBridge;
use baubolp\ryzerbe\lobbycore\provider\RunningClanWarProvider;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class RunningClanWarForm {
    const BUTTON_TEXT = TextFormat::AQUA . "%clan1% " . TextFormat::BOLD . TextFormat::RED . "VS " . TextFormat::RESET . TextFormat::RED . "%clan2%\n" . TextFormat::YELLOW . "Map: " . TextFormat::RED . "%map%   " . TextFormat::YELLOW . "Elo: %elo%";

    public static function open(Player $player){
        $form = new SimpleForm(function(Player $player, $data){
            if($data === null) return;
            CloudBridge::getCloudProvider()->transferPlayer([$player->getName()], $data);
        });
        $form->setTitle(TextFormat::RED . TextFormat::BOLD . "Running Matches");
        foreach(RunningClanWarProvider::getRunningClanWars() as $clanWarMatch) $form->addButton(str_replace("%clan1%", $clanWarMatch->getClan1(), str_replace("%clan2%", $clanWarMatch->getClan2(), str_replace("%map%", $clanWarMatch->getMap(), str_replace("%elo%", ($clanWarMatch->eloEnabled() === true) ? TextFormat::BOLD . TextFormat::GREEN . "ON" : TextFormat::RED . TextFormat::BOLD . "OFF", self::BUTTON_TEXT)))), -1, "", $clanWarMatch->getServer());
        $form->sendToPlayer($player);
    }
}