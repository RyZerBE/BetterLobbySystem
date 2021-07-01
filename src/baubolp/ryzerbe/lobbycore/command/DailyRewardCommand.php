<?php


namespace baubolp\ryzerbe\lobbycore\command;


use baubolp\ryzerbe\lobbycore\form\DailyRewardForm;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class DailyRewardCommand extends Command
{

    public function __construct()
    {
        parent::__construct("dailyreward", "get your daily reward", "", []);
    }
    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player) return;

        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($sender);
        if($lobbyPlayer === null) return;

        $sender->sendForm(new DailyRewardForm($lobbyPlayer));
    }
}