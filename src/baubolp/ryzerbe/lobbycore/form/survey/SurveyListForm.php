<?php

namespace baubolp\ryzerbe\lobbycore\form\survey;

use baubolp\ryzerbe\lobbycore\provider\SurveyProvider;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class SurveyListForm {

    /**
     * @param Player $player
     */
    public static function open(Player $player): void {
        $form = new SimpleForm(function(Player $player, $data): void {
            if($data === null) return;

            $survey = SurveyProvider::getSurveys()[$data] ?? null;
            if($survey === null) return;
            SurveyResultForm::open($player, $survey);
        });
        $form->setTitle(TextFormat::AQUA.TextFormat::BOLD."Survey");
        foreach(SurveyProvider::getSurveys() as $survey) {
            $form->addButton($survey->getSurvey().TextFormat::EOL.$survey->getId(), 0, "", $survey->getId());
        }
        $form->sendToPlayer($player);
    }
}