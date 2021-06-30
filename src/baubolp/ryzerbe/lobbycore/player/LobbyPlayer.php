<?php


namespace baubolp\ryzerbe\lobbycore\player;


use baubolp\core\provider\AsyncExecutor;
use baubolp\core\provider\CoinProvider;
use baubolp\core\provider\LanguageProvider;
use baubolp\core\util\LocationUtils;
use baubolp\ryzerbe\lobbycore\cosmetic\type\Cosmetic;
use baubolp\ryzerbe\lobbycore\Loader;
use mysqli;
use pocketmine\Player;
use pocketmine\Server;
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
    /** @var int */
    private $coinBombs = 0;
    /** @var array  */
    private $lottoWin = [];
    private $loginStreak;
    private $nextLoginStreak;
    private $lastLoginStreak;
    private $dailyCoinTime;
    private $dailyCoinBombTime;
    private $dailyLottoTicketTime;
    private $dailyHypeTrainTime;
    private $activeCosmetics = [];

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function load(): void
    {
        $this->getPlayer()->setAllowFlight(true);
        $playerName = $this->getPlayer()->getName();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (mysqli $mysqli) use ($playerName){
            $now = time();
            $nextDay = strtotime("next day");
            $playerData = [];

            ////// ACCOUNT CHECK \\\\\\
            $res = $mysqli->query("SELECT * FROM LottoTickets WHERE playername='$playerName'");
            if($res->num_rows <= 0) {
                $mysqli->query("INSERT INTO `LottoTickets`(`playername`, `tickets`) VALUES ('$playerName', '0')");
                $playerData["lottotickets"] = 0;
            }else {
                while($data = $res->fetch_assoc()) {
                    $playerData["lottotickets"] = $data["tickets"];
                }
            }

            $res = $mysqli->query("SELECT * FROM Coinbombs WHERE playername='$playerName'");
            if($res->num_rows <= 0) {
                $mysqli->query("INSERT INTO `Coinbombs`(`playername`, `bombs`) VALUES ('$playerName', '0')");
                $playerData["bombs"] = 0;
            }else {
                while($data = $res->fetch_assoc()) {
                    $playerData["bombs"] = $data["bombs"];
                }
            }

            $res = $mysqli->query("SELECT * FROM Position WHERE playername='$playerName'");
            if($res->num_rows <= 0) {
                $mysqli->query("INSERT INTO `Position`(`playername`, `position`) VALUES ('$playerName', '0')");
                $playerData["spawn"] = 0;
            }else {
                while($data = $res->fetch_assoc())
                    $playerData["spawn"] = $data["position"];
            }

            $res = $mysqli->query("SELECT * FROM DailyReward WHERE playername='$playerName'");
            if($res->num_rows <= 0) {
                $mysqli->query("INSERT INTO DailyReward(`playername`, `coins`, `lottoticket`, `coinbomb`, `hypetrain`) VALUES ('$playerName', '$now', '$now', '$now', '$now')");
                $playerData["coinTime"] = $now;
                $playerData["lottoTicketTime"] = $now;
                $playerData["coinBombTime"] = $now;
                $playerData["hypeTrainTime"] = $now;
            }else {
                while($data = $res->fetch_assoc()) {
                    $playerData["coinTime"] = $data["coins"];
                    $playerData["lottoTicketTime"] = $data["lottoticket"];
                    $playerData["coinBombTime"] = $data["coinbomb"];
                    $playerData["hypeTrainTime"] = $data["hypetrain"];
                }
            }

            $res = $mysqli->query("SELECT * FROM LoginStreak WHERE playername='$playerName'");
            if($res->num_rows <= 0) {
                $mysqli->query("INSERT INTO `LoginStreak`(`playername`, `loginstreak`, `nextstreakday`, `laststreakday`) VALUES ('$playerName', '1', '$nextDay', '$now')");
                $playerData["loginstreak"] = 1;
                $playerData["nextstreakday"] = $nextDay;
                $playerData["laststreakday"] = $now;
            }else {
                while($data = $res->fetch_assoc()) {
                    $playerData["loginstreak"] = $data["loginstreak"];
                    $playerData["nextstreakday"] = $data["nextstreakday"];
                    $playerData["laststreakday"] = $data["laststreakday"];
                }
            }

            $res = $mysqli->query("SELECT * FROM Status WHERE playername='$playerName'");
            if($res->num_rows <= 0) $mysqli->query("INSERT INTO `Status`(`playername`, `status`) VALUES ('$playerName', 'false')");

            return $playerData;
        }, function(Server $server, array $loadedData) use ($playerName){
            // LOAD DATA \\
            if(($lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($playerName)) != null) {
                $lobbyPlayer->setTickets($loadedData["lottotickets"]);
                $lobbyPlayer->setDailyCoinBombTime($loadedData["coinBombTime"]);
                $lobbyPlayer->setDailyCoinTime($loadedData["coinTime"]);
                $lobbyPlayer->setDailyHypeTrainTime($loadedData["hypeTrainTime"]);
                $lobbyPlayer->setDailyLottoTicketTime($loadedData["lottoTicketTime"]);
                $lobbyPlayer->setLoginStreak($loadedData["loginstreak"]);
                $lobbyPlayer->setLastLoginStreak($loadedData["laststreakday"]);
                $lobbyPlayer->setNextLoginStreak($loadedData["nextstreakday"]);
                $lobbyPlayer->setCoinBombs($loadedData["bombs"]);

                if($loadedData["spawn"] == 0)
                    $lobbyPlayer->getPlayer()->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn()->add(0, 1));
                else
                    $lobbyPlayer->getPlayer()->teleport(LocationUtils::fromString($loadedData["spawn"]));

                $lobbyPlayer->checkLoginStreak();
            }
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
        $playerName = $this->getPlayer()->getName();
        $position = LocationUtils::toString($this->getPlayer()->asLocation());
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (mysqli $mysqli) use ($playerName, $position) {
            $mysqli->query("UPDATE `Position` SET position='$position' WHERE playername='$playerName'");
        });
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

    /**
     * @param mixed $dailyCoinBombTime
     */
    public function setDailyCoinBombTime($dailyCoinBombTime): void
    {
        $this->dailyCoinBombTime = $dailyCoinBombTime;
        $playerName = $this->getPlayer()->getName();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (mysqli $mysqli) use ($dailyCoinBombTime, $playerName){
            $mysqli->query("UPDATE `DailyReward` SET coinbomb='$dailyCoinBombTime' WHERE playername='$playerName'");
        });
    }

    /**
     * @param mixed $dailyCoinTime
     */
    public function setDailyCoinTime($dailyCoinTime): void
    {
        $this->dailyCoinTime = $dailyCoinTime;

        $playerName = $this->getPlayer()->getName();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (mysqli $mysqli) use ($dailyCoinTime, $playerName){
            $mysqli->query("UPDATE `DailyReward` SET coins='$dailyCoinTime' WHERE playername='$playerName'");
        });
    }

    /**
     * @param mixed $dailyHypeTrainTime
     */
    public function setDailyHypeTrainTime($dailyHypeTrainTime): void
    {
        $this->dailyHypeTrainTime = $dailyHypeTrainTime;

        $playerName = $this->getPlayer()->getName();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (mysqli $mysqli) use ($dailyHypeTrainTime, $playerName){
            $mysqli->query("UPDATE `DailyReward` SET hypetrain='$dailyHypeTrainTime' WHERE playername='$playerName'");
        });
    }

    /**
     * @param mixed $dailyLottoTicketTime
     */
    public function setDailyLottoTicketTime($dailyLottoTicketTime): void
    {
        $this->dailyLottoTicketTime = $dailyLottoTicketTime;
        $playerName = $this->getPlayer()->getName();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (mysqli $mysqli) use ($dailyLottoTicketTime, $playerName){
            $mysqli->query("UPDATE `DailyReward` SET lottoticket='$dailyLottoTicketTime' WHERE playername='$playerName'");
        });
    }

    /**
     * @param mixed $lastLoginStreak
     */
    public function setLastLoginStreak($lastLoginStreak): void
    {
        $this->lastLoginStreak = $lastLoginStreak;
    }

    /**
     * @param mixed $nextLoginStreak
     */
    public function setNextLoginStreak($nextLoginStreak): void
    {
        $this->nextLoginStreak = $nextLoginStreak;
    }

    /**
     * @param mixed $loginStreak
     */
    public function setLoginStreak($loginStreak): void
    {
        $this->loginStreak = $loginStreak;
    }

    /**
     * @return mixed
     */
    public function getLoginStreak()
    {
        return $this->loginStreak;
    }

    /**
     * @return mixed
     */
    public function getLastLoginStreak()
    {
        return $this->lastLoginStreak;
    }

    /**
     * @return mixed
     */
    public function getNextLoginStreak()
    {
        return $this->nextLoginStreak;
    }

    /**
     * @return mixed
     */
    public function getDailyCoinBombTime()
    {
        return $this->dailyCoinBombTime;
    }

    /**
     * @return mixed
     */
    public function getDailyCoinTime()
    {
        return $this->dailyCoinTime;
    }

    /**
     * @return mixed
     */
    public function getDailyHypeTrainTime()
    {
        return $this->dailyHypeTrainTime;
    }

    /**
     * @return mixed
     */
    public function getDailyLottoTicketTime()
    {
        return $this->dailyLottoTicketTime;
    }

    public function checkLoginStreak(): void
    {
        $now = time();
        if (date("Y-m-d", $now) != date("Y-m-d", $this->getLastLoginStreak())) {
            if (date("Y-m-d", $this->getNextLoginStreak()) == date("Y-m-d", $now)) {
                $this->setLoginStreak($this->getLoginStreak() + 1);
                $this->setNextLoginStreak(strtotime("next day"));
                $this->setLastLoginStreak($now);

                if ($this->getLoginStreak() % 5 === 0 && $this->getLoginStreak() != 0) {
                    if ($this->getPlayer() != null) {
                        CoinProvider::addCoins($this->getPlayer()->getName(), 1000);
                        $this->getPlayer()->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer('lobby-loginstreak-get-coins', $this->getPlayer()->getName(), ['#coins' => 1000]));
                    }
                }

                $ls = $this->getLoginStreak();
                $next = $this->getNextLoginStreak();
                $last = $this->getLastLoginStreak();
                $playerName = $this->getPlayer()->getName();
                AsyncExecutor::submitMySQLAsyncTask("Lobby",function (mysqli $mysqli) use ($ls, $next, $last, $playerName) {
                    $mysqli->query("UPDATE LoginStreak SET loginstreak='$ls',nextstreakday='$next',laststreakday='$last' WHERE playername='$playerName'");
                });
            }else {
                $this->resetLoginStreak();
            }
        }
    }

    public function resetLoginStreak(): void
    {
        $this->setLastLoginStreak(time());
        $this->setNextLoginStreak(strtotime("next day"));
        $this->setLoginStreak(0);
        $playerName = $this->getPlayer()->getName();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (mysqli $mysqli) use ($playerName){
            $now = strtotime("next day");
            $now2 = time();

            $mysqli->query("UPDATE LoginStreak SET loginstreak='0',nextstreakday='$now',laststreakday='$now2' WHERE playername='$playerName'");
        }, function (Server $server, $result) use ($playerName){
            if(($player = $server->getPlayerExact($playerName)) != null) {
                $player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer('lobby-loginstreak-reset', $player->getName()));
            }
        });
    }

    /**
     * @return int
     */
    public function getCoinBombs(): int
    {
        return $this->coinBombs;
    }

    /**
     * @param int $coinBombs
     */
    public function setCoinBombs(int $coinBombs): void
    {
        $this->coinBombs = $coinBombs;
    }

    /**
     * @param int $count
     */
    public function addCoinbomb(int $count = 1)
    {
        $this->coinBombs += $count;
    }

    /**
     * @param int $count
     */
    public function removeCoinbomb(int $count = 1)
    {
        $this->coinBombs -= $count;
    }

    /**
     * @return Cosmetic[]
     */
    public function getActiveCosmetics(): array{
        return $this->activeCosmetics;
    }

    /**
     * @param Cosmetic $cosmetic
     * @return bool
     */
    public function isCosmeticActivated(Cosmetic $cosmetic): bool {
        return isset($this->activeCosmetics[$cosmetic->getIdentifier()]);
    }

    /**
     * @param Cosmetic $cosmetic
     */
    public function activateCosmetic(Cosmetic $cosmetic): void {
        $this->activeCosmetics[$cosmetic->getIdentifier()] = $cosmetic;
        $cosmetic->onActivate($this->getPlayer());
    }

    /**
     * @param Cosmetic $cosmetic
     */
    public function deactivateCosmetic(Cosmetic $cosmetic): void {
        if(!$this->isCosmeticActivated($cosmetic)) return;
        unset($this->activeCosmetics[$cosmetic->getIdentifier()]);
        $cosmetic->onDeactivate($this->getPlayer());
    }
}