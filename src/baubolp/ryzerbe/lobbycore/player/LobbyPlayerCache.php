<?php

namespace baubolp\ryzerbe\lobbycore\player;

use pocketmine\Player;

class LobbyPlayerCache {
    /** @var LobbyPlayer[] */
    public static $players = [];

    /**
     * @return LobbyPlayer[]
     */
    public static function getPlayers(): array{
        return self::$players;
    }

    /**
     * @param $player
     * @return LobbyPlayer|null
     */
    public static function getLobbyPlayer($player): ?LobbyPlayer{
        if($player instanceof Player){
            $player = $player->getName();
        }
        return self::$players[$player] ?? null;
    }

    /**
     * @param LobbyPlayer $player
     */
    public static function registerLobbyPlayer(LobbyPlayer $player){
        self::$players[$player->getPlayer()->getName()] = $player;
    }

    /**
     * @param $player
     */
    public static function unregisterLobbyPlayer($player){
        if($player instanceof Player){
            $player = $player->getName();
        }
        unset(self::$players[$player]);
    }
}