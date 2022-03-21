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
use function array_filter;

class CoinbombCommand extends Command {
    public function __construct(){
        parent::__construct("coinbomb", "Get your coinbomb", "", []);
        $this->setPermission("lobby.coinbomb");
        $this->setPermissionMessage(Loader::PREFIX . TextFormat::RED . "No Permissions!");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player) return;
        if(!$this->testPermission($sender)) return;
        $player = LobbyPlayerCache::getLobbyPlayer($sender);
        if($player === null) return;
        $count = 0;
        foreach(array_filter($sender->getInventory()->getContents(), function(Item $item): bool {
            return !$item->isNull() && ItemUtils::hasItemTag($item, "lobby_item") && ItemUtils::getItemTag($item, "lobby_item") === "tag_coinbomb";
        }) as $item) $count += $item->getCount();
        if($player->getCoinBombs() <= 0 || $count >= $player->getCoinBombs()){
            $sender->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer('lobby-no-coin-bombs', $sender->getName()));
            return;
        }
        $item = Item::get(Item::GOLD_NUGGET)->setCustomName(TextFormat::GOLD . "Coinbomb");
        ItemUtils::addItemTag($item, "tag_coinbomb", "lobby_item");
        $sender->getInventory()->addItem($item);
        $sender->playSound("random.orb", 5.0, 1.0, [$sender]);
    }
}