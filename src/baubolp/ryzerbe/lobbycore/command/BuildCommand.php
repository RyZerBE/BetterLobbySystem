<?php

namespace baubolp\ryzerbe\lobbycore\command;

use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class BuildCommand extends Command {
    public function __construct(){
        parent::__construct("build", "enable/disable buildmode", "", []);
        $this->setPermission("lobby.build");
        $this->setPermissionMessage(Loader::PREFIX . TextFormat::RED . "No Permissions!");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player) return;
        if(!$this->testPermission($sender)) return;
        $player = LobbyPlayerCache::getLobbyPlayer($sender);
        if($player === null) return;
        if($player->enabledBuildMode()){
            $player->disableBuildMode();
        }
        else{
            $player->enableBuildMode();
        }
    }
}