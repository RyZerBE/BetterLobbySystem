<?php


namespace baubolp\ryzerbe\lobbycore\shop\article;


use baubolp\core\player\RyzerPlayerProvider;
use baubolp\core\provider\CoinProvider;
use baubolp\core\provider\LanguageProvider;
use baubolp\core\provider\RankProvider;
use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\shop\ShopArticle;
use pocketmine\Player;

class VIPRank extends ShopArticle
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return "VIP Rank";
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return "The rank have cool permissions: ...";
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return 50000;
    }

    /**
     * @param \pocketmine\Player $player
     */
    public function buyArticle(Player $player): void
    {
        $ryzerPlayer = RyzerPlayerProvider::getRyzerPlayer($player);
        if($ryzerPlayer === null) return;

        if(RankProvider::getRankJoinPower("VIP") <= RankProvider::getRankJoinPower($ryzerPlayer->getRank())) {
            $player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer('rank-higher-rankshop', $player->getName()));
            return;
        }
        if($ryzerPlayer->getCoins() < $this->getPrice()) {
            $player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("not-enough-coins", $player->getName()));
            return;
        }

        CoinProvider::removeCoins($player->getName(), $this->getPrice());
        RankProvider::setRank($player->getName(), "SHOP", "VIP");
        $player->kick("rank upgrade", false); //next fallback server...
    }
}