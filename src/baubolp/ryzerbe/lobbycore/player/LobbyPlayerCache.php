<?php


namespace baubolp\ryzerbe\lobbycore\player;


use pocketmine\Player;

class LobbyPlayerCache
{
    /** @var \baubolp\ryzerbe\lobbycore\player\LobbyPlayer[]  */
    public static $players = [];

    /**
     * @return \baubolp\ryzerbe\lobbycore\player\LobbyPlayer[]
     */
    public static function getPlayers(): array
    {
        return self::$players;
    }

    /**
     * @param $player
     * @return \baubolp\ryzerbe\lobbycore\player\LobbyPlayer|null
     */
    public static function getLobbyPlayer($player): ?LobbyPlayer
    {
        if($player instanceof Player)
            $player = $player->getName();

        return self::$players[$player] ?? null;
    }

    /**
     * @param \baubolp\ryzerbe\lobbycore\player\LobbyPlayer $player
     */
    public static function registerLobbyPlayer(LobbyPlayer $player)
    {
        self::$players[$player->getPlayer()->getName()] = $player;
    }

    /**
     * @param $player
     */
    public static function unregisterLobbyPlayer($player)
    {
        if($player instanceof Player)
            $player = $player->getName();

        unset(self::$players[$player]);
    }
}