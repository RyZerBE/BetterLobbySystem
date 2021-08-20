<?php


namespace baubolp\ryzerbe\lobbycore\form\profile;


use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class SettingsOverviewForm
{

    public static function open(Player $player)
    {
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if(is_null($lobbyPlayer)) return;
        $form = new SimpleForm(function (Player $player, $data) use ($lobbyPlayer): void{
            if($data === null) return;

            switch ($data) {
                case "language":
                    $player->getServer()->dispatchCommand($player, "language");
                    break;
                case "more_particle":
                    $player->getServer()->dispatchCommand($player, "particle");
                    break;
                case "lobby":
                    $player->sendForm(new LobbySettingsForm($lobbyPlayer));
                    break;
            }
        });

        $form->setTitle(TextFormat::GREEN.TextFormat::BOLD."Your settings");
        $form->addButton(TextFormat::AQUA."Language"."\n".TextFormat::GRAY."Touch to select", -1, "", "language");
        $form->addButton(TextFormat::YELLOW."Lobby"."\n".TextFormat::GRAY."Touch to setting", -1, "", "lobby");
        $form->addButton(TextFormat::GOLD."More Particle"."\n".TextFormat::GRAY."Touch to setting", -1, "", "more_particle");
        $form->sendToPlayer($player);
    }
}