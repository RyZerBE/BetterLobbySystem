<?php


namespace baubolp\ryzerbe\lobbycore;


use baubolp\core\provider\AsyncExecutor;
use baubolp\core\util\Emotes;
use baubolp\ryzerbe\lobbycore\command\BuildCommand;
use baubolp\ryzerbe\lobbycore\command\CoinbombCommand;
use baubolp\ryzerbe\lobbycore\command\CosmeticCommand;
use baubolp\ryzerbe\lobbycore\command\DailyRewardCommand;
use baubolp\ryzerbe\lobbycore\command\EventCommand;
use baubolp\ryzerbe\lobbycore\command\FlyCommand;
use baubolp\ryzerbe\lobbycore\command\HypeTrainCommand;
use baubolp\ryzerbe\lobbycore\command\LottoCommand;
use baubolp\ryzerbe\lobbycore\command\PositionCommand;
use baubolp\ryzerbe\lobbycore\command\PrivateServerCommand;
use baubolp\ryzerbe\lobbycore\command\ResetNewsPopupCommand;
use baubolp\ryzerbe\lobbycore\command\RunningClanWarsCommand;
use baubolp\ryzerbe\lobbycore\command\ShopCommand;
use baubolp\ryzerbe\lobbycore\command\StatusCommand;
use baubolp\ryzerbe\lobbycore\command\SurveyCommand;
use baubolp\ryzerbe\lobbycore\command\WarpCommand;
use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\entity\CoinBombMinecartEntity;
use baubolp\ryzerbe\lobbycore\entity\EventPortalEntity;
use baubolp\ryzerbe\lobbycore\entity\hypetrain\HypeTrainEntity;
use baubolp\ryzerbe\lobbycore\entity\hypetrain\HypeTrainWagonEntity;
use baubolp\ryzerbe\lobbycore\entity\hypetrain\projectile\HeadProjectileEntity;
use baubolp\ryzerbe\lobbycore\entity\ItemRainItemEntity;
use baubolp\ryzerbe\lobbycore\entity\NPCEntity;
use baubolp\ryzerbe\lobbycore\form\NavigatorForm;
use baubolp\ryzerbe\lobbycore\form\NewsBookForm;
use baubolp\ryzerbe\lobbycore\listener\BlockBreakListener;
use baubolp\ryzerbe\lobbycore\listener\BlockFormListener;
use baubolp\ryzerbe\lobbycore\listener\BlockGrowListener;
use baubolp\ryzerbe\lobbycore\listener\BlockPlaceListener;
use baubolp\ryzerbe\lobbycore\listener\BlockUpdateListener;
use baubolp\ryzerbe\lobbycore\listener\ChunkLoaderListener;
use baubolp\ryzerbe\lobbycore\listener\EntityDamageListener;
use baubolp\ryzerbe\lobbycore\listener\InventoryPickupItemListener;
use baubolp\ryzerbe\lobbycore\listener\InventoryTransactionListener;
use baubolp\ryzerbe\lobbycore\listener\LeavesDecayListener;
use baubolp\ryzerbe\lobbycore\listener\PlayerDropItemListener;
use baubolp\ryzerbe\lobbycore\listener\PlayerExhaustListener;
use baubolp\ryzerbe\lobbycore\listener\PlayerInteractListener;
use baubolp\ryzerbe\lobbycore\listener\PlayerJoinListener;
use baubolp\ryzerbe\lobbycore\listener\PlayerJoinNetworkListener;
use baubolp\ryzerbe\lobbycore\listener\PlayerMoveListener;
use baubolp\ryzerbe\lobbycore\listener\PlayerQuitListener;
use baubolp\ryzerbe\lobbycore\provider\EventProvider;
use baubolp\ryzerbe\lobbycore\provider\RunningClanWarProvider;
use baubolp\ryzerbe\lobbycore\provider\SurveyProvider;
use baubolp\ryzerbe\lobbycore\provider\WarpProvider;
use baubolp\ryzerbe\lobbycore\shop\ShopManager;
use baubolp\ryzerbe\lobbycore\task\AnimationTask;
use baubolp\ryzerbe\lobbycore\task\LobbyTask;
use baubolp\ryzerbe\lobbycore\util\SkinUtils;
use muqsit\invmenu\InvMenuHandler;
use mysqli;
use pocketmine\entity\Entity;
use pocketmine\entity\Skin;
use pocketmine\level\Location;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use function microtime;
use function uniqid;
use function var_dump;

class Loader extends PluginBase
{
    /** @var self */
    private static $instance = null;

    /** @var bool  */
    public static $jumpAndRunEnabled = false;

    const PREFIX = TextFormat::YELLOW.TextFormat::BOLD."Lobby ".TextFormat::RESET;

