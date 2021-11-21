<?php

namespace baubolp\ryzerbe\lobbycore\util;

use DateTime;
use Exception;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;

class Event {
    /** @var string */
    private $eventName;
    /** @var string */
    private $description;
    /** @var string */
    private $group;
    /** @var DateTime */
    private $begin;
    /** @var DateTime */
    private $end;
    /** @var bool */
    private bool $displayedMessage = false;

    /**
     * Event constructor.
     *
     * @param string $eventName
     * @param string $description
     * @param string $group
     * @param DateTime $begin
     * @param DateTime $end
     */
    public function __construct(string $eventName, string $description, string $group, DateTime $begin, DateTime $end){
        $this->group = $group;
        $this->begin = $begin;
        $this->description = $description;
        $this->eventName = $eventName;
        $this->end = $end;
    }

    public function displayMessage(){
        if($this->displayedMessage) return;
        $this->displayedMessage = true;
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            $player->sendMessage(LanguageProvider::getMessageContainer("event-start-broadcast", $player->getName(), ["#name" => $this->getEventName()]));
            $player->playSound("random.explode", 5.0, 1.0, [$player]);
        }
    }

    /**
     * @return string
     */
    public function getEventName(): string{
        return $this->eventName;
    }

    /**
     * @return string
     */
    public function getDescription(): string{
        return str_replace("&", TextFormat::ESCAPE, $this->description);
    }

    /**
     * @return DateTime
     */
    public function getBegin(): DateTime{
        return $this->begin;
    }

    /**
     * @return DateTime
     */
    public function getEnd(): DateTime{
        return $this->end;
    }

    /**
     * @return string
     */
    public function getGroup(): string{
        return $this->group;
    }

    /**
     * @return string
     */
    public function toString(): string{
        return $this->eventName . ";" . $this->description . ";" . $this->group . ";" . $this->begin->format("Y-m-d H:i:s") . ";" . $this->end->format("Y-m-d H:i:s");
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isStarted(): bool{
        return $this->begin < new DateTime("now");
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isEnd(): bool{
        return $this->end < new DateTime("now");
    }
}