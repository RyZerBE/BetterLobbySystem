<?php

namespace baubolp\ryzerbe\lobbycore\provider;

use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\util\ClanWarMatch;
use mysqli;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use ryzerbe\core\util\async\AsyncExecutor;

class RunningClanWarProvider {
    /** @var ClanWarMatch[] */
    public static $runningClanWars = [];

    /**
     * @return ClanWarMatch[]
     */
    public static function getRunningClanWars(): array{
        return self::$runningClanWars;
    }

    public static function updateRunningClanWars(): void{
        Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new class extends Task {
            /**
             * @inheritDoc
             */
            public function onRun(int $currentTick){
                AsyncExecutor::submitMySQLAsyncTask("Clans", function(mysqli $mysqli){
                    $result = $mysqli->query("SELECT * FROM RCW");
                    $rcw = [];
                    if($result->num_rows > 0){
                        while($data = $result->fetch_assoc()) $rcw[$data["server"]] = explode("*", $data["informations"]);
                    }
                    return $rcw;
                }, function(Server $server, array $matchesData){
                    $matches = [];
                    foreach(array_keys($matchesData) as $server){
                        $data = $matchesData[$server];
                        $matches[$server] = new ClanWarMatch($data[2], $data[4], $data[1], $server, true);
                    }
                    RunningClanWarProvider::setRunningClanWars($matches);
                });
            }
        }, 20 * 10);
    }

    /**
     * @param ClanWarMatch[] $runningClanWars
     */
    public static function setRunningClanWars(array $runningClanWars): void{
        self::$runningClanWars = $runningClanWars;
    }
}