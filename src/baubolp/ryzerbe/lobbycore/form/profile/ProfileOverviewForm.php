<?php


namespace baubolp\ryzerbe\lobbycore\form\profile;


use baubolp\core\Ryzer;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ProfileOverviewForm
{

    public static function open(Player $player)
    {
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if(is_null($lobbyPlayer)) return;
        $form = new SimpleForm(function (Player $player, $data) use ($lobbyPlayer): void{
            if($data === null) return;

            switch ($data) {
                case "clans":
                    $player->getServer()->dispatchCommand($player, "cui");
                    break;
                case "friends":
                    $player->sendMessage(Ryzer::PREFIX.TextFormat::RED."Soon! :(");
                    break;
                case "discord":
                    $player->getServer()->dispatchCommand($player, "verify");
                    break;
                case "settings":
                    SettingsOverviewForm::open($player);
                    break;
            }
        });

        $form->setTitle(TextFormat::GREEN.TextFormat::BOLD."Your profile");
        $form->addButton(TextFormat::AQUA."Discord Verification"."\n".TextFormat::GRAY."Touch to open", -1, "", "discord");
        $form->addButton(TextFormat::RED."Settings"."\n".TextFormat::GRAY."Touch to open", -1, "", "settings");
        $form->addButton(TextFormat::GREEN."Friends"."\n".TextFormat::GRAY."Touch to open", -1, "", "friends");
        $form->addButton(TextFormat::YELLOW."Clans"."\n".TextFormat::GRAY."Touch to open", -1, "", "clans");
        $form->sendToPlayer($player);
    }
}