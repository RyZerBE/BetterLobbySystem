<?php


namespace baubolp\ryzerbe\lobbycore\form;


use BauboLP\Cloud\Bungee\BungeeAPI;
use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Provider\CloudProvider;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class LobbySwitcherForm
{

    public static function open(Player $player)
    {
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if (is_null($lobbyPlayer)) return;
        $form = new SimpleForm(function (Player $player, $data) use ($lobbyPlayer): void {
            if ($data === null) return;

            BungeeAPI::transferPlayer($player->getName(), $data);
        });

        $form->setTitle(TextFormat::RED.TextFormat::BOLD."LobbySwitcher");
        foreach (CloudBridge::getCloudProvider()->getRunningServersByGroup("Lobby") as $server) {
            if(CloudProvider::getServer() === $server)
                $form->addButton(TextFormat::RED.$server."\n".TextFormat::DARK_GRAY."(".TextFormat::GREEN."Your lobby".TextFormat::DARK_GRAY.")", -1, "", $server);
            else
                $form->addButton(TextFormat::GREEN.$server."\n".TextFormat::DARK_GRAY."(".TextFormat::RED."Click to connect".TextFormat::DARK_GRAY.")", -1, "", $server);
        }

        $form->sendToPlayer($player);
    }
}