<?php

namespace baubolp\ryzerbe\lobbycore\command;

use baubolp\ryzerbe\lobbycore\entity\NPCEntity;
use baubolp\ryzerbe\lobbycore\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class RotateNPCCommand extends Command {
    /**
     * RotateNPCCommand constructor.
     */
    public function __construct(){
        parent::__construct("rotatenpc", "Rotate NPC Command");
        $this->setPermission("lobby.position");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     * @return mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!$sender instanceof Player || !$this->testPermission($sender)) return;
        if(!isset($args[0])){
            $sender->sendMessage(Loader::PREFIX . TextFormat::RED . "/rotatenpc [Yaw] [Pitch = 0]");
            return;
        }
        $entity = $sender->getLevel()->getNearestEntity($sender, 5, NPCEntity::class);
        if($entity === null){
            $sender->sendMessage(Loader::PREFIX . TextFormat::RED . "Es konnte kein NPC gefunden werden.");
            return;
        }
        $yaw = (float)$args[0];
        $pitch = (float)($args[1] ?? $entity->getPitch());
        $entity->teleport($entity, $yaw, $pitch);
    }
}