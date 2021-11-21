<?php

namespace baubolp\ryzerbe\lobbycore\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use function implode;
use function round;
use function str_repeat;
use function var_dump;

class PositionCommand extends Command {
    /**
     * PositionCommand constructor.
     */
    public function __construct(){
        parent::__construct("position", "Position Command");
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
        $sender->sendMessage(str_repeat("-", 20));
        $sender->sendMessage("X:" . round($sender->x, 1));
        $sender->sendMessage("Y:" . round($sender->y, 1));
        $sender->sendMessage("Z:" . round($sender->z, 1));
        $sender->sendMessage("Yaw:" . round($sender->yaw, 1));
        $sender->sendMessage("Pitch:" . round($sender->pitch, 1));
        $sender->sendMessage(str_repeat("-", 20));
        var_dump(implode(", ", [
            round($sender->x, 1),
            round($sender->y, 1),
            round($sender->z, 1),
            round($sender->yaw, 1),
            round($sender->pitch, 1),
        ]));
    }
}