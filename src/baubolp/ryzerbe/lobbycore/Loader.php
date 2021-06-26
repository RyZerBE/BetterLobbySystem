<?php


namespace baubolp\ryzerbe\lobbycore;


use baubolp\core\provider\AsyncExecutor;
use baubolp\ryzerbe\lobbycore\command\BuildCommand;
use baubolp\ryzerbe\lobbycore\command\FlyCommand;
use baubolp\ryzerbe\lobbycore\command\LottoCommand;
use baubolp\ryzerbe\lobbycore\command\PrivateServerCommand;
use baubolp\ryzerbe\lobbycore\listener\PlayerJoinListener;
use baubolp\ryzerbe\lobbycore\listener\PlayerJoinNetworkListener;
use baubolp\ryzerbe\lobbycore\task\AnimationTask;
use baubolp\ryzerbe\lobbycore\task\LobbyTask;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase
{
    /** @var self */
    private static $instance = null;

    const PREFIX = TextFormat::YELLOW.TextFormat::BOLD."Lobby ".TextFormat::RESET;

    public function onEnable()
    {
       self::$instance = $this;
       $this->registerCommands();
       $this->registerListeners();
       $this->startTasks();
       self::createMySQLTables();

    }

    /**
     * @return \baubolp\ryzerbe\lobbycore\Loader
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    public function registerCommands(): void
    {
        Loader::getInstance()->getServer()->getCommandMap()->registerAll("lobbycore", [
            new BuildCommand(),
            new PrivateServerCommand(),
            new FlyCommand(),
            new LottoCommand()
        ]);
    }

    public function registerListeners(): void
    {
        $listeners = [
            new PlayerJoinNetworkListener(),
            new PlayerJoinListener()
        ];

        foreach ($listeners as $listener)
            $this->getServer()->getPluginManager()->registerEvents($listener, $this);
    }

    public function startTasks(): void
    {
        $this->getScheduler()->scheduleRepeatingTask(new AnimationTask(), 1);
        $this->getScheduler()->scheduleRepeatingTask(new LobbyTask(), 10);
    }

    public static function createMySQLTables(): void
    {
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli){
            $mysqli->query("CREATE TABLE IF NOT EXISTS LottoTickets(id INTEGER NOT NULL KEY AUTO_INCREMENT, playername varchar(16) NOT NULL, tickets integer NOT NULL)");
        });
    }
}