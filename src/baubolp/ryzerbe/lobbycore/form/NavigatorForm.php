<?php

namespace baubolp\ryzerbe\lobbycore\form;

use BauboLP\Cloud\Bungee\BungeeAPI;
use BauboLP\Cloud\CloudBridge;
use baubolp\ryzerbe\lobbycore\animation\AnimationProvider;
use baubolp\ryzerbe\lobbycore\animation\type\NavigatorTeleportAnimation;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use baubolp\ryzerbe\lobbycore\provider\WarpProvider;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use function array_keys;
use function in_array;

class NavigatorForm {
    public static array $games = [];

    public static function open(Player $player){
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if($lobbyPlayer === null) return;
        $games = array_keys(self::$games);
        $form = new SimpleForm(function(Player $player, $data) use ($lobbyPlayer, $games): void{
            if($data === null) return;
            $data = explode(":", $data);
            $warp = WarpProvider::getWarp($data[0]);
            if($warp === null){
                if($data[0] === "TrainingLobby"){
                    $server = CloudBridge::getCloudProvider()->getRunningServersByGroup("TrainingLobby")[0] ?? null;
                    if($server === null) return;
                    $data[0] = $server;
                }
                BungeeAPI::transferPlayer($player->getName(), $data[0]);
                return;
            }
            if($lobbyPlayer->isNavigatorAnimationEnabled()){
                AnimationProvider::addActiveAnimation(new NavigatorTeleportAnimation($player, $warp, $data[1]));
            }
            else{
                $player->teleport($warp->getLocation(), $player->getYaw(), 0);
            }
        });
        $form->setTitle(TextFormat::AQUA . TextFormat::BOLD . "Games");
        foreach($games as $game){
            $icon = self::$games[$game]["icon"];
            $warpName = self::$games[$game]["warpName"] ?? self::$games[$game]["directConnect"];
            if($icon == "-1"){
                $form->addButton($game . "\n" . TextFormat::GRAY . "» " . (TextFormat::GREEN . NavigatorForm::$games[$game]["players"] ?? 0) . " Players", -1, "", $warpName . ":" . $game);
            }
            else{
                $form->addButton($game . "\n" . TextFormat::GRAY . "» " . (TextFormat::GREEN . NavigatorForm::$games[$game]["players"] ?? 0) . " Players", 1, $icon, $warpName . ":" . $game);
            }
        }
        $form->sendToPlayer($player);
    }
}