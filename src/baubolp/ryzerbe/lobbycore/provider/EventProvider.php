<?php

namespace baubolp\ryzerbe\lobbycore\provider;

use baubolp\ryzerbe\lobbycore\entity\EventPortalEntity;
use baubolp\ryzerbe\lobbycore\util\Event;
use DateTime;
use Exception;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\Config;

class EventProvider {
    /** @var */
    public static $portalVector3;
    /** @var int */
    public static $id = -1;
    /** @var null|Event */
    public static $event = null;

    public function __construct(){
        self::$portalVector3 = new Vector3(228.5, 73, 262.5);
        self::spawnPortal();
    }

    public static function spawnPortal(): void{
        $vec3 = self::$portalVector3;
        Server::getInstance()->getDefaultLevel()->loadChunk($vec3->x >> 4, $vec3->z >> 4);
        $eventPortal = new EventPortalEntity(Server::getInstance()->getDefaultLevel(), Entity::createBaseNBT(self::$portalVector3));
        $eventPortal->setScale(3);
        $eventPortal->spawnToAll();
        self::$id = $eventPortal->getId();
    }

    public static function createEvent(string $eventName, string $description, string $group, DateTime $begin, DateTime $end){
        $ev = new Event($eventName, $description, $group, $begin, $end);
        $config = new Config("/root/RyzerCloud/data/Lobby/config.json");
        $config->set("event", $ev->toString());
        $config->save();
        self::$event = $ev;
    }

    public static function resetEvent(): void{
        self::$event = null;
        $config = new Config("/root/RyzerCloud/data/Lobby/config.json");
        $config->set("event", null);
        $config->save();
    }

    public static function reload(): void{
        $config = new Config("/root/RyzerCloud/data/Lobby/config.json");
        self::$event = self::readString($config->get("event"));
    }

    /**
     * @param $eventString
     * @return Event|null
     * @throws Exception
     */
    public static function readString($eventString): ?Event{
        if($eventString == null) return null;
        $i = explode(";", $eventString);
        return new Event($i[0], $i[1], $i[2], new DateTime($i[3]), new DateTime($i[4])) ?? null;
    }
}