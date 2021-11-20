<?php


namespace baubolp\ryzerbe\lobbycore\form;


use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use ryzerbe\core\util\async\AsyncExecutor;

class NewsBookForm
{
    /** @var string|null */
    public static $news = null;

    /**
     * @param \pocketmine\Player $player
     */
    public static function open(Player $player)
    {
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if (is_null($lobbyPlayer)) return;
        if (is_null(self::$news)) return;

        $form = new SimpleForm(function (Player $player, $data): void {
            $playerName = $player->getName();
            AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli) use ($playerName) {
                $mysqli->query("INSERT INTO `News`(`playername`) VALUES ('$playerName')");
            });
        });

        $form->setTitle(TextFormat::LIGHT_PURPLE . "News");
        $form->setContent(self::$news);
        $form->sendToPlayer($player);
    }
}