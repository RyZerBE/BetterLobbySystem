<?php


namespace baubolp\ryzerbe\lobbycore\command;


use baubolp\ryzerbe\lobbycore\form\LottoTicketForm;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\Player;

class LottoCommand extends Command
{

    public function __construct()
    {
        parent::__construct("lotto", "play lotto", "", []);
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if (($lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($sender)) != null)
                $sender->sendForm(new LottoTicketForm($lobbyPlayer));
        }
    }
}