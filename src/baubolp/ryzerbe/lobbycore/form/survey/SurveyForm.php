<?php

namespace baubolp\ryzerbe\lobbycore\form\survey;

use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use baubolp\ryzerbe\lobbycore\provider\SurveyProvider;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;

class SurveyForm {
    public static function open(Player $player){
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if(is_null($lobbyPlayer)) return;
        $form = new SimpleForm(function(Player $player, $data) use ($lobbyPlayer): void{
            if($data == null || $data === "nope") return;
            $i = explode(";", $data);
            $surveyId = $i[0];
            $answerId = $i[1];
            $survey = SurveyProvider::getSurveys()[$surveyId] ?? null;
            if($survey === null) return;
            $survey->vote($lobbyPlayer, $answerId);
        });
        $noVote = [];
        foreach(SurveyProvider::getSurveys() as $survey){
            if(!in_array($survey->getId(), $lobbyPlayer->getAlreadyVotedSurveys())){
                $noVote[] = $survey->getId();
            }
        }
        $form->setTitle(TextFormat::AQUA . TextFormat::BOLD . "Survey");
        if(count($noVote) <= 0){
            $form->setContent(LanguageProvider::getMessageContainer("lobby-survey-no-survey", $player->getName()));
            $form->addButton(TextFormat::RED . "Okay :(", -1, "", "nope");
            $form->sendToPlayer($player);
            return;
        }
        $survey = SurveyProvider::getSurveys()[$noVote[0]] ?? null;
        if($survey === null){
            $form->setContent(TextFormat::RED . "ERROR");
            $form->sendToPlayer($player);
            return;
        }
        $form->setContent($survey->getSurvey());
        foreach($survey->getSurveyAnswers() as $answer) $form->addButton($answer->getAnswerName(), -1, "", $survey->getId() . ";" . $answer->getIdentifier());
        $form->sendToPlayer($player);
    }
}