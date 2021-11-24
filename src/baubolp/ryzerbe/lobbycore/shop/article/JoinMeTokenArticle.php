<?php

namespace baubolp\ryzerbe\lobbycore\shop\article;

use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\shop\ShopArticle;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use pocketmine\Server;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\player\RyZerPlayerProvider;
use ryzerbe\core\provider\CoinProvider;
use ryzerbe\core\RyZerBE;

class JoinMeTokenArticle extends ShopArticle {

    public function getName(): string{
        return "2x JoinMe Token";
    }

    public function getDescription(): string{
        return "§7Mit diesem §6Token §7kannst du §bJoinMe's §7in einem Spiel §aerstellen. Spieler auf dem gesamten Netzwerk werden informiert und können dir §bnachspringen§7.";
    }

    public function getPrice(): int{
        return 10000;
    }

    public function buyArticle(Player $player): void{
        $ryzerPlayer = RyzerPlayerProvider::getRyzerPlayer($player->getName());
        if($ryzerPlayer === null) return;
        if($ryzerPlayer->getCoins() < $this->getPrice()){
            $player->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer("not-enough-coins", $player->getName()));
            return;
        }
        CoinProvider::removeCoins($player->getName(), $this->getPrice());
        Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(), "joinme add " . $player->getName() . " 2");
        $player->sendMessage(RyZerBE::PREFIX . LanguageProvider::getMessageContainer("lobby-shop-article-bought", $player->getName(), ["#article" => $this->getName()]));
    }
}