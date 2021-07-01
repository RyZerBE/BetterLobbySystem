<?php


namespace baubolp\ryzerbe\lobbycore\command;


use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\provider\WarpProvider;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class WarpCommand extends Command
{

    public function __construct()
    {
        parent::__construct("warp", "create or delete a warp", "", []);
        $this->setPermission("lobby.warp");
        $this->setPermissionMessage(Loader::PREFIX.TextFormat::RED."No Permissions!");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) return;
        if (!$this->testPermission($sender)) return;

        if (empty($args[0])) {
            $sender->sendMessage(Loader::PREFIX . TextFormat::RED . "/warp <create|delete> <Name of Warp>");
            $sender->sendMessage(Loader::PREFIX . TextFormat::RED . "/warp list");
            return;
        }

        $subCommand = strtolower($args[0]);
        if ($subCommand === "list") {
            $sender->sendMessage(Loader::PREFIX . "Gespeicherte Warps:");
            foreach (WarpProvider::getWarps() as $warp)
                $sender->sendMessage($warp->toString());

            return;
        } else {
            if (empty($args[1])) {
                $sender->sendMessage(Loader::PREFIX . TextFormat::RED . "/warp <create|delete> <Name of Warp>");
                $sender->sendMessage(Loader::PREFIX . TextFormat::RED . "/warp list");
                return;
            }

            $warpName = strtolower($args[1]);
            if ($subCommand === "create") {
                $warp = WarpProvider::getWarps()[$warpName] ?? null;
                if ($warp !== null) {
                    $sender->sendMessage(Loader::PREFIX . TextFormat::RED . "Dieser Warp existiert bereits!");
                    return;
                }

                $success = WarpProvider::createWarp($warpName, $sender->getLocation());
                if ($success)
                    $sender->sendMessage(Loader::PREFIX . TextFormat::GREEN . "Warp wurde erstellt.");
                else
                    $sender->sendMessage(Loader::PREFIX . TextFormat::RED . "Warp konnte nicht erstellt werden ;/");
            } else if ($subCommand === "delete") {
                $warp = WarpProvider::getWarps()[$warpName] ?? null;
                if ($warp === null) {
                    $sender->sendMessage(Loader::PREFIX . TextFormat::RED . "Dieser Warp existiert nicht!");
                    return;
                }

                $warp->delete();
                WarpProvider::removeWarp($warpName);
                $sender->sendMessage(Loader::PREFIX . TextFormat::GREEN . "Warp wurde entfernt.");
            } else {
                $sender->sendMessage(Loader::PREFIX . TextFormat::RED . "/warp <create|delete> <Name of Warp>");
                $sender->sendMessage(Loader::PREFIX . TextFormat::RED . "/warp list");
            }
            return;
        }
    }
}