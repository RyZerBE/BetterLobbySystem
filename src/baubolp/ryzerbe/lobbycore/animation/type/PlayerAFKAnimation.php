<?php

namespace baubolp\ryzerbe\lobbycore\animation\type;

use baubolp\ryzerbe\lobbycore\animation\Animation;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\Player;
use function array_rand;
use function floor;
use function is_null;

class PlayerAFKAnimation extends Animation {
    private Player $player;

    private array $titles = [
        [
            "§fRyzer§cBE", "§f_yzer§cBE", "§f__zer§cBE", "§f___er§cBE", "§f____r§cBE", "§f_____§cBE", "§f_____§c_E",
            "§f_____§c__", "§f_____§c_E", "§f_____§cBE", "§f____r§cBE", "§f___er§cBE", "§f__zer§cBE", "§f_yzer§cBE", "§fRyzer§cBE",
        ], [
            "§fRyzer§cBE", "§l§fRyzer§cBE", "§fRyzer§cBE", "§l§fRyzer§cBE", "§fRyzer§cBE", "§l§fRyzer§cBE", "§fRyzer§cBE", "§l§fRyzer§cBE"
        ], [
            "§f§lR§r§fyzer§cBE", "§fR§ly§r§fzer§cBE", "§fRy§lz§r§fer§cBE", "§fRyz§le§r§fr§cBE", "§fRyze§lr§r§cBE", "§fRyzer§c§lB§r§cE", "§fRyzer§cB§lE", "§fRyzer§cBE"
        ], [
            "§f§kRyzer§cBE", "§fR§kyzer§cBE", "§fRy§kzer§cBE", "§fRyz§ker§cBE", "§fRyze§kr§cBE", "§fRyzer§c§kBE", "§fRyzer§cB§kE", "§fRyzer§cBE"
        ], [
            "§f§6R§r§fyzer§cBE", "§fR§6y§r§fzer§cBE", "§fRy§6z§r§fer§cBE", "§fRyz§6e§r§fr§cBE", "§fRyze§6r§r§cBE", "§fRyzer§c§6B§r§cE", "§fRyzer§cB§6E", "§fRyzer§cBE"
        ]//todo: add more
    ];

    /** @var int  */
    private $currentTitle;
    /** @var int  */
    private $currentTitleKey = 0;

    /**
     * PlayerAFKAnimation constructor.
     * @param Player $player
     */
    public function __construct(Player $player){
        $this->player = $player;
        $this->currentTitle = array_rand($this->titles);
        parent::__construct();
    }

    public function tick(): void{
        parent::tick();

        $player = $this->player;
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if(!$player->isConnected() || is_null($lobbyPlayer)) {
            $this->stop();
            return;
        }
        if(!$lobbyPlayer->isAfk()) {
            $this->stop();
            $player->sendTitle("§fWelcome back!");
            return;
        }
        $afkTime = floor($lobbyPlayer->getAfkTicks() / 20);
        if($afkTime <= 0) return;

        $seconds = $afkTime % 60;
        $minutes = floor($afkTime / 60) % 60;
        $hours = floor(floor($afkTime / 60) / 60);

        $afkTip = "";
        if($hours > 0) $afkTip .= $hours." Hour".((int)$hours === 1 ? "" : "s").", ";
        if($minutes > 0) $afkTip .= $minutes." Minute".((int)$minutes === 1 ? "" : "s").", ";
        $afkTip .= $seconds." Seconds";

        $player->sendTip("§r§aAfk Time §8§l» §r§a" . $afkTip);

        if($this->getCurrentTick() % 10 !== 0) return;
        $title = $this->titles[$this->currentTitle][$this->currentTitleKey++];
        $player->sendTitle($title, "", 0, 20, 0);

        if(!isset($this->titles[$this->currentTitle][$this->currentTitleKey])) {
            $this->currentTitle = array_rand($this->titles);
            $this->currentTitleKey = 0;
        }
    }
}