<?php


namespace baubolp\ryzerbe\lobbycore\form\profile;


use ryzerbe\core\util\async\AsyncExecutor;
use ryzerbe\core\RyZerBE;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use jojoe77777\FormAPI\SimpleForm;
use mysqli;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class ProfileOverviewForm
{

    public static function open(Player $player)
    {
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if(is_null($lobbyPlayer)) return;
        $form = new SimpleForm(function (Player $player, $data) use ($lobbyPlayer): void{
            if($data === null) return;

            switch ($data){
                case "clans":
                    $player->getServer()->dispatchCommand($player, "cui");
                    break;
                case "stats":
                    $playerName = $player->getName();
                    AsyncExecutor::submitMySQLAsyncTask("RyzerCore", function(mysqli $mysqli) use ($playerName): int{
                        $res = $mysqli->query("SELECT * FROM statstokens WHERE player='$playerName'");
                        if($res->num_rows <= 0) return 0;

                        if($data = $res->fetch_assoc()) {
                            return $data["tokens"];
                        }

                        return 0;
                    }, function(Server $server, int $tokens) use ($playerName): void{
                        $player = $server->getPlayerExact($playerName);
                        if($player === null) return;

                        $form = new SimpleForm(function(Player $player, $data): void{
                            if($data === null) return;
                            $player->getServer()->dispatchCommand($player, $data);
                        });

                        $form->setTitle(TextFormat::GOLD."Statistics");
                        $form->addButton(TextFormat::GRAY."My statistics", 1, "https://media.discordapp.net/attachments/602115215307309066/905423812433170503/statistic.png?width=327&height=327", "stats");
                        $form->addButton(TextFormat::GRAY."Statsreset-Tokens\n§8» §e".$tokens." Tokens", 1, "https://media.discordapp.net/attachments/602115215307309066/905427053057294406/1865448.png?width=410&height=410", "statsreset");
                        $form->sendToPlayer($player);
                    });
                    break;
                case "friends":
                    $player->sendMessage(RyZerBE::PREFIX.TextFormat::RED."Dieses Feature kommt in einem weiteren Update von uns!");
                    break;
                case "discord":
                    $player->getServer()->dispatchCommand($player, "verify");
                    break;
                case "party":
                    $player->getServer()->dispatchCommand($player, "p");
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
        $form->addButton(TextFormat::GOLD."Stats"."\n".TextFormat::GRAY."Touch to open", 1, "https://media.discordapp.net/attachments/602115215307309066/905423812433170503/statistic.png?width=327&height=327", "stats");
        $form->addButton(TextFormat::LIGHT_PURPLE."Network Level"."\n".TextFormat::GRAY."Touch to open", 1, "https://media.discordapp.net/attachments/412217468287713282/881161840237879336/15_Top_Player_game_competition-512.png?width=410&height=410", "network_level");
        $form->addButton(TextFormat::GREEN."Friends"."\n".TextFormat::GRAY."Touch to open", 1, "https://media.discordapp.net/attachments/412217468287713282/881164230009684029/friends.png?width=160&height=160", "friends");
        $form->addButton(TextFormat::DARK_PURPLE."Party"."\n".TextFormat::GRAY."Touch to open", 1, "https://media.discordapp.net/attachments/602115215307309066/909802469658337290/confetti-party-popper-paper-spark-512.png?width=410&height=410", "party");
        $form->addButton(TextFormat::YELLOW."Clans"."\n".TextFormat::GRAY."Touch to open", 1, "https://media.discordapp.net/attachments/412217468287713282/881162752280903750/War_Leagues.png?width=144&height=144", "clans");
        $form->addButton(TextFormat::RED."Settings"."\n".TextFormat::GRAY."Touch to open", 1, "https://media.discordapp.net/attachments/412217468287713282/881163563354443806/99-998662_customizable-services-gear-settings-icon-clipart.png?width=242&height=242", "settings");
        $form->sendToPlayer($player);
    }
}