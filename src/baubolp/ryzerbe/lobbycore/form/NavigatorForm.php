<?php


namespace baubolp\ryzerbe\lobbycore\form;

use BauboLP\Cloud\Bungee\BungeeAPI;
use BauboLP\Cloud\CloudBridge;
use baubolp\core\Ryzer;
use baubolp\ryzerbe\lobbycore\animation\AnimationProvider;
use baubolp\ryzerbe\lobbycore\animation\type\NavigatorTeleportAnimation;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use baubolp\ryzerbe\lobbycore\provider\WarpProvider;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use function array_keys;
use function in_array;

class NavigatorForm
{
    /** @var array */
    public static $games = [];
    /** @var array  */
    public static array $games_order = [];

    public static function open(Player $player)
    {
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if ($lobbyPlayer === null) return;

        $playername = $player->getName();
        $games = self::$games_order[$playername] ?? array_keys(self::$games);

        $form = new SimpleForm(function (Player $player, $data) use ($lobbyPlayer, $games): void {
            if ($data === null) return;
            $data = explode(":", $data);

            $warp = WarpProvider::getWarp($data[0]);
            if ($warp === null) {
                if($data[0] === "challenge") { //REMOVE
                    if(!$player->hasPermission("cloud.admin")) {
                        $player->sendMessage(Ryzer::PREFIX.TextFormat::DARK_RED."Du hast keine Befugniss den Trainingserver zu betreten, da der gerade in Entwicklung ist! Permission: cloud.admin");
                        return;
                    }
                    BungeeAPI::transferPlayer($player->getName(), "challenge");
                }else {
                    BungeeAPI::transferPlayer($player->getName(), $data[0]);
                }
                return;
            }

            $games__ = [$data[1]];
            foreach($games as $game) {
                if(!in_array($game, $games__)) $games__[] = $game;
            }
            self::$games_order[$player->getName()] = $games__;


            if ($lobbyPlayer->isNavigatorAnimationEnabled()){
                AnimationProvider::addActiveAnimation(new NavigatorTeleportAnimation($player, $warp, $data[1]));
            }else{
                $player->teleport($warp->getLocation(), $player->getYaw(), 0);
            }
        });

        $form->setTitle(TextFormat::AQUA . TextFormat::BOLD . "Games");
        foreach ($games as $game){
            $icon = self::$games[$game]["icon"];
            $warpName = self::$games[$game]["warpName"] ?? self::$games[$game]["directConnect"];

            if($icon == "-1")
                $form->addButton($game, -1, "", $warpName.":".$game);
            else{
                $form->addButton($game, 1, $icon, $warpName.":".$game);
            }
        }
        $form->sendToPlayer($player);
    }
}