<?php

namespace baubolp\ryzerbe\lobbycore;

use BauboLP\Cloud\Bungee\BungeeAPI;
use BauboLP\Cloud\CloudBridge;
use baubolp\ryzerbe\lobbycore\animation\AnimationProvider;
use baubolp\ryzerbe\lobbycore\animation\type\NavigatorTeleportAnimation;
use baubolp\ryzerbe\lobbycore\command\BuildCommand;
use baubolp\ryzerbe\lobbycore\command\CoinbombCommand;
use baubolp\ryzerbe\lobbycore\command\CosmeticCommand;
use baubolp\ryzerbe\lobbycore\command\DailyRewardCommand;
use baubolp\ryzerbe\lobbycore\command\EventCommand;
use baubolp\ryzerbe\lobbycore\command\FlyCommand;
use baubolp\ryzerbe\lobbycore\command\HypeTrainCommand;
use baubolp\ryzerbe\lobbycore\command\LottoCommand;
use baubolp\ryzerbe\lobbycore\command\PositionCommand;
use baubolp\ryzerbe\lobbycore\command\ResetNewsPopupCommand;
use baubolp\ryzerbe\lobbycore\command\RotateNPCCommand;
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
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
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
use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\RyZerBE;
use ryzerbe\core\util\async\AsyncExecutor;
use ryzerbe\core\util\emote\EmoteIds;
use ryzerbe\core\util\loader\ListenerDirectoryLoader;
use function explode;
use function file_get_contents;
use function uniqid;

class Loader extends PluginBase {
    public const PREFIX = TextFormat::YELLOW . TextFormat::BOLD . "Lobby " . TextFormat::RESET;

    public static array $entityCheckQueue = [];
    public static bool $jumpAndRunEnabled = false;
    private static ?Loader $instance = null;

    public function onEnable(){
        self::$instance = $this;
        $this->registerCommands();
        ListenerDirectoryLoader::load($this, $this->getFile(), __DIR__ . "/listener/");
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
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
        date_default_timezone_set("Europe/Berlin");
        $level = $this->getServer()->getDefaultLevel();
        $level->setTime(Level::TIME_MIDNIGHT);
        $level->stopTime();
    }

    public function registerCommands(): void{
        Loader::getInstance()->getServer()->getCommandMap()->registerAll("lobbycore", [
            new BuildCommand(),
            #  new PrivateServerCommand(),
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
            new ShopCommand(),
            new RotateNPCCommand(),
        ]);
    }

    public static function getInstance(): ?Loader{
        return self::$instance;
    }

    public function registerEntities(): void{
        $entities = [
            CoinBombMinecartEntity::class,
            ItemRainItemEntity::class,
            HypeTrainEntity::class,
            HypeTrainWagonEntity::class,
            HeadProjectileEntity::class,
            NPCEntity::class,
            EventPortalEntity::class,
        ];
        foreach($entities as $entity){
            Entity::registerEntity($entity, true);
        }
    }

    public function startTasks(): void{
        $this->getScheduler()->scheduleRepeatingTask(new AnimationTask(), 1);
        $this->getScheduler()->scheduleRepeatingTask(new LobbyTask(), 5);
    }

