<?php


namespace baubolp\ryzerbe\lobbycore\shop\article;


use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\shop\ShopArticle;
use pocketmine\Player;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\player\RyZerPlayerProvider;
use ryzerbe\core\provider\CoinProvider;
use ryzerbe\core\rank\RankManager;

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

        $rank = RankManager::getInstance()->getRank("VIP");
        if($rank === null) return;
        if($rank->getJoinPower() <= $ryzerPlayer->getRank()->getJoinPower()) {
            $player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer('rank-higher-rankshop', $player->getName()));
            return;
        }
        if($ryzerPlayer->getCoins() < $this->getPrice()) {
            $player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("not-enough-coins", $player->getName()));
            return;
        }

        CoinProvider::removeCoins($player->getName(), $this->getPrice());
        $ryzerPlayer->setRank($rank, true, true, true);
        $player->kick("rank upgrade", false); //next fallback server...
    }
}