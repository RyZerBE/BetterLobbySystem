<?php


namespace baubolp\ryzerbe\lobbycore\command;


use baubolp\ryzerbe\lobbycore\form\RunningClanWarForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class RunningClanWarsCommand extends Command
{

    public function __construct()
    {
        parent::__construct("runningclanwars", "See all ClanWar matches", "", ["runningcw"]);
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player) return;

        RunningClanWarForm::open($sender);
    }
}