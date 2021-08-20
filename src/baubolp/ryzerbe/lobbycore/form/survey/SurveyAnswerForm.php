<?php


namespace baubolp\ryzerbe\lobbycore\form\survey;


use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\util\survey\Survey;
use baubolp\ryzerbe\lobbycore\util\survey\SurveyAnswer;
use jojoe77777\FormAPI\CustomForm as CustomForm;
use pocketmine\form\CustomForm as PMCustomForm;
use pocketmine\form\CustomFormResponse;
use pocketmine\form\element\Input;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class SurveyAnswerForm extends PMCustomForm
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

    /**
     * @param Player $player
     * @param string $question
     * @param int $answerCount
     */
    public static function open(Player $player, string $question, int $answerCount): void {
        $form = new CustomForm(function(Player $player, $data) use ($answerCount, $question): void {
            if($data === null) return;
            $surveyAnswers = [];
            for($i = 0; $i < $answerCount; $i++) {
                $surveyAnswers[] = $data[("answer_" . $i)];
            }
            $survey = new Survey($question, $surveyAnswers);
            $survey->save();
            $player->sendMessage(Loader::PREFIX.TextFormat::GREEN."Deine Umfrage wurde erstellt.");
        });
        $form->setTitle(TextFormat::AQUA.TextFormat::BOLD."Survey");
        for($i = 0; $i < $answerCount; $i++) {
            $form->addInput("Antwortmöglichkeit $i", "", "", "answer_" . $i);
        }
        $form->sendToPlayer($player);
    }
}