    public function onEnable()
    {
        self::$instance = $this;
        $this->registerCommands();
        $this->registerListeners();
        $this->registerEntities();
        $this->startTasks();
        self::createMySQLTables();
        $this->loadConfig();
        $this->registerPermissions();
        $this->loadNPCs();

        self::$jumpAndRunEnabled = (Server::getInstance()->getPluginManager()->getPlugin("GommeJumpAndRun") !== null);


        CosmeticManager::getInstance();
        WarpProvider::loadWarps();
        SurveyProvider::loadSurvey();
        ShopManager::registerCategories();
        RunningClanWarProvider::updateRunningClanWars();
        new EventProvider();

        if (!InvMenuHandler::isRegistered())
            InvMenuHandler::register($this);

        date_default_timezone_set("Europe/Berlin");
    }

    /**
     * @return Loader
     */
    public static function getInstance(): ?Loader{
        return self::$instance;
    }

    public function registerCommands(): void
    {
        Loader::getInstance()->getServer()->getCommandMap()->registerAll("lobbycore", [
            new BuildCommand(),
            new PrivateServerCommand(),
            new FlyCommand(),
            new LottoCommand(),
            new DailyRewardCommand(),
            new StatusCommand(),
            new CoinbombCommand(),
            new CosmeticCommand(),
            new WarpCommand(),
            new HypeTrainCommand(),
            new ResetNewsPopupCommand(),
            new EventCommand(),
            new PositionCommand(),
            new SurveyCommand(),
            new RunningClanWarsCommand(),
            new ShopCommand()
        ]);
    }

    public function registerListeners(): void
    {
        $listeners = [
            new PlayerJoinNetworkListener(),
            new PlayerJoinListener(),
            new PlayerQuitListener(),
            new InventoryTransactionListener(),
            new BlockPlaceListener(),
            new BlockBreakListener(),
            new PlayerDropItemListener(),
            new PlayerExhaustListener(),
            new EntityDamageListener(),
            new BlockGrowListener(),
            new BlockUpdateListener(),
            new BlockFormListener(),
            new LeavesDecayListener(),
            new PlayerInteractListener(),
            new InventoryPickupItemListener(),
            new PlayerMoveListener(),
            new ChunkLoaderListener()
        ];

        foreach ($listeners as $listener)
            $this->getServer()->getPluginManager()->registerEvents($listener, $this);
    }

