<?php


namespace baubolp\ryzerbe\lobbycore\command;


use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class FlyCommand extends Command
{

    public function __construct()
    {
        parent::__construct("fly", "activate or deactivate your flymode", "", []);
        $this->setPermission("lobby.fly");
        $this->setPermissionMessage(Loader::PREFIX.TextFormat::RED."No Permissions!");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player) return;
        if(!$this->testPermission($sender)) return;

        $player = LobbyPlayerCache::getLobbyPlayer($sender);
        if($player === null) return;

        if($player->enabledFlyMode() || $player->getPlayer()->isFlying())
            $player->disableFlyMode();
        else
            $player->enableFlyMode();
    }
}