<?php


namespace baubolp\ryzerbe\lobbycore\command;


use baubolp\ryzerbe\lobbycore\form\event\EventForm;
use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\provider\EventProvider;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EventCommand extends Command
{

    public function __construct()
    {
        parent::__construct("event", "start/stop an event", "", []);
        $this->setPermission("lobby.event");
        $this->setPermissionMessage(Loader::PREFIX.TextFormat::RED."No Permissions");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player) return;
        if(!$this->testPermission($sender)) return;

        if(empty($args[0])) {
            $sender->sendMessage(Loader::PREFIX.TextFormat::RED."/event start");
            $sender->sendMessage(Loader::PREFIX.TextFormat::RED."/event reset");
            $sender->sendMessage(Loader::PREFIX.TextFormat::RED."/event reload");
            return;
        }

        $subCommand = strtolower($args[0]);

        switch ($subCommand) {
            case "start":
                if(EventProvider::$event != null) {
                    $sender->sendMessage(Loader::PREFIX.TextFormat::RED."Es läuft bereits ein Event!");
                    return;
                }
                $sender->sendForm(new EventForm());
                break;
            case "reset":
                if(EventProvider::$event === null) {
                    $sender->sendMessage(Loader::PREFIX.TextFormat::RED."Es läuft kein Event!");
                    return;
                }
                EventProvider::resetEvent();
                $sender->sendMessage(Loader::PREFIX.TextFormat::GREEN."Das Event wurde gestoppt.");
                break;
            case "reload":
                EventProvider::reload();
                $sender->sendMessage(Loader::PREFIX.TextFormat::GREEN."Event stuff wurde reloaded :)");
                break;
        }
    }
}