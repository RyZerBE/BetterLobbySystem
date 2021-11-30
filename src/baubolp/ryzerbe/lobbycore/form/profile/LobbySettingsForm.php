<?php

namespace baubolp\ryzerbe\lobbycore\form\profile;

use baubolp\ryzerbe\lobbycore\player\LobbyPlayer;
use pocketmine\form\CustomForm;
use pocketmine\form\CustomFormResponse;
use pocketmine\form\element\Input;
use pocketmine\form\element\Toggle;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use function strlen;

class LobbySettingsForm extends CustomForm {
    public function __construct(LobbyPlayer $lobbyPlayer){
        $elements = [
            new Toggle("joinanimation", TextFormat::GREEN . "▷ " . TextFormat::RED . "Join-Animation", $lobbyPlayer->isJoinAnimationEnabled()),
            new Toggle("afkanimation", TextFormat::GREEN . "▷ " . TextFormat::RED . "AFK-Animation", $lobbyPlayer->isAFKAnimationEnabled()),
            new Toggle("navigatoranimation", TextFormat::GREEN . "▷ " . TextFormat::RED . "Navigator-Animation", $lobbyPlayer->isNavigatorAnimationEnabled()),
            new Toggle("doublejump", TextFormat::GREEN . "▷ " . TextFormat::RED . "Doublejump", $lobbyPlayer->isDoubleJumpEnabled()),
            new Toggle("spawn", TextFormat::GREEN . "▷ " . TextFormat::RED . "Spawn on your last position in the lobby", $lobbyPlayer->isLastPositionSpawnEnabled()),
            new Toggle("overview", TextFormat::GREEN . "▷ " . TextFormat::RED . "Interact a player to get a quick overview", $lobbyPlayer->isQuickPlayerOverview())
        ];

        if($lobbyPlayer->getPlayer()->hasPermission("lobby.status")) $elements[] = new Input("status", TextFormat::RED."Your status (under nametag)", "", ""); //todo: default -> current status
        parent::__construct(TextFormat::YELLOW . TextFormat::BOLD . "Lobby " . TextFormat::RED . "Settings", $elements, function(Player $player, CustomFormResponse $response) use ($lobbyPlayer): void{
            $e1 = $this->getElement(0);
            $e2 = $this->getElement(1);
            $e3 = $this->getElement(2);
            $e4 = $this->getElement(3);
            $e5 = $this->getElement(4);
            $e6 = $this->getElement(5);
            $e7 = $this->getElement(6);
            $joinAnimation = $response->getBool($e1->getName());
            $afkAnimation = $response->getBool($e2->getName());
            $navigatorAnimation = $response->getBool($e3->getName());
            $doubleJump = $response->getBool($e4->getName());
            $lastPositionSpawn = $response->getBool($e5->getName());
            $quickInteract = $response->getBool($e6->getName());
            $lobbyPlayer->updateLobbySettings([
                $joinAnimation,
                $afkAnimation,
                $navigatorAnimation,
                $doubleJump,
                $lastPositionSpawn,
                $quickInteract
            ]);

            if($player->hasPermission("lobby.status")) {
                $status = $response->getString($e7->getName());
                if(strlen($status) <= 0) $status = "reset";

                $player->getServer()->dispatchCommand($player, "status ".$status);
            }
        });
    }
}