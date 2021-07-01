<?php


namespace baubolp\ryzerbe\lobbycore\command;


use baubolp\ryzerbe\lobbycore\gui\PrivateServerGUI;
use baubolp\ryzerbe\lobbycore\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class PrivateServerCommand extends Command
{

    public function __construct()
    {
        parent::__construct("ps", "create a private server", "", []);
        $this->setPermissionMessage(Loader::PREFIX.TextFormat::RED."No Permissions!");
        $this->setPermission("lobby.ps");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) return;
        if (!$this->testPermission($sender)) return;

        (new PrivateServerGUI($sender))->send();
    }
}