<?php

namespace baubolp\ryzerbe\lobbycore\command;

use baubolp\ryzerbe\lobbycore\cosmetic\type\vehicle\hypetrain\HypeTrain;
use baubolp\ryzerbe\lobbycore\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class HypeTrainCommand extends Command {

    /**
     * HypeTrainCommand constructor.
     */
    public function __construct(){
        parent::__construct("hypetrain", "HypeTrain Command");
        $this->setPermission("lobby.hypetrain");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     * @return mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!$sender instanceof Player) return;
        //if(!$this->testPermission($sender)) return;//todo: add

        if($sender->isRiding()) {
            $sender->sendMessage(Loader::PREFIX . "You have to leave your current train to spawn a new one.");
            return;
        }
        HypeTrain::spawn($sender);
    }
}