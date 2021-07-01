<?php


namespace baubolp\ryzerbe\lobbycore\provider;


use baubolp\core\provider\LanguageProvider;
use baubolp\core\util\ItemUtils;
use baubolp\ryzerbe\lobbycore\cosmetic\type\vehicle\hypetrain\HypeTrain;
use baubolp\ryzerbe\lobbycore\form\LobbySwitcherForm;
use baubolp\ryzerbe\lobbycore\form\NavigatorForm;
use baubolp\ryzerbe\lobbycore\form\profile\ProfileOverviewForm;
use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\item\Item;
use pocketmine\level\sound\BlazeShootSound;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ItemProvider
{

    public static function giveLobbyItems(Player $player): void
    {
        self::clearAllInventories($player);

        $navigator = ItemUtils::addItemTag(Item::get(Item::FIREWORKS)->setCustomName(TextFormat::GREEN."Navigator"."\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]"), "navigator", "lobby_item");
        $lobbySwitcher = ItemUtils::addItemTag(Item::get(-219)->setCustomName(TextFormat::GREEN."Lobbyswitcher"."\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]"), "lobbyswitcher", "lobby_item");
        $profile = ItemUtils::addItemTag(Item::get(Item::MOB_HEAD, 3)->setCustomName(TextFormat::GREEN."Profile"."\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]"), "profile", "lobby_item");
        $gadgets = ItemUtils::addItemTag(Item::get(Item::FLINT_AND_STEEL)->setCustomName(TextFormat::GREEN."Gadgets"."\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]"), "gadgets", "lobby_item");
        $shield = ItemUtils::addItemTag(Item::get(Item::SHIELD)->setCustomName(TextFormat::GREEN."Shield"."\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]"), "shield", "lobby_item");

        $inventory = $player->getInventory();
        if($player->hasPermission("lobby.shield"))
            $inventory->setItem(5, $shield);

        $inventory->setItem(1, $lobbySwitcher);
        $inventory->setItem(2, $gadgets);
        $inventory->setItem(4, $navigator);
        $inventory->setItem(4, $navigator);
        $inventory->setItem(7, $profile);
    }

    public static function execItem(Player $player): bool
    {
        $item = $player->getInventory()->getItemInHand();
        if($player->hasItemCooldown($item)) return false;
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if(is_null($lobbyPlayer)) return false;
        $player->resetItemCooldown($item, 10);

        if(ItemUtils::hasItemTag($item, "lobby_item")) {
            switch (ItemUtils::getItemTag($item, "lobby_item")) {
                case "shield":
                    if (!$lobbyPlayer->enabledShield()) {
                        $lobbyPlayer->enableShield();
                        $player->sendActionBarMessage(Loader::PREFIX . LanguageProvider::getMessageContainer('lobby-shield-activated', $player->getName()));
                    } else {
                        $lobbyPlayer->disableShield();
                        $player->sendActionBarMessage(Loader::PREFIX . LanguageProvider::getMessageContainer('lobby-shield-deactivated', $player->getName()));
                    }
                    break;
                case "profile":
                    ProfileOverviewForm::open($player);
                    break;
                case "gadgets":
                    $player->getServer()->getCommandMap()->dispatch($player, "cosmetic");
                    break;
                case "navigator":
                    NavigatorForm::open($player);
                    break;
                case "lobbyswitcher":
                    LobbySwitcherForm::open($player);
                    break;
            }
        }
        if(ItemUtils::hasItemTag($item, "hypetrain_item")) {
            switch(ItemUtils::getItemTag($item, "hypetrain_item")) {
                case "head_canon": {
                    HypeTrain::shootHead($player);
                    $player->getLevel()->addSound(new BlazeShootSound($player));
                    $player->resetItemCooldown($item, 60);
                    break;
                }
            }
        }
        return true;
    }

    public static function clearAllInventories(Player $player): void
    {
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->removeAllEffects();
        $player->getCursorInventory()->clearAll();
        $player->getCraftingGrid()->clearAll();
        $player->getOffHandInventory()->clearAll();
    }
}