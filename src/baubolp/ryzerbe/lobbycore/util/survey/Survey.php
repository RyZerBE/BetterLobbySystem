<?php


namespace baubolp\ryzerbe\lobbycore\util\survey;


use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayer;
use baubolp\ryzerbe\lobbycore\provider\SurveyProvider;
use pocketmine\utils\Config;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\provider\CoinProvider;
use ryzerbe\core\util\async\AsyncExecutor;

class Survey
{
    /** @var \baubolp\ryzerbe\lobbycore\util\survey\SurveyAnswer[] */
    private $surveyAnswers;
    /** @var string */
    private $survey;
    /** @var string */
    private $id;

    /**
     * Survey constructor.
     *
     * @param string $survey
     * @param array $surveyAnswers
     * @param string|null $id
     */
    public function __construct(string $survey, array $surveyAnswers, string $id = null)
    {
        $this->surveyAnswers = $surveyAnswers;
        $this->survey = $survey;
        if($id === null) $this->id = uniqid(); else $this->id = $id;
    }

    /**
     * @return string
     */
    public function getSurvey(): string
    {
        return $this->survey;
    }

    /**
     * @return \baubolp\ryzerbe\lobbycore\util\survey\SurveyAnswer[]
     */
    public function getSurveyAnswers(): array
    {
        return $this->surveyAnswers;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        $answers = [];
        foreach ($this->getSurveyAnswers() as $answer) $answers[] = $answer->getAnswerName();

        return $this->id.";".$this->survey.";".implode(":", $answers);
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (!is_file("/root/RyzerCloud/data/Lobby/config.json")) return false;

        $config = new Config("/root/RyzerCloud/data/Lobby/config.json");
        $surveys = $config->get("surveys");
        $surveys[] = $this->toString();
        $config->set("surveys", $surveys);
        SurveyProvider::addSurvey($this);
        return $config->save();
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        if (!is_file("/root/RyzerCloud/data/Lobby/config.json")) return false;

        $config = new Config("/root/RyzerCloud/data/Lobby/config.json");
        $surveys = $config->get("surveys");
        unset($surveys[array_search($this->toString(), $surveys)]);
        $config->set("surveys", $surveys);
        SurveyProvider::removeSurvey($this);
        return $config->save();
    }

    /**
     * @param \baubolp\ryzerbe\lobbycore\player\LobbyPlayer $player
     * @param string $answerId
     */
    public function vote(LobbyPlayer $player, string $answerId)
    {
        $playerName = $player->getPlayer()->getName();
        $surveyId = $this->getId();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli) use ($playerName, $surveyId, $answerId){
            $mysqli->query("INSERT INTO `Surveys`(`playername`, `surveyid`, `answerid`) VALUES ('$playerName', '$surveyId', '$answerId')");
        });

        $av = $player->getAlreadyVotedSurveys();
        $av[] = $surveyId;
        $player->setAlreadyVotedSurveys($av);
        CoinProvider::addCoins($playerName, 50);
        $player->getPlayer()->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("lobby-survey-voted", $playerName));
    }
}