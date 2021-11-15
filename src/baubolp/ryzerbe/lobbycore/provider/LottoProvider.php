<?php


namespace baubolp\ryzerbe\lobbycore\provider;


use ryzerbe\core\util\async\AsyncExecutor;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayer;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class LottoProvider
{
    /**
     * @param \baubolp\ryzerbe\lobbycore\player\LobbyPlayer $player
     * @param int $count
     */
    public static function addTicket(LobbyPlayer $player, int $count = 1): void
    {
        $player->addTicket($count);
        $playerName = $player->getPlayer()->getName();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli) use ($count, $playerName){
            $mysqli->query("UPDATE LottoTickets SET tickets=tickets+'$count' WHERE playername='$playerName'");
        });
    }

    /**
     * @param \baubolp\ryzerbe\lobbycore\player\LobbyPlayer $player
     * @param int $count
     */
    public static function removeTicket(LobbyPlayer $player, int $count = 1): void
    {
        $playerName = $player->getPlayer()->getName();
        $player->removeTicket($count);
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli) use ($count, $playerName){
            $mysqli->query("UPDATE LottoTickets SET tickets=tickets-'$count' WHERE playername='$playerName'");
        });
    }


    /**
     * @return int
     */
    public static function getRandomWin(): int
    {
        $r = rand(50, 500001);
        $r = rand($r, 5000);
        $r = rand($r, 50000);
        $r = rand($r, 5000);
        $r = rand($r, 500);
        $r = rand($r, 50);
        $r = rand($r, 14);
        $r = rand($r, 23);
        $r = rand($r, 4);
        $r = rand($r, 14);
        $r = rand($r, 1);
        $r = rand($r, 60);
        $r = rand($r, 2);
        return $r;
    }

    /**
     * @param int $int
     * @return \pocketmine\item\Item
     */
    public static function getItemByInt(int $int): Item
    {
        if($int < 300) {
            return Item::get(Item::COAL)->setCustomName(TextFormat::WHITE.$int." Coins");
        }else if($int < 1000 && $int > 300) {
            return Item::get(Item::IRON_INGOT)->setCustomName(TextFormat::GRAY.$int." Coins");
        }else if($int < 5000 && $int > 1000) {
            return Item::get(Item::REDSTONE)->setCustomName(TextFormat::YELLOW.TextFormat::BOLD.$int." Coins");
        }else if($int < 10000 && $int > 5000) {
            return Item::get(Item::GOLD_INGOT)->setCustomName(TextFormat::GOLD.TextFormat::BOLD.$int." Coins");
        }else if($int > 10000 && $int < 30000) {
            return Item::get(Item::DIAMOND)->setCustomName(TextFormat::LIGHT_PURPLE.TextFormat::BOLD.$int." Coins");
        }else if($int > 30000) {
            return Item::get(Item::DIAMOND_BLOCK)->setCustomName(TextFormat::AQUA.TextFormat::BOLD.$int." Coins");
        }

        return Item::get(Item::ANVIL)->setCustomName(TextFormat::RED."ERROR!");
    }
}