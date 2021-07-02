<?php


namespace baubolp\ryzerbe\lobbycore\player;


use baubolp\core\player\RyzerPlayer;
use baubolp\core\player\RyzerPlayerProvider;
use baubolp\core\provider\AsyncExecutor;
use baubolp\core\provider\CoinProvider;
use baubolp\core\provider\LanguageProvider;
use baubolp\core\provider\RankProvider;
use baubolp\core\util\LocationUtils;
use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\Cosmetic;
use baubolp\ryzerbe\lobbycore\cosmetic\type\vehicle\hypetrain\HypeTrain;
use baubolp\ryzerbe\lobbycore\form\NewsBookForm;
use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\provider\ItemProvider;
use baubolp\ryzerbe\lobbycore\util\ScoreboardUtils;
use mysqli;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use function is_null;
use function time;

class LobbyPlayer
{
    /** @var Player */
    private $player;
    /** @var bool */
    private $build = false;
    /** @var bool */
    private $fly = false;
    /** @var int */
    private $tickets = 0;
    /** @var int */
    private $coinBombs = 0;
    /** @var int */
    private $hypeTrains = 0;
    /** @var array  */
    private $lottoWin = [];
    /** @var bool  */
    private $hasMoved = false;
    /** @var int  */
    private $afkTicks = 0;
    /** @var bool  */
    private $isAfk = false;

    private $activeCosmetics = [];
    private $loginStreak;
    private $nextLoginStreak;
    private $lastLoginStreak;
    private $dailyCoinTime;
    private $dailyCoinBombTime;
    private $dailyLottoTicketTime;
    private $dailyHypeTrainTime;
    private $cosmetics;
    /** @var bool */
    private $shield = false;

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

