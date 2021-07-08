<?php


namespace baubolp\ryzerbe\lobbycore\form\survey;


use baubolp\core\provider\AsyncExecutor;
use baubolp\ryzerbe\lobbycore\provider\SurveyProvider;
use baubolp\ryzerbe\lobbycore\util\survey\Survey;
use jojoe77777\FormAPI\SimpleForm;
use mysqli;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class SurveyResultForm
{

    public static function open(Player $player, Survey $survey)
    {
        $surveyId = $survey->getId();
        $playerName = $player->getName();
       AsyncExecutor::submitMySQLAsyncTask("Lobby", function(mysqli $mysqli) use ($surveyId): array{
           $res = $mysqli->query("SELECT * FROM Surveys WHERE surveyid='$surveyId'");
           if($res->num_rows <= 0) return [];

           $result = [];
           while ($data = $res->fetch_assoc())
               $result[] = $data["answerid"];

           return $result;
       }, function (Server $server, array $surveyResult) use ($playerName, $surveyId): void {
           $player = $server->getPlayer($playerName);
           if($player === null) return;

           $survey = SurveyProvider::getSurveys()[$surveyId] ?? null;
           if($survey === null) return;

           $barGraph = "";
           $form = new SimpleForm(function (Player $player, $data): void {});

           foreach ($survey->getSurveyAnswers() as $answer) {
               $votes = array_count_values($surveyResult)[$answer->getIdentifier()] ?? null;
               if($votes === null) {
                   $barGraph .= TextFormat::GREEN.$answer->getAnswerName()."\n".TextFormat::RED."NO VOTES!\n";
                   continue;
               }

               $votesAsPercent = (($votes * 100) / count($surveyResult)); //VOTES * 100 / ALL VOTES = P%
               $barGraph .= TextFormat::GREEN . $answer->getAnswerName() . "\n" . TextFormat::WHITE . str_repeat("▀", (int)(($votesAsPercent * 18) / 100)) . TextFormat::GREEN . " ".$votesAsPercent . "%\n";
           }

           $form->setContent($barGraph);
           $form->setTitle(TextFormat::AQUA . "Survey " . TextFormat::GREEN . "Result");
           $form->sendToPlayer($player);
       });
    }
}