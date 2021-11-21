<?php

namespace baubolp\ryzerbe\lobbycore\util\survey;

class SurveyAnswer {
    /** @var string */
    private $answerName;
    /** @var string */
    private $identifier;

    public function __construct(string $answerName){
        $this->answerName = $answerName;
        $this->identifier = "answer!" . str_replace(" ", "_", $answerName);
    }

    /**
     * @return string
     */
    public function getAnswerName(): string{
        return $this->answerName;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string{
        return $this->identifier;
    }
}