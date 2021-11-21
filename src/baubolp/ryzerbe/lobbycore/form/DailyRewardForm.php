<?php

namespace baubolp\ryzerbe\lobbycore\form;

use baubolp\ryzerbe\lobbycore\player\LobbyPlayer;
use baubolp\ryzerbe\lobbycore\provider\LottoProvider;
use pocketmine\form\FormIcon;
use pocketmine\form\MenuForm;
use pocketmine\form\MenuOption;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use ryzerbe\core\provider\CoinProvider;

class DailyRewardForm extends MenuForm {
    public function __construct(LobbyPlayer $lobbyPlayer){
        $options = [];
        if($lobbyPlayer->getDailyCoinTime() < time()){
            $options[] = new MenuOption(TextFormat::GREEN . "200 Coins" . "\n" . TextFormat::GRAY . "[" . TextFormat::YELLOW . "Click to get" . TextFormat::GRAY . "]", new FormIcon("textures/ui/MCoin", FormIcon::IMAGE_TYPE_PATH));
        }
        else{
            $options[] = new MenuOption(TextFormat::GREEN . "200 Coins" . "\n" . TextFormat::GRAY . "[" . TextFormat::RED . "You have to wait" . TextFormat::GRAY . "]", new FormIcon("textures/ui/MCoin", FormIcon::IMAGE_TYPE_PATH));
        }
        if($lobbyPlayer->getDailyLottoTicketTime() < time()){
            $options[] = new MenuOption(TextFormat::GREEN . "1x LottoTicket" . "\n" . TextFormat::GRAY . "[" . TextFormat::YELLOW . "Click to get" . TextFormat::GRAY . "]", new FormIcon("textures/items/map_mansion", FormIcon::IMAGE_TYPE_PATH));
        }
        else{
            $options[] = new MenuOption(TextFormat::GREEN . "1x LottoTicket" . "\n" . TextFormat::GRAY . "[" . TextFormat::RED . "You have to wait" . TextFormat::GRAY . "]", new FormIcon("textures/items/map_mansion", FormIcon::IMAGE_TYPE_PATH));
        }
        if($lobbyPlayer->getDailyXPTime() < time()){
            $options[] = new MenuOption(TextFormat::GREEN . "10 XP" . "\n" . TextFormat::GRAY . "[" . TextFormat::YELLOW . "Click to get" . TextFormat::GRAY . "]", new FormIcon("textures/items/experience_bottle", FormIcon::IMAGE_TYPE_PATH));
        }
        else{
            $options[] = new MenuOption(TextFormat::GREEN . "10 XP" . "\n" . TextFormat::GRAY . "[" . TextFormat::RED . "You have to wait" . TextFormat::GRAY . "]", new FormIcon("textures/items/experience_bottle", FormIcon::IMAGE_TYPE_PATH));
        }
        if($lobbyPlayer->getPlayer()->hasPermission("lobby.coinbomb")){
            if($lobbyPlayer->getDailyCoinBombTime() < time()){
                $options[] = new MenuOption(TextFormat::GREEN . "1x CoinBomb" . "\n" . TextFormat::GRAY . "[" . TextFormat::YELLOW . "Click to get" . TextFormat::GRAY . "]", new FormIcon("textures/items/gold_nugget", FormIcon::IMAGE_TYPE_PATH));
            }
            else{
                $options[] = new MenuOption(TextFormat::GREEN . "1x Coinbomb" . "\n" . TextFormat::GRAY . "[" . TextFormat::RED . "You have to wait" . TextFormat::GRAY . "]", new FormIcon("textures/items/gold_nugget", FormIcon::IMAGE_TYPE_PATH));
            }
        }
        else{
            $options[] = new MenuOption(TextFormat::GREEN . "1x HypeTrain" . "\n" . TextFormat::GRAY . "[" . TextFormat::DARK_PURPLE . "YouTuber" . TextFormat::GRAY . "]", new FormIcon("textures/items/minecart_normal", FormIcon::IMAGE_TYPE_PATH));
        }
        if($lobbyPlayer->getPlayer()->hasPermission("lobby.hypetrain")){
            if($lobbyPlayer->getDailyHypeTrainTime() < time()){
                $options[] = new MenuOption(TextFormat::GREEN . "1x HypeTrain" . "\n" . TextFormat::GRAY . "[" . TextFormat::YELLOW . "Click to get" . TextFormat::GRAY . "]", new FormIcon("textures/items/minecart_normal", FormIcon::IMAGE_TYPE_PATH));
            }
            else{
                $options[] = new MenuOption(TextFormat::GREEN . "1x HypeTrain" . "\n" . TextFormat::GRAY . "[" . TextFormat::RED . "You have to wait" . TextFormat::GRAY . "]", new FormIcon("textures/items/minecart_normal", FormIcon::IMAGE_TYPE_PATH));
            }
        }
        else{
            $options[] = new MenuOption(TextFormat::GREEN . "1x HypeTrain" . "\n" . TextFormat::GRAY . "[" . TextFormat::DARK_PURPLE . "YouTuber" . TextFormat::GRAY . "]", new FormIcon("textures/items/minecart_normal", FormIcon::IMAGE_TYPE_PATH));
        }
        $options[] = new MenuOption(TextFormat::GREEN . "Your Loginstreak: " . TextFormat::AQUA . $lobbyPlayer->getLoginStreak() . "\n" . TextFormat::GRAY . "[" . TextFormat::RED . date("d.m.Y", $lobbyPlayer->getNextLoginStreak()) . TextFormat::GRAY . "]", new FormIcon("textures/ui/feedIcon", FormIcon::IMAGE_TYPE_PATH));
        parent::__construct(TextFormat::AQUA . "Daily Reward", "", $options, function(Player $player, int $selectedOption) use ($lobbyPlayer): void{
            switch($selectedOption){
                case 0:
                    if($lobbyPlayer->getDailyCoinTime() < time()){
                        CoinProvider::addCoins($player->getName(), 200);
                        $lobbyPlayer->setDailyCoinTime($lobbyPlayer->getNextLoginStreak());
                        $player->playSound("random.levelup", 5.0, 1.0, [$player]);
                        $player->sendForm(new DailyRewardForm($lobbyPlayer));
                    }
                    else{
                        $player->playSound("note.bass", 5.0, 1.0, [$player]);
                    }
                    break;
                case 2:
                    if($lobbyPlayer->getDailyXPTime() < time()){
                        $lobbyPlayer->asRyZerPlayer()->getNetworkLevel()->addXP(10, null);
                        $lobbyPlayer->setDailyXPTime($lobbyPlayer->getNextLoginStreak());
                        $player->playSound("random.levelup", 5.0, 1.0, [$player]);
                        $player->sendForm(new DailyRewardForm($lobbyPlayer));
                    }
                    else{
                        $player->playSound("note.bass", 5.0, 1.0, [$player]);
                    }
                    break;
                case 1:
                    if($lobbyPlayer->getDailyLottoTicketTime() < time()){
                        LottoProvider::addTicket($lobbyPlayer);
                        $lobbyPlayer->setDailyLottoTicketTime($lobbyPlayer->getNextLoginStreak());
                        $player->playSound("random.levelup", 5.0, 1.0, [$player]);
                        $player->sendForm(new DailyRewardForm($lobbyPlayer));
                    }
                    else{
                        $player->playSound("note.bass", 5.0, 1.0, [$player]);
                    }
                    break;
                case 3:
                    if($lobbyPlayer->getDailyCoinBombTime() < time() && $player->hasPermission("lobby.coinbomb")){
                        $lobbyPlayer->addCoinbomb();
                        $lobbyPlayer->setDailyCoinBombTime($lobbyPlayer->getNextLoginStreak());
                        $player->playSound("random.levelup", 5.0, 1.0, [$player]);
                        $player->sendForm(new DailyRewardForm($lobbyPlayer));
                    }
                    else{
                        $player->playSound("note.bass", 5.0, 1.0, [$player]);
                    }
                    break;
                case 4:
                    if($lobbyPlayer->getDailyHypeTrainTime() < time() && $player->hasPermission("lobby.hypetrain")){
                        $lobbyPlayer->addHypeTrains();
                        $lobbyPlayer->setDailyHypeTrainTime($lobbyPlayer->getNextLoginStreak());
                        $player->playSound("random.levelup", 5.0, 1.0, [$player]);
                        $player->sendForm(new DailyRewardForm($lobbyPlayer));
                    }
                    else{
                        $player->playSound("note.bass", 5.0, 1.0, [$player]);
                    }
                    break;
            }
        });
    }
}