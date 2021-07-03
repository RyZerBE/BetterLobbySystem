<?php


namespace baubolp\ryzerbe\lobbycore\form\event;


use baubolp\ryzerbe\lobbycore\util\Event;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EventDescriptionForm
{
    public static function open(Player $player, Event $event)
    {
        $form = new SimpleForm(function (Player $player, $data):void{});
        $form->setContent($event->getDescription());
        $form->setTitle(TextFormat::YELLOW.$event->getEventName());
        $form->sendToPlayer($player);
    }
}