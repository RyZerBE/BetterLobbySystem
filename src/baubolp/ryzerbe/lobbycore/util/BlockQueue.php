<?php

namespace baubolp\ryzerbe\lobbycore\util;

use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\Server;

class BlockQueue {

    /** @var array  */
    private static $blocks = [];

    /**
     * @return array
     */
    public static function getBlocks(): array{
        return self::$blocks;
    }

    /**
     * @param Vector3 $vector3
     * @return bool
     */
    public static function isUsed(Vector3 $vector3): bool {
        return isset(self::$blocks[$vector3->floor()->__toString()]);
    }

    /**
     * @param Block $block
     * @param int $delay
     */
    public static function addBlock(Block $block, int $delay): void {
        if(self::isUsed($block->floor())) return;
        self::$blocks[$block->floor()->__toString()] = [
            "Block" => $block,
            "Tick" => Server::getInstance()->getTick() + $delay
        ];
    }

    /**
     * @param int $currentTick
     */
    public static function onUpdate(int $currentTick): void {
        foreach(self::$blocks as $vector => $data) {
            $tick = $data["Tick"];
            if($tick >= $currentTick) continue;
            unset(self::$blocks[$vector]);
            $block = $data["Block"];
            if(!$block instanceof Block) continue;
            $block->getLevel()->setBlockIdAt($block->x, $block->y, $block->z, $block->getId());
            $block->getLevel()->setBlockDataAt($block->x, $block->y, $block->z, $block->getDamage());
        }
    }
}