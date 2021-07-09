<?php


namespace baubolp\ryzerbe\lobbycore\listener;


use pocketmine\event\level\ChunkLoadEvent;
use pocketmine\event\Listener;
use pocketmine\tile\Sign;

class ChunkLoaderListener implements Listener
{

    public function loadChunk(ChunkLoadEvent $event)
    {
        foreach ($event->getChunk()->getTiles() as $tile) {
            if ($tile instanceof Sign) {
                if ($tile->getText()[0] == "ClanWar Queue") {
                    $type = $tile->getText()[2] ?? null;
                    if($type === null) continue;

                    if (strtolower($type) == "elo") {
                        $tile->setText(
                            "§foO §o§4Clan§fWar §fOo",
                            "",
                            "§7[§aElo§7]",
                            "§b- §cClick to join §b-"
                        );
                    } else if (strtolower($type) == "fun") {
                        $tile->setText(
                            "§foO §o§4Clan§fWar §fOo",
                            "",
                            "§7[§aFun§7]",
                            "§b- §cClick to join §b-"
                        );
                    }
                }
            }
        }
    }
}