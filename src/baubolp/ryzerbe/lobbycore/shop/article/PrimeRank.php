<?php

namespace baubolp\ryzerbe\lobbycore\shop\article;

use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\shop\ShopArticle;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\player\networklevel\NetworkLevelProvider;
use ryzerbe\core\player\RyZerPlayerProvider;
use ryzerbe\core\provider\ChatEmojiProvider;
use ryzerbe\core\provider\CoinProvider;
use ryzerbe\core\rank\RankManager;
use function implode;

class PrimeRank extends ShopArticle {
    /**
     * @return string
     */
    public function getName(): string{
        return "§dPrime";
    }

    /**
     * @return string
     */
    public function getDescription(): string{
        return implode("\n".TextFormat::RESET.TextFormat::DARK_GRAY."» ".TextFormat::WHITE, [
            TextFormat::RESET.TextFormat::DARK_GRAY."» ".
            "Dein Name wird ".TextFormat::LIGHT_PURPLE."pink ".TextFormat::WHITE."angezeigt.",
            "Deine Schrift im Chat ist weiß statt ".TextFormat::DARK_GRAY."grau ",
            "Du erhältst eine ".TextFormat::LIGHT_PURPLE."Prime Rolle ".TextFormat::WHITE."auf unserem ".TextFormat::AQUA."Discord",
            "Du hast Zugriff auf ".TextFormat::LIGHT_PURPLE." Prime Talks ".TextFormat::WHITE."auf unserem ".TextFormat::AQUA."Discord",
            "Du erhältst möglichen ".TextFormat::BLUE."BETA Zugang ".TextFormat::WHITE."zu Spielmodi oder zu Features",
            "Du kannst deinen Rang mit ".TextFormat::RED."/togglerank ".TextFormat::WHITE."verstecken",
            "Dein Map-Votes in ".TextFormat::DARK_AQUA."Bedwars, FlagWars & BuildFFA".TextFormat::WHITE." haben ein doppeltes Gewicht",
            "Du erhältst mehr ".TextFormat::YELLOW."Coins ".TextFormat::WHITE."in der ".TextFormat::DARK_GREEN."täglichen Belohnung",
            "Dein ".TextFormat::AQUA.TextFormat::BOLD."GG".TextFormat::RESET.TextFormat::WHITE." nach einer Runde wird ".TextFormat::GOLD.TextFormat::BOLD."SPECIAL",
            "Du kannst coole ".TextFormat::YELLOW."Chat Emojis ".TextFormat::GREEN."nutzen ",
            "Du kannst ".TextFormat::DARK_PURPLE." 2 ".TextFormat::AQUA." CWBW-Training Szenarien ".TextFormat::WHITE." rausvoten",
            "Du kannst ".TextFormat::RED."volle Runden ".TextFormat::GREEN." betreten. ".TextFormat::WHITE."Aber nur, wenn Spieler mit einem schlechteren Rang als §dPrime ".TextFormat::WHITE."in der Runde sind!",
            "Du kannst gratis ".TextFormat::GOLD."Clans ".TextFormat::GREEN."erstellen",
            "Du kannst in der ".TextFormat::YELLOW."Lobby ".TextFormat::GREEN."fliegen ".TextFormat::WHITE."(/fly)",
            "Du kannst deinen §3Bedwars Shop Namen §abeliebig betiteln §fund §dg§ee§bs§ct§aa§fl§6t§9e§en",
            //"Du kannst doppelt so viele Freunde haben",
            //"Du kannst einen Status im Freundesystem setzen",
            "",
            //"Das Kit ".TextFormat::AQUA."Sprengmeister".TextFormat::WHITE." in ".TextFormat::DARK_AQUA."FlagWars ".TextFormat::WHITE."bekommst du geschenkt",
            "Ein Rangkauf wirkt sich ".TextFormat::GREEN."positiv ".TextFormat::WHITE."auf dein Netzwerklevel aus. Du erhältst ".TextFormat::AQUA."300 XP Punkte",
            "",
            TextFormat::BOLD.TextFormat::RED."ACHTUNG: ".TextFormat::YELLOW."Der Rang ist nur für 90 Tage in deinem Besitz!"
        ]);
    }

    /**
     * @param Player $player
     */
    public function buyArticle(Player $player): void{
        $ryzerPlayer = RyzerPlayerProvider::getRyzerPlayer($player);
        if($ryzerPlayer === null) return;
        $rank = RankManager::getInstance()->getRank("Prime");
        if($rank === null) return;
        if($rank->getJoinPower() <= $ryzerPlayer->getRank()->getJoinPower()){
            $player->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer('rank-higher-rankshop', $player->getName()));
            return;
        }
        if($ryzerPlayer->getCoins() < $this->getPrice()){
            $player->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer("not-enough-coins", $player->getName()));
            return;
        }
        CoinProvider::removeCoins($player->getName(), $this->getPrice());
        $ryzerPlayer->getNetworkLevel()->addXP(300, null);
        $ryzerPlayer->setRank($rank, true, true, true, (new \DateTime())->add(new \DateInterval("P3M")));
    }

    /**
     * @return int
     */
    public function getPrice(): int{
        return 150000;
    }
}