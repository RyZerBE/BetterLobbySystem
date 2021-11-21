<?php

namespace baubolp\ryzerbe\lobbycore\provider;

use baubolp\ryzerbe\lobbycore\util\survey\Survey;
use baubolp\ryzerbe\lobbycore\util\survey\SurveyAnswer;
use pocketmine\utils\Config;

class SurveyProvider {
    /** @var Survey[] */
    public static $surveys = [];

    public static function loadSurvey(): void{
        $config = new Config("/root/RyzerCloud/data/Lobby/config.json");
        foreach($config->get("surveys") as $surveyString){
            $survey = SurveyProvider::readSurvey($surveyString);
            SurveyProvider::addSurvey($survey);
        }
    }

    /**
     * @param string $surveyString
     * @return Survey
     */
    public static function readSurvey(string $surveyString){
        $surveyData = explode(";", $surveyString);
        $answers = explode(":", $surveyData[2]);
        $surveyAnswers = [];
        foreach($answers as $answer) $surveyAnswers[] = new SurveyAnswer(str_replace("_", " ", $answer));
        return new Survey($surveyData[1], $surveyAnswers, $surveyData[0]);
    }

    /**
     * @param Survey $survey
     */
    public static function addSurvey(Survey $survey){
        self::$surveys[$survey->getId()] = $survey;
    }

    /**
     * @param Survey $survey
     */
    public static function removeSurvey(Survey $survey){
        unset(self::$surveys[$survey->getId()]);
    }

    /**
     * @return Survey[]
     */
    public static function getSurveys(): array{
        return self::$surveys;
    }
}