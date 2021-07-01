<?php


namespace baubolp\ryzerbe\lobbycore\provider;


use baubolp\core\util\LocationUtils;
use baubolp\ryzerbe\lobbycore\util\Warp;
use pocketmine\level\Location;
use pocketmine\utils\Config;

class WarpProvider
{
    /** @var \baubolp\ryzerbe\lobbycore\util\Warp[] */
    public static $warps = [];

    /**
     * @return \baubolp\ryzerbe\lobbycore\util\Warp[]
     */
    public static function getWarps(): array
    {
        return self::$warps;
    }

    public static function loadWarps(): void
    {
        $config = new Config("/root/RyzerCloud/data/Lobby/config.json");
        $savedWarps = $config->get("warps");

        foreach ($savedWarps as $savedWarp)
            self::addWarp(WarpProvider::readWarpString($savedWarp));
    }

    /**
     * @param \baubolp\ryzerbe\lobbycore\util\Warp[] $warps
     */
    public static function setWarps(array $warps): void
    {
        self::$warps = $warps;
    }

    /**
     * @param \baubolp\ryzerbe\lobbycore\util\Warp $warp
     */
    public static function addWarp(Warp $warp)
    {
        self::$warps[$warp->getName()] = $warp;
    }

    /**
     * @param string $name
     */
    public static function removeWarp(string $name)
    {
        unset(self::$warps[$name]);
    }

    /**
     * @param string $warpString
     * @return \baubolp\ryzerbe\lobbycore\util\Warp|null
     */
    public static function readWarpString(string $warpString): ?Warp
    {
        $i = explode(";", $warpString);

        if(empty($i[0]) || empty($i[1])) return null;
        return new Warp($i[0], LocationUtils::fromString($i[1]));
    }

    /**
     * @param string $name
     * @param \pocketmine\level\Location $location
     * @return bool
     */
    public static function createWarp(string $name, Location $location): bool
    {
        $warp = new Warp($name, $location);
        self::addWarp($warp);
        return $warp->save();
    }
}