    private function loadNPCs(): void {
        /*
         * Available Positions
         *
         * 230.5, 72, 272.5, 0, 0 //Used
         * 224.5, 72, 272.5, 0, 0 //Used
         *
         * 234.5, 71, 274.5, 0, 0
         * 238.5, 71, 273.5, 0, 0
         * 219.5, 71, 274.5, 0, 0
         * 216.5, 71, 271.5, 0, 0
         */

        $emotes = [
            Emotes::WAVE, Emotes::THE_WOODPUNCH, Emotes::UNDERWATER_DANCING, Emotes::HAND_STAND, Emotes::SHY_GIGGLING, Emotes::MEDITATING_LIKE_LUKE, Emotes::OFFERING,
            Emotes::BORED, Emotes::AHH_CHOO, Emotes::GIDDY, Emotes::OVER_HERE, Emotes::GROOVIN, Emotes::WAVING_LIKE_C_3PO, Emotes::THINKING, Emotes::SURRENDERING
        ];

        $skin = new Skin(
            uniqid(),
            SkinUtils::readImage("/root/RyzerCloud/data/NPC/backup_skin.png"),
            "",
            (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("name"),
            (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("geo")
        );

        //Maybe BedWars?
        $npc = new NPCEntity(new Location(230.5, 72, 272.5, 0, 0, Server::getInstance()->getDefaultLevel()), $skin);
        $closure = (function(Player $player): void {
            $player->sendMessage("Hi");
        });
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setEmotes($emotes);
        $npc->setNameTag("§l§aBedWars?");
        $npc->spawnToAll();

        //Maybe FlagWars?
        $npc = new NPCEntity(new Location(224.5, 72, 272.5, 0, 0, Server::getInstance()->getDefaultLevel()), $skin);
        $closure = (function(Player $player): void {
            $player->sendMessage("Bye");
        });
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setEmotes($emotes);
        $npc->setNameTag("§l§aFlagWars?");
        $npc->spawnToAll();
    }

    public function registerEntities(): void {
        $entities = [
            CoinBombMinecartEntity::class,
            ItemRainItemEntity::class,
            HypeTrainEntity::class,
            HypeTrainWagonEntity::class,
            HeadProjectileEntity::class,
            NPCEntity::class,
            EventPortalEntity::class
        ];
        foreach($entities as $entity) {
            Entity::registerEntity($entity, true);
        }
    }

    public function startTasks(): void
    {
        $this->getScheduler()->scheduleRepeatingTask(new AnimationTask(), 1);
        $this->getScheduler()->scheduleRepeatingTask(new LobbyTask(), 5);
    }

    public static function createMySQLTables(): void
    {
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (mysqli $mysqli) {
            $mysqli->query("CREATE TABLE IF NOT EXISTS LottoTickets(id INTEGER NOT NULL KEY AUTO_INCREMENT, playername varchar(16) NOT NULL, tickets integer NOT NULL)");
            $mysqli->query("CREATE TABLE IF NOT EXISTS Position(id INTEGER NOT NULL KEY AUTO_INCREMENT, playername varchar(16) NOT NULL, position varchar(32) NOT NULL)");
            $mysqli->query("CREATE TABLE IF NOT EXISTS LoginStreak(id INTEGER NOT NULL KEY AUTO_INCREMENT, playername varchar(32) NOT NULL, loginstreak integer NOT NULL, nextstreakday integer NOT NULL, laststreakday integer NOT NULL)");
            $mysqli->query("CREATE TABLE IF NOT EXISTS DailyReward(id INTEGER NOT NULL KEY AUTO_INCREMENT, playername varchar(16) NOT NULL, coins integer NOT NULL, lottoticket integer NOT NULL, coinbomb integer NOT NULL, hypetrain integer NOT NULL)");
            $mysqli->query("CREATE TABLE IF NOT EXISTS Status(id INTEGER NOT NULL KEY AUTO_INCREMENT, playername varchar(32) NOT NULL, status varchar(25) NOT NULL)");
            $mysqli->query("CREATE TABLE IF NOT EXISTS Coinbombs(id INTEGER NOT NULL KEY AUTO_INCREMENT, playername varchar(32) NOT NULL, bombs integer NOT NULL)");
            $mysqli->query("CREATE TABLE IF NOT EXISTS Cosmetics(id INT NOT NULL KEY AUTO_INCREMENT, playername VARCHAR(32) NOT NULL, cosmetic VARCHAR(128) NOT NULL, active INT NOT NULL DEFAULT '0')");
            $mysqli->query("CREATE TABLE IF NOT EXISTS Hypetrains(id INT NOT NULL KEY AUTO_INCREMENT, playername VARCHAR(32) NOT NULL , hypetrains INT NOT NULL DEFAULT '0')");
            $mysqli->query("CREATE TABLE IF NOT EXISTS News(id INT NOT NULL KEY AUTO_INCREMENT, playername VARCHAR(32) NOT NULL)");
            $mysqli->query("CREATE TABLE IF NOT EXISTS Settings(id INT NOT NULL KEY AUTO_INCREMENT, playername VARCHAR(32) NOT NULL, settings TEXT NOT NULL DEFAULT '1:1:1:1:1')");
            $mysqli->query("CREATE TABLE IF NOT EXISTS Surveys(id INT NOT NULL KEY AUTO_INCREMENT, playername VARCHAR(32) NOT NULL, surveyid TEXT NOT NULL, answerid TEXT NOT NULL)");
        });
    }

    public function loadConfig(): void
    {
        if (!is_file("/root/RyzerCloud/data/Lobby/config.json")) {
            $config = new Config("/root/RyzerCloud/data/Lobby/config.json");
            $config->set("warps", []);
            $config->set("games", ["BedWars" => ["warpName" => "bedwars", "icon" => ""], "FlagWars" => ["warpName" => "flagwars", "icon" => ""]]);
            $config->set("bossbarMessages", []);
            $config->set("news", []);
            $config->set("event", null);
            $config->set("survey", []);
            $config->save();
        }
        $config = new Config("/root/RyzerCloud/data/Lobby/config.json");

        foreach (array_keys($config->get("games")) as $key) {
            $data = $config->get("games")[$key];
            NavigatorForm::$games[$key] = ["icon" => $data["icon"], "warpName" => $data["warpName"]];
        }

        $news = (array)$config->get("news");
        if(count($news) > 0)
        NewsBookForm::$news = str_replace("&", TextFormat::ESCAPE, implode("\n", $news));

        EventProvider::reload();
    }

    public function registerPermissions(): void
    {
        $permissions = [
            "lobby.build",
            "lobby.coinbomb",
            "lobby.fly",
            "lobby.hypetrain",
            "lobby.resetpopup",
            "lobby.status",
            "lobby.warp",
            "lobby.event",
            "lobby.position"
        ];

        foreach ($permissions as $permission)
            PermissionManager::getInstance()->addPermission(new Permission($permission, "lobby permission"));
    }
}