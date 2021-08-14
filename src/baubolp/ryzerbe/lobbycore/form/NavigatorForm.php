<?php


namespace baubolp\ryzerbe\lobbycore\form;


use BauboLP\CloudSigns\Provider\CloudSignProvider;
use baubolp\ryzerbe\lobbycore\animation\AnimationProvider;
use baubolp\ryzerbe\lobbycore\animation\type\NavigatorTeleportAnimation;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use baubolp\ryzerbe\lobbycore\provider\WarpProvider;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class NavigatorForm
{
    /** @var array */
    public static $games = [];

    public static function open(Player $player)
    {
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if (is_null($lobbyPlayer)) return;
        $form = new SimpleForm(function (Player $player, $data) use ($lobbyPlayer): void {
            if ($data === null) return;
            $data = explode(":", $data);

            $warp = WarpProvider::getWarp($data[0]);
            if ($warp === null) return;

            if ($lobbyPlayer->isNavigatorAnimationEnabled())
                AnimationProvider::addActiveAnimation(new NavigatorTeleportAnimation($player, $warp, $data[1]));
            else
                $player->teleport($warp->getLocation());
        });

        $form->setTitle(TextFormat::AQUA . TextFormat::BOLD . "Games");
        foreach (array_keys(self::$games) as $game){
            $icon = self::$games[$game]["icon"];
            $warpName = self::$games[$game]["warpName"];

            if($icon == "-1")
                $form->addButton($game, -1, "", $warpName.":".$game);
            else{
                $form->addButton($game, 1, $icon, $warpName.":".$game);
            }
        }
        $form->sendToPlayer($player);
    }
}