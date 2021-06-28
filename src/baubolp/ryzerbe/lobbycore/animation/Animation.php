<?php


namespace baubolp\ryzerbe\lobbycore\animation;


use function uniqid;

abstract class Animation
{
    /** @var int  */
    private $ticks = 0;

    /** @var string */
    private $id;

    public function __construct()
    {
        $this->id = uniqid();
    }

    public function tick(): void {
        $this->ticks++;
    }

    /**
     * @return int
     */
    public function getCurrentTick(): int
    {
        return $this->ticks;
    }

    /**
     * @return string
     */
    public function getAnimationId(): string
    {
        return $this->id;
    }


    public function stop(): void
    {
        unset(AnimationProvider::$activeAnimation[$this->getAnimationId()]);
    }

    public function cancel(): void
    {
        unset(AnimationProvider::$activeAnimation[$this->getAnimationId()]);
    }
}