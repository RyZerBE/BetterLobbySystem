<?php


namespace baubolp\ryzerbe\lobbycore\command;


use baubolp\ryzerbe\lobbycore\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\util\async\AsyncExecutor;

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
        if ($sender instanceof Player)
            $senderName = $sender->getName();
        else
            $senderName = null;

        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli) {
            $mysqli->query("TRUNCATE `Lobby`.`News`");
        }, function (Server $server, $result) use ($senderName) {
            if ($senderName != null) {
                $player = $server->getPlayerExact($senderName);
                if (is_null($player)) return;
                $player->sendMessage(Loader::PREFIX . TextFormat::GREEN . "Die Datenbank für News wurde geleert. Es erhalten nun alle wieder ein NewsForm!");
            } else {
                $server->getLogger()->info(Loader::PREFIX . TextFormat::GREEN . "Die Datenbank für News wurde geleert. Es erhalten nun alle wieder ein NewsForm!");
            }
        });
    }
}