<?php

namespace baubolp\ryzerbe\lobbycore\shop\article;

use ryzerbe\core\player\RyZerPlayerProvider;
use ryzerbe\core\provider\CoinProvider;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\RyZerBE;
use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\shop\ShopArticle;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use pocketmine\Server;

class StatsResetTokenArticle extends ShopArticle {

    /**
     * @return string
     */
    public function getName(): string{
        return "Statsreset-Token";
    }

    /**
     * @return string
     */
    public function getDescription(): string{
        return "§7Mit diesem §6Token §7kannst du deine §6Statistiken §7in einem Spiel §czurücksetzen.";
    }

    /**
     * @return int
     */
    public function getPrice(): int{
        return 5000;
    }

    public function buyArticle(Player $player): void{
        $ryzerPlayer = RyzerPlayerProvider::getRyzerPlayer($player->getName());
        if($ryzerPlayer === null) return;

        if($ryzerPlayer->getCoins() < $this->getPrice()) {
            $player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("not-enough-coins", $player->getName()));
            return;
        }

        CoinProvider::removeCoins($player->getName(), $this->getPrice());
        Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(), "statsreset add ".$player->getName()." 1");
        $player->sendMessage(Ryzer::PREFIX.LanguageProvider::getMessageContainer("lobby-shop-article-bought", $player->getName(), ["#article" => $this->getName()]));
    }
}