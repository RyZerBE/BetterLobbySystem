<?php

namespace baubolp\ryzerbe\lobbycore\entity;

use BauboLP\Cloud\CloudBridge;
use baubolp\ryzerbe\lobbycore\form\event\EventDescriptionForm;
use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\provider\EventProvider;
use baubolp\ryzerbe\lobbycore\util\SkinUtils;
use DateTime;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\ChunkLoader;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;

class EventPortalEntity extends Human implements ChunkLoader {
    /** @var array */
    private $alreadyCollided = [];

    public function __construct(Level $level, CompoundTag $nbt){
        $this->skin = new Skin("EndPortalEntity", SkinUtils::readImage("/root/RyzerCloud/data/NPC/portal.png"), "", "geometry.portal", file_get_contents("/root/RyzerCloud/data/NPC/portal.json"));
        parent::__construct($level, $nbt);
        $this->getLevel()->registerChunkLoader($this, $this->x >> 4, $this->z >> 4, true);
    }

    public function updateTitle(): void{
        $event = EventProvider::$event;
        if($event === null){
            $this->setNameTag(TextFormat::RED . "There are currently no event!");
            return;
        }
        if($event->isStarted()){
            $event->displayMessage();
            $diff = $event->getEnd()->diff(new DateTime("now"));
            $days = $diff->d;
            $hours = $diff->h;
            $minutes = $diff->i;
            $seconds = $diff->s;
            if($days > 0){
                $this->setNameTag($event->getEventName() . TextFormat::GRAY . "(" . TextFormat::GREEN . "Started" . TextFormat::GRAY . ")" . "\n" . TextFormat::YELLOW . "End in " . TextFormat::GREEN . $days . TextFormat::YELLOW . " Days, " . TextFormat::GREEN . $hours . TextFormat::YELLOW . " Hours and " . TextFormat::GREEN . $minutes . TextFormat::YELLOW . " Minutes");
            }
            else{
                if($hours > 0){
                    $this->setNameTag($event->getEventName() . TextFormat::GRAY . "(" . TextFormat::GREEN . "Started" . TextFormat::GRAY . ")" . "\n" . TextFormat::YELLOW . "End in " . TextFormat::GREEN . $hours . TextFormat::YELLOW . " Hours and " . TextFormat::GREEN . $minutes . TextFormat::YELLOW . " Minutes");
                }
                else{
                    if($minutes > 0){
                        $this->setNameTag($event->getEventName() . TextFormat::GRAY . "(" . TextFormat::GREEN . "Started" . TextFormat::GRAY . ")" . "\n" . TextFormat::YELLOW . "End in " . TextFormat::GREEN . $minutes . TextFormat::YELLOW . " Minutes");
                    }
                    else{
                        $this->setNameTag($event->getEventName() . TextFormat::GRAY . "(" . TextFormat::GREEN . "Started" . TextFormat::GRAY . ")" . "\n" . TextFormat::YELLOW . "End in " . TextFormat::GREEN . $seconds . TextFormat::YELLOW . " Seconds");
                    }
                }
            }
            if($event->isEnd()){
                EventProvider::resetEvent();
            }
            return;
        }
        $diff = $event->getBegin()->diff(new DateTime("now"));
        $days = $diff->d;
        $hours = $diff->h;
        $minutes = $diff->i;
        $seconds = $diff->s;
        if($days > 0){
            $this->setNameTag($event->getEventName() . "\n" . TextFormat::YELLOW . "Start in " . TextFormat::GREEN . $days . TextFormat::YELLOW . " Days, " . TextFormat::GREEN . $hours . TextFormat::YELLOW . " Hours and " . TextFormat::GREEN . $minutes . TextFormat::YELLOW . " Minutes");
        }
        else{
            if($hours > 0){
                $this->setNameTag($event->getEventName() . "\n" . TextFormat::YELLOW . "Start in " . TextFormat::GREEN . $hours . TextFormat::YELLOW . " Hours and " . TextFormat::GREEN . $minutes . TextFormat::YELLOW . " Minutes");
            }
            else{
                if($minutes > 0){
                    $this->setNameTag($event->getEventName() . "\n" . TextFormat::YELLOW . "Start in " . TextFormat::GREEN . $minutes . TextFormat::YELLOW . " Minutes");
                }
                else{
                    $this->setNameTag($event->getEventName() . "\n" . TextFormat::YELLOW . "Start in " . TextFormat::GREEN . $seconds . TextFormat::YELLOW . " Seconds");
                }
            }
        }
    }

    public function attack(EntityDamageEvent $source): void{
        if(!$source instanceof EntityDamageByEntityEvent) return;
        $player = $source->getDamager();
        if(!$player instanceof Player) return;
        $event = EventProvider::$event;
        if($event === null){
            $player->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer("lobby-no-event", $player->getName()));
            return;
        }
        EventDescriptionForm::open($player, $event);
    }

    public function onCollideWithPlayer(Player $player): void{
        if(empty($this->alreadyCollided[$player->getName()])){
            $this->alreadyCollided[$player->getName()] = time();
        }
        if($this->alreadyCollided[$player->getName()] >= time()) return;
        $this->alreadyCollided[$player->getName()] = time() + 2;
        $event = EventProvider::$event;
        if($event === null || $event->isEnd() || !$event->isStarted()){
            $xz = cos(deg2rad(0));
            $x = -$xz * sin(deg2rad(0));
            $z = $xz * cos(deg2rad(0));
            $direction = $player->temporalVector->setComponents($x, 0, $z)->normalize();
            $dx = $direction->getX();
            $dz = $direction->getZ();
            $player->knockBack($player, 0, $dx, $dz, 1.0);
        }
        if($event === null){
            $player->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer("lobby-no-event", $player->getName()));
            return;
        }
        if(!$event->isStarted()){
            $player->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer("lobby-event-arent-started", $player->getName()));
            return;
        }
        if($event->isEnd()){
            $player->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer("lobby-event-over", $player->getName()));
            return;
        }
        $server = CloudBridge::getCloudProvider()->getRunningServersByGroup($event->getGroup())[0] ?? null;
        if(CloudBridge::getCloudProvider()->isServerBlacklisted($server)){ #already ingame
            $player->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer("lobby-wait-next-round", $player->getName()));
            return;
        }
        if($server === null){
            $player->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer("lobby-no-free-server", $player->getName()));
            return;
        }
        $player->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn()->add(1));
        CloudBridge::getCloudProvider()->transferPlayer([$player->getName()], $server);
    }

    /**
     * @inheritDoc
     */
    public function getLoaderId(): int{
        return spl_object_id($this);
    }

    /**
     * @inheritDoc
     */
    public function isLoaderActive(): bool{
        return !$this->isClosed();
    }

    /**
     * @inheritDoc
     */
    public function onChunkChanged(Chunk $chunk){
    }

    /**
     * @inheritDoc
     */
    public function onChunkLoaded(Chunk $chunk){
    }

    /**
     * @inheritDoc
     */
    public function onChunkUnloaded(Chunk $chunk){
    }

    /**
     * @inheritDoc
     */
    public function onChunkPopulated(Chunk $chunk){
    }

    /**
     * @inheritDoc
     */
    public function onBlockChanged(Vector3 $block){
    }

    protected function initEntity(): void{
        parent::initEntity(); // TODO: Change the autogenerated stub
        $this->sendSkin();
    }
}