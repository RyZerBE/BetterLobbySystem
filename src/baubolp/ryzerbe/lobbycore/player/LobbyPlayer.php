<?php


namespace baubolp\ryzerbe\lobbycore\player;


use baubolp\core\provider\AsyncExecutor;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class LobbyPlayer
{
    /** @var \pocketmine\Player */
    private $player;
    /** @var bool */
    private $build = false;
    /** @var bool */
    private $fly = false;
    /** @var int */
    private $tickets = 0;
    /** @var array  */
    private $lottoWin = [];

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function load(): void
    {
        $this->getPlayer()->setAllowFlight(true);
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli){
            //TODO: LOAD STUFF FROM MYSQL -> e.g LottoTickets ...
        });
    }

    /**
     * @return \pocketmine\Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function enableBuildMode()
    {
        $this->build = true;
        $this->getPlayer()->sendTitle(TextFormat::GREEN."✓ ".TextFormat::GRAY."BuildMode");
    }

    public function disableBuildMode()
    {
        $this->build = false;
        $this->getPlayer()->sendTitle(TextFormat::RED."✕ ".TextFormat::GRAY."BuildMode");
    }

    /**
     * @return bool
     */
    public function enabledBuildMode(): bool
    {
        return $this->build;
    }

    public function enableFlyMode(): void
    {
        $this->fly = true;
        $this->getPlayer()->sendTitle(TextFormat::GREEN."✓ ".TextFormat::GRAY."FlyMode");
    }

    public function disableFlyMode(): void
    {
        $this->fly = false;
        $this->getPlayer()->sendTitle(TextFormat::RED."✕ ".TextFormat::GRAY."FlyMode");
    }

    /**
     * @return bool
     */
    public function enabledFlyMode(): bool
    {
        return $this->fly;
    }

    public function register(): void
    {
        LobbyPlayerCache::registerLobbyPlayer($this);
    }

    public function unregister(): void
    {
        LobbyPlayerCache::unregisterLobbyPlayer($this->getPlayer()->getName());
    }

    /**
     * @return int
     */
    public function getTickets(): int
    {
        return $this->tickets;
    }

    /**
     * @param int $tickets
     */
    public function setTickets(int $tickets): void
    {
        $this->tickets = $tickets;
    }

    /**
     * @param int $count
     */
    public function addTicket(int $count = 1)
    {
        $this->tickets += $count;
    }
    /**
     * @param int $count
     */
    public function removeTicket(int $count = 1)
    {
        $this->tickets -= $count;
    }

    /**
     * @return array
     */
    public function getLottoWin(): array
    {
        return $this->lottoWin;
    }

    /**
     * @param int $win
     */
    public function addLottoWin(int $win)
    {
        $this->lottoWin[] = $win;
    }

    /**
     * @param array $lottoWin
     */
    public function setLottoWin(array $lottoWin): void
    {
        $this->lottoWin = $lottoWin;
    }
}