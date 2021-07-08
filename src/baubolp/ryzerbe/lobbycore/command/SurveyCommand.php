<?php


namespace baubolp\ryzerbe\lobbycore\command;


use baubolp\ryzerbe\lobbycore\form\survey\CreateSurveyForm;
use baubolp\ryzerbe\lobbycore\form\survey\SurveyDeleteForm;
use baubolp\ryzerbe\lobbycore\form\survey\SurveyForm;
use baubolp\ryzerbe\lobbycore\form\survey\SurveyResultForm;
use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\provider\SurveyProvider;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
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
            case "delete":
                SurveyDeleteForm::open($sender);
                break;
            case "result":
                if(empty($args[1])) {
                    $sender->sendMessage(Loader::PREFIX.TextFormat::RED."/survey result <answerId>");
                    return;
                }
                $id = $args[1];
                $survey = SurveyProvider::getSurveys()[$id] ?? null;
                if($survey === null) {
                    $sender->sendMessage(Loader::PREFIX.TextFormat::RED."Diese Umfrage ist nicht existent!");
                    return;
                }

                SurveyResultForm::open($sender, $survey);
                break;
            default:
                $sender->sendMessage(Loader::PREFIX.TextFormat::RED."/survey <create|delete|result>");
                break;
        }
    }
}