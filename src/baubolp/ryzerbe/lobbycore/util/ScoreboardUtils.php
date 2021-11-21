<?php

namespace baubolp\ryzerbe\lobbycore\util;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;

class ScoreboardUtils {
    /**
     * @param Player $player
     * @param int $score
     * @param string $line
     * @param string $identifier
     */
    public static function addLine(Player $player, int $score, string $line, string $identifier){
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $identifier;
        $entry->type = 3;
        $entry->customName = str_repeat(" ", 1) . $line . str_repeat("  ", 2);
        $entry->score = $score;
        $entry->scoreboardId = $score;
        $pk = new SetScorePacket();
        $pk->type = 0;
        $pk->entries[$score] = $entry;
        $player->sendDataPacket($pk);
    }

    /**
     * @param Player $player
     * @param int $score
     * @param string $identifier
     */
    public static function addEmptyLine(Player $player, int $score, string $identifier): void{
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $identifier;
        $entry->type = 3;
        $entry->customName = "§" . ($score % 8) . ($score > 8 ? "§" . ($score % 8) : "") . "§r";
        $entry->score = $score;
        $entry->scoreboardId = $score;
        $pk = new SetScorePacket();
        $pk->type = 0;
        $pk->entries[$score] = $entry;
        $player->sendDataPacket($pk);
    }

    /**
     * @param Player $player
     * @param string $title
     * @param string $identifier
     * @param string $slot
     * @param int $order
     */
    public static function create(Player $player, string $title, string $identifier, string $slot = "sidebar", $order = 0){
        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = $slot;
        $pk->objectiveName = $identifier;
        $pk->displayName = $title;
        $pk->criteriaName = "dummy";
        $pk->sortOrder = $order;
        $player->sendDataPacket($pk);
    }

    /**
     * @param Player $player
     * @param string $identifier
     */
    public static function remove(Player $player, string $identifier){
        $pk = new RemoveObjectivePacket();
        $pk->objectiveName = $identifier;
        $player->sendDataPacket($pk);
    }
}