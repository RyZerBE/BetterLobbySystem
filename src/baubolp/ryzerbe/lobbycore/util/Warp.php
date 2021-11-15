<?php


namespace baubolp\ryzerbe\lobbycore\util;


use ryzerbe\core\util\LocationUtils;
use pocketmine\level\Location;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Warp
{
    /** @var string */
    private $name;
    /** @var Location */
    private $location;

    public function __construct(string $name, Location $location)
    {
        $this->name = $name;
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return TextFormat::clean($this->name);
    }

    /**
     * @return \pocketmine\level\Location
     */
    public function getLocation(): \pocketmine\level\Location
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function locationToString(): string
    {
        return LocationUtils::toString($this->location);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->name.";".$this->locationToString();
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (!is_file("/root/RyzerCloud/data/Lobby/config.json")) return false;

        $config = new Config("/root/RyzerCloud/data/Lobby/config.json");
        $warps = $config->get("warps");
        $warps[] = $this->toString();
        $config->set("warps", $warps);
        return $config->save();
    }

    public function delete(): bool
    {
        if (!is_file("/root/RyzerCloud/data/Lobby/config.json")) return false;

        $config = new Config("/root/RyzerCloud/data/Lobby/config.json");
        $warps = $config->get("warps");
        unset($warps[array_search($this->toString(), $warps)]);
        $config->set("warps", $warps);
        return $config->save();
    }
}