    public static function createMySQLTables(): void{
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function(mysqli $mysqli){
            $mysqli->query("CREATE TABLE IF NOT EXISTS LottoTickets(id INTEGER NOT NULL KEY AUTO_INCREMENT, playername varchar(16) NOT NULL, tickets integer NOT NULL)");
            $mysqli->query("CREATE TABLE IF NOT EXISTS Position(id INTEGER NOT NULL KEY AUTO_INCREMENT, playername varchar(16) NOT NULL, position varchar(32) NOT NULL)");
            $mysqli->query("CREATE TABLE IF NOT EXISTS LoginStreak(id INTEGER NOT NULL KEY AUTO_INCREMENT, playername varchar(32) NOT NULL, loginstreak integer NOT NULL, nextstreakday integer NOT NULL, laststreakday integer NOT NULL)");
            $mysqli->query("CREATE TABLE IF NOT EXISTS DailyReward(id INTEGER NOT NULL KEY AUTO_INCREMENT, playername varchar(16) NOT NULL, coins integer NOT NULL, lottoticket integer NOT NULL, coinbomb integer NOT NULL, hypetrain integer NOT NULL, xp integer NOT NULL)");
            $mysqli->query("CREATE TABLE IF NOT EXISTS Status(id INTEGER NOT NULL KEY AUTO_INCREMENT, playername varchar(32) NOT NULL, status varchar(25) NOT NULL)");
            $mysqli->query("CREATE TABLE IF NOT EXISTS Coinbombs(id INTEGER NOT NULL KEY AUTO_INCREMENT, playername varchar(32) NOT NULL, bombs integer NOT NULL)");
            $mysqli->query("CREATE TABLE IF NOT EXISTS Cosmetics(id INT NOT NULL KEY AUTO_INCREMENT, playername VARCHAR(32) NOT NULL, cosmetic VARCHAR(128) NOT NULL, active INT NOT NULL DEFAULT '0')");
            $mysqli->query("CREATE TABLE IF NOT EXISTS Hypetrains(id INT NOT NULL KEY AUTO_INCREMENT, playername VARCHAR(32) NOT NULL , hypetrains INT NOT NULL DEFAULT '0')");
            $mysqli->query("CREATE TABLE IF NOT EXISTS News(id INT NOT NULL KEY AUTO_INCREMENT, playername VARCHAR(32) NOT NULL)");
            $mysqli->query("CREATE TABLE IF NOT EXISTS Settings(id INT NOT NULL KEY AUTO_INCREMENT, playername VARCHAR(32) NOT NULL, settings TEXT NOT NULL DEFAULT '1:1:1:1:1')");
            $mysqli->query("CREATE TABLE IF NOT EXISTS Surveys(id INT NOT NULL KEY AUTO_INCREMENT, playername VARCHAR(32) NOT NULL, surveyid TEXT NOT NULL, answerid TEXT NOT NULL)");
        });
    }

    public function loadConfig(): void{
        if(!is_file("/root/RyzerCloud/data/Lobby/config.json")){
            $config = new Config("/root/RyzerCloud/data/Lobby/config.json");
            $config->set("warps", []);
            $config->set("games", [
                "BedWars" => ["warpName" => "bedwars", "groups" => ["BW2x1", "BW2x2"], "icon" => ""],
                "FlagWars" => ["warpName" => "flagwars", "icon" => "", "groups" => ["FlagWars4x2"]],
            ]);
            $config->set("bossbarMessages", []);
            $config->set("news", []);
            $config->set("event", null);
            $config->set("survey", []);
            $config->save();
        }
        $config = new Config("/root/RyzerCloud/data/Lobby/config.json");
        foreach(array_keys($config->get("games")) as $key){
            $data = $config->get("games")[$key];
            if(isset($data["warpName"])){
                NavigatorForm::$games[$key] = [
                    "icon" => $data["icon"],
                    "warpName" => $data["warpName"],
                    "groups" => $data["groups"],
                    "players" => 0,
                ];
            }
            else{
                if(isset($data["directConnect"])){
                    NavigatorForm::$games[$key] = [
                        "icon" => $data["icon"],
                        "directConnect" => $data["directConnect"],
                        "groups" => $data["groups"],
                        "players" => 0,
                    ];
                }
            }
        }
        $news = (array)$config->get("news");
        if(count($news) > 0){
            NewsBookForm::$news = str_replace("&", TextFormat::ESCAPE, implode("\n", $news));
        }
        EventProvider::reload();
    }

    public function registerPermissions(): void{
        $permissions = [
            "lobby.build",
            "lobby.coinbomb",
            "lobby.fly",
            "lobby.hypetrain",
            "lobby.resetpopup",
            "lobby.status",
            "lobby.warp",
            "lobby.event",
            "lobby.position",
        ];
        foreach($permissions as $permission){
            PermissionManager::getInstance()->addPermission(new Permission($permission, "lobby permission"));
        }
    }

    private function loadNPCs(): void{
        $EmoteIds = [
            EmoteIds::WAVE,
            EmoteIds::THE_WOODPUNCH,
            EmoteIds::UNDERWATER_DANCING,
            EmoteIds::HAND_STAND,
            EmoteIds::SHY_GIGGLING,
            EmoteIds::MEDITATING_LIKE_LUKE,
            EmoteIds::OFFERING,
            EmoteIds::BORED,
            EmoteIds::AHH_CHOO,
            EmoteIds::GIDDY,
            EmoteIds::OVER_HERE,
            EmoteIds::GROOVIN,
            EmoteIds::WAVING_LIKE_C_3PO,
            EmoteIds::THINKING,
            EmoteIds::SURRENDERING,
        ];
        $closure = (function(Player $player, NPCEntity $entity): void{
            $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
            if($lobbyPlayer === null) return;
            $warp = WarpProvider::getWarp($entity->namedtag->getString("warpName", "N/A"));
            if($warp === null){
                $directConnect = $entity->namedtag->getString("directConnect", "N/A");
                if($directConnect != "N/A"){
                    if($directConnect === "TrainingLobby"){
                        $server = CloudBridge::getCloudProvider()->getRunningServersByGroup("TrainingLobby")[0] ?? null;
                        if($server === null) return;
                        $directConnect = $server;
                    }
                    BungeeAPI::transferPlayer($player->getName(), $directConnect);
                }
                return;
            }
            $game = TextFormat::clean(explode("\n", $entity->getNameTag())[0]);
            if($lobbyPlayer->isNavigatorAnimationEnabled()){
                $player->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn()->add(0, 1));
                AnimationProvider::addActiveAnimation(new NavigatorTeleportAnimation($player, $warp, $game));
            }
            else{
                $player->teleport($warp->getLocation());
            }
        });

        // CWBW-Training
        $skin = new Skin(uniqid(), SkinUtils::readImage("/root/RyzerCloud/data/NPC/cwbwtraining.png"), "", (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("name"), (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("geo"));
        $npc = new NPCEntity(new Location(230.5, 72, 272.5, 0, 0, Server::getInstance()->getDefaultLevel()), $skin);
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setEmotes($EmoteIds);
        $npc->updateTitle(TextFormat::YELLOW . "CWBW-Training", TextFormat::BLACK . "♠ " . TextFormat::AQUA . "Practice CWBW Scenarios" . TextFormat::BLACK . " ♠");
        $npc->namedtag->setString("warpName", "cwtraining");
        $npc->spawnToAll();

        // CW-Training Inventory Sort
        $npc = new NPCEntity(new Location(300.5, 69, 320.5, 0, 0, Server::getInstance()->getDefaultLevel()), $skin);
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setEmotes($EmoteIds);
        $npc->setLookAtPlayer(true);
        $npc->updateTitle(TextFormat::YELLOW . "Sort your inventories", "");
        $npc->namedtag->setString("directConnect", "onlysortcwt");
        $npc->spawnToAll();

        // FlagWars
        $skin = new Skin(uniqid(), SkinUtils::readImage("/root/RyzerCloud/data/NPC/flagwars.png"), "", (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("name"), (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("geo"));
        $npc = new NPCEntity(new Location(224.5, 72, 272.5, 0, 0, Server::getInstance()->getDefaultLevel()), $skin);
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setEmotes($EmoteIds);
        $npc->updateTitle(TextFormat::DARK_AQUA . "Flag" . TextFormat::AQUA . "Wars", TextFormat::BLACK . "♠ " . TextFormat::GREEN . "NEW GAME" . TextFormat::BLACK . " ♠");
        $npc->namedtag->setString("warpName", "flagwars");
        $npc->spawnToAll();

        // "Coming Soon"
        $skin = new Skin(uniqid(), SkinUtils::readImage("/root/RyzerCloud/data/NPC/questionmark.png"), "", (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("name"), (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("geo"));
        $npc = new NPCEntity(new Location(216.5, 71, 271.5, 0, 0, Server::getInstance()->getDefaultLevel()), $skin);
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setEmotes($EmoteIds);
        $npc->updateTitle(TextFormat::WHITE . TextFormat::BOLD . "???", "");
        $npc->spawnToAll();

        // FFA
        $skin = new Skin(uniqid(), SkinUtils::readImage("/root/RyzerCloud/data/NPC/ffa.png"), "", (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("name"), (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("geo"));
        $npc = new NPCEntity(new Location(238.5, 71, 273.5, 0, 0, Server::getInstance()->getDefaultLevel()), $skin);
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setEmotes($EmoteIds);
        $npc->updateTitle(TextFormat::GOLD . "FFA", TextFormat::BLACK . "♠ " . TextFormat::GREEN . "FFA & BuildFFA" . TextFormat::BLACK . " ♠");
        $npc->namedtag->setString("warpName", "ffa");
        $npc->spawnToAll();

        // Training
        $skin = new Skin(uniqid(), SkinUtils::readImage("/root/RyzerCloud/data/NPC/training.png"), "", (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("name"), (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("geo"));
        $npc = new NPCEntity(new Location(219.5, 71, 274.5, 0, 0, Server::getInstance()->getDefaultLevel()), $skin);
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setEmotes($EmoteIds);
        $npc->updateTitle(TextFormat::WHITE . "Training", TextFormat::BLACK . "♠ " . TextFormat::YELLOW . "Practice and prove your skills" . TextFormat::BLACK . " ♠");
        $npc->namedtag->setString("directConnect", "TrainingLobby");
        $npc->spawnToAll();

        // BedWars
        $skin = new Skin(
            uniqid(),
            SkinUtils::readImage("/root/RyzerCloud/data/NPC/bedwars.png"),
            "",
            "geometry.bedwars",
            file_get_contents("/root/RyzerCloud/data/NPC/geo_bedwars.json")
        );
        $npc = new NPCEntity(new Location(234.5, 71, 274.5, 0, 0, Server::getInstance()->getDefaultLevel()), $skin);
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setEmotes($EmoteIds);
        $npc->updateTitle(TextFormat::DARK_AQUA . "Bedwars", TextFormat::BLACK . "♠ " . TextFormat::RED . "Cool Maps & Cosmetics" . TextFormat::BLACK . " ♠");
        $npc->namedtag->setString("warpName", "bedwars");
        $npc->spawnToAll();

        // JumpAndRun
        $skin = new Skin(uniqid(), SkinUtils::readImage("/root/RyzerCloud/data/NPC/jumpandrun.png"), "", (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("name"), (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("geo"));
        $npc = new NPCEntity(new Location(242.5, 70, 286.5, 0, 0, Server::getInstance()->getDefaultLevel()), $skin);
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setEmotes($EmoteIds);
        $npc->updateTitle(TextFormat::GOLD . "Jump and Run", TextFormat::BLACK . "♠ " . TextFormat::WHITE . "WITH STATS" . TextFormat::BLACK . " ♠");
        $npc->namedtag->setString("warpName", "JaR");
        $npc->spawnToAll();

        // Coin Shop
        $npc = new NPCEntity(new Location(213.5, 70, 291.5, 200, 0, Server::getInstance()->getDefaultLevel()), new Skin(uniqid(), SkinUtils::readImage("/root/RyzerCloud/data/NPC/RankShop.png"), "", "geometry.Mobs.Zombie", file_get_contents("/root/RyzerCloud/data/NPC/rankshop_geometry.json")));
        $closure = function(Player $player): void{
            $player->getServer()->dispatchCommand($player, "shop");
        };
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setScale(1.5);
        $npc->updateTitle(TextFormat::GOLD . "Coinshop", "");
        $npc->spawnToAll();

        // Private Server
        $npc = new NPCEntity(new Location(235.5, 73, 306.5, 150, 0, Server::getInstance()->getDefaultLevel()), new Skin(uniqid(), SkinUtils::readImage("/root/RyzerCloud/data/NPC/PServer.png"), "", "geometry.normal1", file_get_contents("/root/RyzerCloud/data/NPC/pserver_geometry.json")));
        $closure = function(Player $player): void{
            $player->getServer()->dispatchCommand($player, "ps");
        };
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setScale(1.5);
        $npc->updateTitle(TextFormat::DARK_PURPLE . "Private Server", TextFormat::BLACK . "♠ " . TextFormat::AQUA . "PRIME RANK " . TextFormat::BLACK . "♠");
        //$npc->spawnToAll(); come soon

        // Lotto
        $npc = new NPCEntity(new Location(221.5, 73, 306.5, 200, 0, Server::getInstance()->getDefaultLevel()), new Skin(uniqid(), SkinUtils::readImage("/root/RyzerCloud/data/NPC/Lotto.png"), "", "geometry.normal1", file_get_contents("/root/RyzerCloud/data/NPC/lotto_geometry.json")));
        $closure = function(Player $player): void{
            $player->getServer()->dispatchCommand($player, "lotto");
        };
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setScale(1.5);
        $npc->updateTitle(TextFormat::YELLOW . "Lotto", TextFormat::BLACK . "♠ " . TextFormat::GOLD . "PLAY WITH YOUR COINS" . TextFormat::BLACK . "♠");
        $npc->spawnToAll();


        // Daily Reward

        $skin = new Skin(
            uniqid(),
            SkinUtils::readImage("/root/RyzerCloud/data/NPC/dailyrewards.png"),
            "",
            "geometry.dailyrewards",
            file_get_contents("/root/RyzerCloud/data/NPC/geo_dailyrewards.json")
        );
        $npc = new NPCEntity(new Location(231.5, 73, 300.5, 0, 0, Server::getInstance()->getDefaultLevel()), $skin);
        $closure = function(Player $player): void{
            $player->getServer()->dispatchCommand($player, "dailyreward");
        };
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setEmotes($EmoteIds);
        $npc->updateTitle(TextFormat::DARK_GREEN . "Daily Rewards", TextFormat::BLACK . "♠ " . TextFormat::RED . "FOR YOU" . TextFormat::BLACK . " ♠");
        $npc->spawnToAll();

        // Survey
        $skin = new Skin(uniqid(), SkinUtils::readImage("/root/RyzerCloud/data/NPC/baubo.png"), "", (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("name"), (new Config("/root/RyzerCloud/data/NPC/default_geometry.json"))->get("geo"));
        $npc = new NPCEntity(new Location(225.5, 73, 300.5, 0, 0, Server::getInstance()->getDefaultLevel()), $skin);
        $closure = function(Player $player): void{
            $player->getServer()->dispatchCommand($player, "survey");
        };
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setEmotes($EmoteIds);
        $npc->updateTitle(TextFormat::YELLOW . "Survey", TextFormat::BLACK . "♠ " . TextFormat::RED . "GET COINS FOR VOTING" . TextFormat::BLACK . " ♠");
        $npc->spawnToAll();


        // SANTA CLAUS \\

        $skin = new Skin(
            uniqid(),
            SkinUtils::readImage("/root/RyzerCloud/data/NPC/santa_claus.png"),
            "",
            "geometry.weihnachtsmann",
            file_get_contents("/root/RyzerCloud/data/NPC/geo_santa_claus.json")
        );
        $npc = new NPCEntity(new Location(204.5, 95, 271.5, 0.0, 0.0, Server::getInstance()->getDefaultLevel()), $skin);
        $npc->updateTitle(TextFormat::RED.TextFormat::BOLD."Santa Claus", TextFormat::WHITE."Merry Christmas!");
        $npc->setScale(1.3);
        $closure = function(Player $player): void{
            $player->sendMessage(RyZerBE::PREFIX.LanguageProvider::getMessageContainer("santa-claus-info", $player));
        };
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->spawnToAll();
    }
}