<?php


namespace baubolp\ryzerbe\lobbycore\util;


class ClanWarMatch
{
    /** @var string  */
    private $clan1;
    /** @var string  */
    private $clan2;
    /** @var string  */
    private $map;
    /** @var string  */
    private $server;
    /** @var bool  */
    private $elo;

    /**
     * ClanWarMatch constructor.
     *
     * @param string $clan1
     * @param string $clan2
     * @param string $map
     * @param string $server
     * @param bool $elo
     */
    public function __construct(string $clan1, string $clan2, string $map, string $server, bool $elo)
    {
        $this->elo = $elo;
        $this->map = $map;
        $this->server = $server;
        $this->clan1 = $clan1;
        $this->clan2 = $clan2;
    }

    /**
     * @return string
     */
    public function getServer(): string
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getClan1(): string
    {
        return $this->clan1;
    }

    /**
     * @return string
     */
    public function getClan2(): string
    {
        return $this->clan2;
    }

    /**
     * @return string
     */
    public function getMap(): string
    {
        return $this->map;
    }

    /**
     * @return bool
     */
    public function eloEnabled(): bool
    {
        return $this->elo;
    }
}