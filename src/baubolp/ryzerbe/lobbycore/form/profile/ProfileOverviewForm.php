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
                    $player->sendMessage(Ryzer::PREFIX.TextFormat::RED."Dieses Feature kommt in einem weiteren Update von uns!");
                    break;
                case "discord":
                    $player->getServer()->dispatchCommand($player, "verify");
                    break;
                case "network_level":
                    $player->getServer()->dispatchCommand($player, "networklevel");
                    break;
                case "settings":
                    SettingsOverviewForm::open($player);
                    break;
            }
        });

        $form->setTitle(TextFormat::GREEN.TextFormat::BOLD."Your profile");
        $form->addButton(TextFormat::AQUA."Discord Verification"."\n".TextFormat::GRAY."Touch to open", 1, "https://media.discordapp.net/attachments/412217468287713282/881160991809876019/discord-2474808-2056094.png?width=410&height=410", "discord");
        $form->addButton(TextFormat::RED."Settings"."\n".TextFormat::GRAY."Touch to open", 1, "https://media.discordapp.net/attachments/412217468287713282/881163563354443806/99-998662_customizable-services-gear-settings-icon-clipart.png?width=242&height=242", "settings");
        $form->addButton(TextFormat::LIGHT_PURPLE."Network Level"."\n".TextFormat::GRAY."Touch to open", 1, "https://media.discordapp.net/attachments/412217468287713282/881161840237879336/15_Top_Player_game_competition-512.png?width=410&height=410", "network_level");
        $form->addButton(TextFormat::GREEN."Friends"."\n".TextFormat::GRAY."Touch to open", -1, "https://media.discordapp.net/attachments/412217468287713282/881164230009684029/friends.png?width=160&height=160", "friends");
        $form->addButton(TextFormat::YELLOW."Clans"."\n".TextFormat::GRAY."Touch to open", 1, "https://media.discordapp.net/attachments/412217468287713282/881162752280903750/War_Leagues.png?width=144&height=144", "clans");
        $form->sendToPlayer($player);
    }
}