            $res = $mysqli->query("SELECT * FROM Hypetrains WHERE playername='$playerName'");
            if($res->num_rows <= 0) {
                $mysqli->query("INSERT INTO `Hypetrains`(`playername`) VALUES ('$playerName')");
                $playerData["hypetrains"] = 0;
            }else {
                while($data = $res->fetch_assoc()) {
                    $playerData["hypetrains"] = $data["hypetrains"];
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

            $res = $mysqli->query("SELECT * FROM Cosmetics WHERE playername='$playerName'");
            $playerData["cosmetics"] = [];//Prevent empty value. Could be replaced with '$loadedData["cosmetics"] ?? []' - Buuuuuttt no
            while($data = $res->fetch_assoc()) {
                $playerData["cosmetics"][] = [
                    "Active" => (bool)$data["active"],
                    "Cosmetic" => $data["cosmetic"]
                ];
            }

            $res = $mysqli->query("SELECT * FROM Status WHERE playername='$playerName'");
            if($res->num_rows <= 0) $mysqli->query("INSERT INTO `Status`(`playername`, `status`) VALUES ('$playerName', 'false')");

            $res = $mysqli->query("SELECT * FROM `News` WHERE playername='$playerName'");
            if($res->num_rows > 0)
                $playerData["news"] = false;
            else
                $playerData["news"] = true;

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
                $lobbyPlayer->setHypeTrains($loadedData["hypetrains"]);

                ItemProvider::giveLobbyItems($lobbyPlayer->getPlayer());

                $cosmetics = [];
                $activeCosmetics = [];
                foreach($loadedData["cosmetics"] as $cosmeticData) {
                    $cosmetic = CosmeticManager::getInstance()->getCosmetic($cosmeticData["Cosmetic"]);
                    if(is_null($cosmetic)) continue;
                    $cosmetics[$cosmetic->getIdentifier()] = $cosmetic;
                    if($cosmeticData["Active"]) $activeCosmetics[$cosmetic->getIdentifier()] = $cosmetic;
                }
                $lobbyPlayer->setActiveCosmetics($activeCosmetics);
                $lobbyPlayer->setCosmetics($cosmetics);

                if($loadedData["spawn"] == 0)
                    $lobbyPlayer->getPlayer()->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn()->add(0, 1));
                else
                    $lobbyPlayer->getPlayer()->teleport(LocationUtils::fromString($loadedData["spawn"]));

                $lobbyPlayer->checkLoginStreak();
                if((bool)$loadedData["news"] && NewsBookForm::$news != null)
                    NewsBookForm::open($lobbyPlayer->getPlayer());

                $lobbyPlayer->updateScoreboard();
            }
        });
    }

    /**
     * @return Player
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

        $playerName = $this->getPlayer()->getName();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function(mysqli $mysqli) use ($coinBombs, $playerName): void {
            $mysqli->query("UPDATE Coinbombs SET bombs='$coinBombs' WHERE playername='$playerName'");
        });
    }

    /**
     * @param int $count
     */
    public function addCoinbomb(int $count = 1)
    {
        $this->setCoinBombs($this->coinBombs + $count);
    }

    /**
     * @param int $count
     */
    public function removeCoinbomb(int $count = 1)
    {
        $this->setCoinBombs($this->coinBombs - $count);
    }

    /**
     * @return int
     */
    public function getHypeTrains(): int
    {
        return $this->hypeTrains;
    }

    /**
     * @param int $hypeTrains
     */
    public function setHypeTrains(int $hypeTrains): void
    {
        $this->hypeTrains = $hypeTrains;

        $playerName = $this->getPlayer()->getName();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function(mysqli $mysqli) use ($hypeTrains, $playerName): void {
            $mysqli->query("UPDATE Hypetrains SET hypetrains='$hypeTrains' WHERE playername='$playerName'");
        });
    }

    /**
     * @param int $count
     */
    public function addHypeTrains(int $count = 1)
    {
        $this->setHypeTrains($this->hypeTrains + $count);
    }

    /**
     * @param int $count
     */
    public function removeHypeTrains(int $count = 1)
    {
        $this->setHypeTrains($this->hypeTrains - $count);
    }

    /**
     * @param array $cosmetics
     */
    public function setCosmetics(array $cosmetics): void{
        $this->cosmetics = $cosmetics;
    }

    /**
     * @param array $activeCosmetics
     */
    public function setActiveCosmetics(array $activeCosmetics): void{
        $this->activeCosmetics = $activeCosmetics;
    }

    /**
     * @param Cosmetic $cosmetic
     */
    public function unlockCosmetic(Cosmetic $cosmetic): void {
        $this->cosmetics[$cosmetic->getIdentifier()] = $cosmetic;

        $playerName = $this->getPlayer()->getName();
        $cosmetic = $cosmetic->getIdentifier();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function(mysqli $mysqli) use ($playerName, $cosmetic): void {
            $mysqli->query("INSERT INTO Cosmetics (playername, cosmetic) VALUES ('$playerName', '$cosmetic')");
        });
    }

    /**
     * @param Cosmetic $cosmetic
     * @return bool
     */
    public function isCosmeticUnlocked(Cosmetic $cosmetic): bool {
        return isset($this->cosmetics[$cosmetic->getIdentifier()]);
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
        foreach($this->getActiveCosmetics() as $activeCosmetic) {
            if($activeCosmetic->getCategory() !== $cosmetic->getCategory()) continue;
            $this->deactivateCosmetic($activeCosmetic);
        }

        $this->activeCosmetics[$cosmetic->getIdentifier()] = $cosmetic;
        $cosmetic->onActivate($this->getPlayer());

        $playerName = $this->getPlayer()->getName();
        $cosmetic = $cosmetic->getIdentifier();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function(mysqli $mysqli) use ($playerName, $cosmetic): void {
            $mysqli->query("UPDATE Cosmetics SET active='1' WHERE playername='$playerName' AND cosmetic='$cosmetic'");
        });
    }

    /**
     * @param Cosmetic $cosmetic
     */
    public function deactivateCosmetic(Cosmetic $cosmetic): void {
        if(!$this->isCosmeticActivated($cosmetic)) return;
        unset($this->activeCosmetics[$cosmetic->getIdentifier()]);
        $cosmetic->onDeactivate($this->getPlayer());

        $playerName = $this->getPlayer()->getName();
        $cosmetic = $cosmetic->getIdentifier();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function(mysqli $mysqli) use ($playerName, $cosmetic): void {
            $mysqli->query("UPDATE Cosmetics SET active='0' WHERE playername='$playerName' AND cosmetic='$cosmetic'");
        });
    }

    /**
     * @param bool $shield
     */
    public function setShield(bool $shield): void
    {
        $this->shield = $shield;
    }

    public function enableShield(): void
    {
        $this->setShield(true);
    }

    public function disableShield(): void
    {
        $this->setShield(false);
    }

    /**
     * @return bool
     */
    public function enabledShield(): bool
    {
        return $this->shield;
    }

    /**
     * @return \baubolp\core\player\RyzerPlayer|null
     */
    public function asRyZerPlayer(): ?RyzerPlayer
    {
        return RyzerPlayerProvider::getRyzerPlayer($this->getPlayer()->getName());
    }

    /**
     * @return bool
     */
    public function isJoinAnimationEnabled(): bool
    {
        return true; //todo: settings
    }

    /**
     * @return bool
     */
    public function isNavigatorAnimationEnabled(): bool
    {
        return true; //todo: settings
    }

    /**
     * @return int
     */
    public function getAfkTicks(): int
    {
        return $this->afkTicks;
    }

    /**
     * @param int $ticks
     */
    public function addAfkTicks(int $ticks = 0): void {
        $this->afkTicks += $ticks;
    }

    public function resetAfkTicks(): void
    {
        $this->afkTicks = 0;
    }

    /**
     * @param bool $hasMoved
     */
    public function setHasMoved(bool $hasMoved = true): void
    {
        $this->hasMoved = $hasMoved;
    }

    /**
     * @return bool
     */
    public function hasMoved(): bool
    {
        return $this->hasMoved;
    }

    /**
     * @return bool
     */
    public function isAfk(): bool
    {
        return $this->isAfk;
    }

    /**
     * @param bool $afk
     */
    public function setAfk(bool $afk = true): void
    {
        $this->isAfk = $afk;
    }

    public function updateScoreboard(): void
    {
        $rbePlayer = $this->asRyZerPlayer();
        if($rbePlayer === null) {
            $playerName = $this->getPlayer()->getName();
            AsyncExecutor::submitClosureTask(40, function (int $currentTick) use ($playerName): void{
                $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($playerName);
                if($lobbyPlayer != null)
                    $lobbyPlayer->updateScoreboard();
            });
            return;
        }
        ScoreboardUtils::remove($this->getPlayer(), "lobby");
        ScoreboardUtils::create($this->getPlayer(), TextFormat::WHITE.TextFormat::BOLD."RyZer".TextFormat::RED."BE", "lobby");
        ScoreboardUtils::addEmptyLine($this->getPlayer(), 0, "lobby");
        ScoreboardUtils::addLine($this->getPlayer(), 1, TextFormat::GRAY."Rank", "lobby");
        ScoreboardUtils::addLine($this->getPlayer(), 2, TextFormat::DARK_GRAY."» ".str_replace("&", TextFormat::ESCAPE, explode(" ", RankProvider::getNameTag($rbePlayer->getRank()))[0]), "lobby");
        ScoreboardUtils::addEmptyLine($this->getPlayer(), 3, "lobby");
        ScoreboardUtils::addLine($this->getPlayer(), 4, TextFormat::GRAY."Coins", "lobby");
        ScoreboardUtils::addLine($this->getPlayer(), 5, TextFormat::DARK_GRAY."» ".TextFormat::AQUA.$rbePlayer->getCoins(), "lobby");
        ScoreboardUtils::addEmptyLine($this->getPlayer(), 6, "lobby");
        ScoreboardUtils::addLine($this->getPlayer(), 7, TextFormat::GRAY."Playtime", "lobby");
        ScoreboardUtils::addLine($this->getPlayer(), 8, TextFormat::DARK_GRAY."» ".TextFormat::AQUA.$rbePlayer->getOnlineTime(), "lobby");
        ScoreboardUtils::addEmptyLine($this->getPlayer(), 9, "lobby");
        ScoreboardUtils::addLine($this->getPlayer(), 10, TextFormat::GRAY."Clan", "lobby");
        ScoreboardUtils::addLine($this->getPlayer(), 11, TextFormat::DARK_GRAY."» ".TextFormat::YELLOW.$rbePlayer->getClan().TextFormat::GRAY."[".str_replace("&", TextFormat::ESCAPE, $rbePlayer->getClanTag()).TextFormat::GRAY."]", "lobby");
    }
}