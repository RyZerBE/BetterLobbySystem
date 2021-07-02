<?php


namespace baubolp\ryzerbe\lobbycore\command;


use baubolp\core\provider\AsyncExecutor;
use baubolp\ryzerbe\lobbycore\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class ResetNewsPopupCommand extends Command
{

    public function __construct()
    {
        parent::__construct("resetpopup", "", "", []);
        $this->setPermission("lobby.resetpopup");
        $this->setPermissionMessage(Loader::PREFIX . TextFormat::RED . "No Permissions!");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) return;

        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli) {
            $mysqli->query("TRUNCATE `Lobby`.`News`");
        }, function (Server $server, $result) use ($sender) {
            $sender->sendMessage(Loader::PREFIX . TextFormat::GREEN . "Die Datenbank für News wurde geleert. Es erhalten nun alle wieder ein NewsForm!");
        });
    }
}