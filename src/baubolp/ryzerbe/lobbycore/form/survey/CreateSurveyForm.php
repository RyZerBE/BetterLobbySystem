<?php


namespace baubolp\ryzerbe\lobbycore\form\survey;



use pocketmine\form\CustomForm;
use pocketmine\form\CustomFormResponse;
use pocketmine\form\element\Input;
use pocketmine\form\element\StepSlider;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CreateSurveyForm extends CustomForm
{

    public function __construct()
    {
        $elements = [
            new Input("question", TextFormat::RED."Frage", "Heißt es \"Die Nutella\" oder \"Das Nutella\"?"),
            new StepSlider("answercount", "Wie viele Antwortmöglichkeiten soll es geben?", ["1", "2", "3", "4"])
        ];
        parent::__construct(TextFormat::AQUA.TextFormat::BOLD."Survey", $elements, function (Player $player, CustomFormResponse $response): void{
            $e1 = $this->getElement(0);
            $e2 = $this->getElement(1);

            $question = $response->getString($e1->getName());
            $answerCount = $response->getInt($e2->getName()) + 1;

            $player->sendForm(new SurveyAnswerForm($question, $answerCount));
        });
    }
}