<?php


namespace baubolp\ryzerbe\lobbycore\form;

use BauboLP\Cloud\Bungee\BungeeAPI;
use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Provider\CloudProvider;
use baubolp\core\provider\AsyncExecutor;
use baubolp\core\Ryzer;
use baubolp\core\util\query\ServerQuery;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use function exec;

class LobbySwitcherForm
{

    public static function open(Player $player)
    {
        $servers = CloudBridge::getCloudProvider()->getRunningServersByGroup("Lobby");
        $ports = [];
        foreach($servers as $server) {
            $ports[$server] = CloudBridge::getCloudProvider()->getServerPort($server);
        }
        $playername = $player->getName();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function() use ($ports): array {
            $servers = [];
            $address = exec("ifconfig | grep -Eo 'inet (addr:)?([0-9]*\.){3}[0-9]*' | grep -Eo '([0-9]*\.){3}[0-9]*' | grep -v '127.0.0.1'");
            foreach($ports as $server => $port) {
                if($port === null) continue;
                $result = (new ServerQuery($address, $port))->run();
                if($result === null) continue;
                $servers[$server] = $result->getOnlinePlayers();
            }
            return $servers;
        }, function(Server $server, array $servers) use ($playername): void {
            $player = $server->getPlayerExact($playername);
            if($player === null) return;

            $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
            if ($lobbyPlayer === null) return;
            $form = new SimpleForm(function (Player $player, $data) use ($lobbyPlayer): void {
                if ($data === null) return;
                if($data === "start") {
                    CloudBridge::getCloudProvider()->startServer("Lobby", 1);
                    $player->sendMessage(Ryzer::PREFIX.TextFormat::GREEN."Starte neue Lobby...");
                    return;
                }
                BungeeAPI::transferPlayer($player->getName(), $data);
            });

            $form->setTitle(TextFormat::RED.TextFormat::BOLD."LobbySwitcher");
            foreach ($servers as $server => $onlinePlayers) {
                if(CloudProvider::getServer() === $server){
                    $form->addButton(TextFormat::RED.$server.TextFormat::DARK_GRAY." [".TextFormat::GRAY.$onlinePlayers.TextFormat::DARK_GRAY." Online]"."\n".TextFormat::DARK_GRAY."(".TextFormat::GREEN."Your lobby".TextFormat::DARK_GRAY.")", 0, "textures/ui/world_glyph_color", $server);
                }else{
                    $form->addButton(TextFormat::GREEN.$server.TextFormat::DARK_GRAY." [".TextFormat::GRAY.$onlinePlayers.TextFormat::DARK_GRAY." Online]"."\n".TextFormat::DARK_GRAY."(".TextFormat::RED."Click to connect".TextFormat::DARK_GRAY.")", 0, "textures/ui/world_glyph_color", $server);
                }
            }
            if($player->hasPermission("cloud.admin")) $form->addButton(TextFormat::GREEN."Start new lobby", 0, "textures/ui/servers", "start");
            $form->sendToPlayer($player);
        });
    }
}