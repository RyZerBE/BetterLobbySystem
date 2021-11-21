<?php

namespace baubolp\ryzerbe\lobbycore\command;

use baubolp\ryzerbe\lobbycore\form\cosmetic\CosmeticsCategoriesForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class CosmeticCommand extends Command {
    /**
     * CosmeticCommand constructor.
     */
    public function __construct(){
        parent::__construct("cosmetic", "Cosmetic Command", null, ["cosmetics"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     * @return mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!$sender instanceof Player) return;
        CosmeticsCategoriesForm::open($sender);
    }
}