<?php


namespace baubolp\ryzerbe\lobbycore\form\survey;


use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\provider\SurveyProvider;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class SurveyDeleteForm
{
    /**
     * @param \pocketmine\Player $player
     */
    public static function open(Player $player)
    {
        $form = new SimpleForm(function (Player $player, $data): void {
            if ($data === null) return;

            $survey = SurveyProvider::getSurveys()[$data];
            if ($survey === null) return;

            $survey->delete();
            $player->sendMessage(Loader::PREFIX . TextFormat::RED . "Die Umfrage wurde gelöscht.");
        });

        $form->setTitle(TextFormat::AQUA . TextFormat::BOLD . "Survey");
        foreach (SurveyProvider::getSurveys() as $survey)
            $form->addButton($survey->getSurvey(), -1, "", $survey->getId());

        $form->sendToPlayer($player);
    }
}