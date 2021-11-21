<?php

namespace baubolp\ryzerbe\lobbycore\command;

use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\util\ItemUtils;

class HypeTrainCommand extends Command {
    /**
     * HypeTrainCommand constructor.
     */
    public function __construct(){
        parent::__construct("hypetrain", "HypeTrain Command");
        $this->setPermission("lobby.hypetrain");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     * @return mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!$sender instanceof Player) return;
        if(!$this->testPermission($sender)) return;
        $player = LobbyPlayerCache::getLobbyPlayer($sender);
        if($player === null) return;
        if($player->getHypeTrains() <= 0){
            $sender->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer("lobby-no-hype-trains", $sender->getName()));
            return;
        }
        $item = Item::get(Item::MINECART)->setCustomName(TextFormat::GOLD . "Hype Train");
        ItemUtils::addItemTag($item, "tag_hypetrain", "lobby_item");
        $sender->getInventory()->addItem($item);
        $sender->playSound("random.orb", 5.0, 1.0, [$sender]);
    }
}