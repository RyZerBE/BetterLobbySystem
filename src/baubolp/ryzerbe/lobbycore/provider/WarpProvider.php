<?php

namespace baubolp\ryzerbe\lobbycore\provider;

use baubolp\ryzerbe\lobbycore\util\Warp;
use pocketmine\level\Location;
use pocketmine\utils\Config;
use ryzerbe\core\util\LocationUtils;

class WarpProvider {
    /** @var Warp[] */
    public static $warps = [];

    /**
     * @return Warp[]
     */
    public static function getWarps(): array{
        return self::$warps;
    }

    public static function loadWarps(): void{
        $config = new Config("/root/RyzerCloud/data/Lobby/config.json");
        $savedWarps = $config->get("warps");
        foreach($savedWarps as $savedWarp) self::addWarp(WarpProvider::readWarpString($savedWarp));
    }

    /**
     * @param Warp $warp
     */
    public static function addWarp(Warp $warp){
        self::$warps[strtolower($warp->getName())] = $warp;
    }

    /**
     * @param string $warpString
     * @return Warp|null
     */
    public static function readWarpString(string $warpString): ?Warp{
        $i = explode(";", $warpString);
        if(empty($i[0]) || empty($i[1])) return null;
        return new Warp($i[0], LocationUtils::fromString($i[1]));
    }

    /**
     * @param string $name
     * @return Warp|null
     */
    public static function getWarp(string $name): ?Warp{
        return self::$warps[strtolower($name)] ?? null;
    }

    /**
     * @param string $name
     */
    public static function removeWarp(string $name){
        unset(self::$warps[$name]);
    }

    /**
     * @param string $name
     * @param Location $location
     * @return bool
     */
    public static function createWarp(string $name, Location $location): bool{
        $warp = new Warp($name, $location);
        self::addWarp($warp);
        return $warp->save();
    }
}