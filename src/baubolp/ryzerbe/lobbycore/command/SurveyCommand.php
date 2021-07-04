<?php


namespace baubolp\ryzerbe\lobbycore\command;


use baubolp\ryzerbe\lobbycore\form\survey\CreateSurveyForm;
use baubolp\ryzerbe\lobbycore\form\survey\SurveyDeleteForm;
use baubolp\ryzerbe\lobbycore\form\survey\SurveyForm;
use baubolp\ryzerbe\lobbycore\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class SurveyCommand extends Command
{

    public function __construct()
    {
        parent::__construct("survey", "get free coins for vote","", []);
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player) return;

        if(empty($args[0]) || !$sender->hasPermission("lobby.survey.manage")) {
            SurveyForm::open($sender);
            return;
        }

        $subCommand = strtolower($args[0]);

        switch ($subCommand) {
            case "create":
                $sender->sendForm(new CreateSurveyForm());
                break;
            case "stop":
                SurveyDeleteForm::open($sender);
                break;
            default:
                $sender->sendMessage(Loader::PREFIX.TextFormat::RED."/survey <create|delete>");
                break;
        }
    }
}