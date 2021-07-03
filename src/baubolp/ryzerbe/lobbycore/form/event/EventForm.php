<?php


namespace baubolp\ryzerbe\lobbycore\form\event;


use BauboLP\Cloud\CloudBridge;
use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\provider\EventProvider;
use pocketmine\form\CustomForm;
use pocketmine\form\CustomFormResponse;
use pocketmine\form\element\Dropdown;
use pocketmine\form\element\Input;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EventForm extends CustomForm
{

    public function __construct()
    {
        $elements = [
            new Input("eventName", TextFormat::RED . "Titel des Events", "z.B Big BW Round"),
            new Input("description", TextFormat::RED . "Beschreibe das Event", "Wir spielen eine große Runde Bedwars zusammen um 20:15Uhr", ""),
            new Dropdown("group", TextFormat::RED . "Auf welcher Cloudgruppe findet das Event statt?", CloudBridge::getCloudProvider()->getGroups()),
            new Input("start", TextFormat::RED . "Wann soll das Event starten?", "Y-m-d H:i:s"),
            new Input("end", TextFormat::RED . "Wann soll das Event enden?", "Y-m-d H:i:s")
        ];
        parent::__construct(TextFormat::RED . TextFormat::BOLD . "Event planning", $elements, function (Player $player, CustomFormResponse $response): void {
            $e1 = $this->getElement(0);
            $e2 = $this->getElement(1);
            $e3 = $this->getElement(2);
            $e4 = $this->getElement(3);
            $e5 = $this->getElement(4);

            $eventName = $response->getString($e1->getName());
            $group = CloudBridge::getCloudProvider()->getGroups()[$response->getInt($e3->getName())];
            $description = $response->getString($e2->getName());
            $start = $response->getString($e4->getName());
            $end = $response->getString($e5->getName());

            $startTme = strtotime($start);
            $endTme = strtotime($end);
            if($startTme === false || $endTme === false) {
                $player->sendMessage(Loader::PREFIX.TextFormat::RED."Bitte überprüfe deine Zeitangaben!");
                return;
            }

            EventProvider::createEvent($eventName, $description, $group, new \DateTime(date("Y-m-d H:i:s", $startTme)), new \DateTime(date("Y-m-d H:i:s", $endTme)));
            $player->sendMessage(Loader::PREFIX.TextFormat::GREEN."Dein Event wurde erstellt.");
        });
    }
}