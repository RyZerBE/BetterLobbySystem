<?php

namespace baubolp\ryzerbe\lobbycore\command;

use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use mysqli;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\player\RyZerPlayerProvider;
use ryzerbe\core\util\async\AsyncExecutor;

class StatusCommand extends Command {
    public function __construct(){
        parent::__construct("setstatus", "set your personal status", "", ["ss"]);
        $this->setPermission("lobby.status");
        $this->setPermissionMessage(Loader::PREFIX . TextFormat::RED . "No Permissions!");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player) return;
        if(!$this->testPermission($sender)) return;
        if(empty($args[0])){
            $sender->sendMessage(Loader::PREFIX . TextFormat::RED . "/setstatus <Status>");
            $sender->sendMessage(Loader::PREFIX . TextFormat::RED . "/setstatus reset");
            return;
        }
        $playerName = $sender->getName();
        if($args[0] === "reset"){
            AsyncExecutor::submitMySQLAsyncTask("Lobby", function(mysqli $mysqli) use ($playerName){
                $mysqli->query("UPDATE `Status` SET status='false' WHERE playername='$playerName'");
            });
            $sender->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer('lobby-status-reset', $sender->getName()));
            RyzerPlayerProvider::getRyzerPlayer($sender)?->updateStatus(null);
            return;
        }
        $status = implode(" ", $args);
        if(strlen($status) > 30){
            $sender->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer('lobby-status-too-big', $sender->getName(), ['#max' => 30]));
            return;
        }
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function(mysqli $mysqli) use ($playerName, $status){
            $mysqli->query("UPDATE `Status` SET status='$status' WHERE playername='$playerName'");
        }, function(Server $server, $result) use ($playerName, $status){
            if(($sender = $server->getPlayerExact($playerName)) != null){
                $sender->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer('lobby-status-set', $sender->getName(), ['#status' => $status]));
            }
            LobbyPlayerCache::getLobbyPlayer($playerName)?->setStatus(($status === "reset") ? "" : $status);
            RyzerPlayerProvider::getRyzerPlayer($sender)?->updateStatus($status);
        });
    }
}