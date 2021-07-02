<?php


namespace baubolp\ryzerbe\lobbycore\util;


use DateTime;

class Event
{
    /** @var string */
    private $eventName;
    /** @var string  */
    private $description;
    /** @var string  */
    private $group;
    /** @var \DateTime  */
    private $begin;
    /** @var \DateTime  */
    private $end;

    /**
     * Event constructor.
     *
     * @param string $eventName
     * @param string $description
     * @param string $group
     * @param \DateTime $begin
     * @param \DateTime $end
     */
    public function __construct(string $eventName, string $description, string $group, DateTime $begin, DateTime $end)
    {
        $this->group = $group;
        $this->begin = $begin;
        $this->description = $description;
        $this->eventName = $eventName;
        $this->end = $end;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return \DateTime
     */
    public function getBegin(): DateTime
    {
        return $this->begin;
    }

    /**
     * @return \DateTime
     */
    public function getEnd(): DateTime
    {
        return $this->end;
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->eventName.";".$this->description.";".$this->group.";".$this->begin->format("Y-m-d H:i:s").";".$this->end->format("Y-m-d H:i:s");
    }
}