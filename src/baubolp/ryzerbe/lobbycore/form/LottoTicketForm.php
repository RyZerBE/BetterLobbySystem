<?php

namespace baubolp\ryzerbe\lobbycore\form;

use baubolp\ryzerbe\lobbycore\gui\LottoGUI;
use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayer;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use baubolp\ryzerbe\lobbycore\provider\LottoProvider;
use pocketmine\form\MenuForm;
use pocketmine\form\MenuOption;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\player\RyZerPlayerProvider;
use ryzerbe\core\provider\CoinProvider;

class LottoTicketForm extends MenuForm {
    public function __construct(LobbyPlayer $player){
        $options = [];
        if($player->getTickets() > 0) $options[] =  new MenuOption(TextFormat::GOLD . $player->getTickets()."x LottoTicket\n" . TextFormat::YELLOW . "Click to play");
        $options[] = new MenuOption(TextFormat::RED . "Buy a LottoTicket\n" . TextFormat::YELLOW . "1000 Coins per ticket");
        parent::__construct(Loader::PREFIX . TextFormat::AQUA . "Lotto", LanguageProvider::getMessageContainer('lobby-lotto-dangerous', $player->getPlayer()->getName()), $options, function(Player $player, int $selectedOption): void{
            if($selectedOption == 0){
                if(($obj = RyzerPlayerProvider::getRyzerPlayer($player->getName())) != null){
                    if($obj->getCoins() < 1000){
                        $player->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer('not-enough-coins', $player->getName()));
                        return;
                    }
                    CoinProvider::removeCoins($player->getName(), 1000);
                    if(($lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player->getName())) != null){
                        LottoProvider::addTicket($lobbyPlayer);
                        $player->playSound('random.levelup', 5, 1.0, [$player]);
                        $player->sendForm(new LottoTicketForm($lobbyPlayer));
                    }
                }
            }
            else{
                if(($lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player->getName())) != null){
                    LottoProvider::removeTicket($lobbyPlayer);
                }
                (new LottoGUI($player))->send();
            }
        });
    }
}