<?php


namespace baubolp\ryzerbe\lobbycore\form\survey;


use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\util\survey\Survey;
use baubolp\ryzerbe\lobbycore\util\survey\SurveyAnswer;
use pocketmine\form\CustomForm;
use pocketmine\form\CustomFormResponse;
use pocketmine\form\element\Input;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class SurveyAnswerForm extends CustomForm
{

    public function __construct(string $question, int $answerCount)
    {
        $elements = [];
        for($i = 0; $i < $answerCount; $i++)
            $elements[] = new Input("answer_$i", "Antwortmöglichkeit $i");

        parent::__construct(TextFormat::AQUA.TextFormat::BOLD."Survey", $elements, function (Player $player, CustomFormResponse $response) use ($answerCount, $question): void{

            $surveyAnswers = [];
            for($i = 0; $i < $answerCount; $i++)
                $surveyAnswers[] = new SurveyAnswer($response->getString($this->getElement($i)->getName()));

            $survey = new Survey($question, $surveyAnswers);
            $survey->save();
            $player->sendMessage(Loader::PREFIX.TextFormat::GREEN."Deine Umfrage wurde erstellt.");
        });
    }
}