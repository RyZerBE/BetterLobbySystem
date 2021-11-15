<?php


namespace baubolp\ryzerbe\lobbycore\provider;


use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\util\ItemUtils;
use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\special\HeadCanonSpecialCosmetic;
use baubolp\ryzerbe\lobbycore\cosmetic\type\special\SpidermanGunSpecialCosmetic;
use baubolp\ryzerbe\lobbycore\cosmetic\type\vehicle\hypetrain\HypeTrain;
use baubolp\ryzerbe\lobbycore\form\LobbySwitcherForm;
use baubolp\ryzerbe\lobbycore\form\NavigatorForm;
use baubolp\ryzerbe\lobbycore\form\profile\ProfileOverviewForm;
use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\item\Item;
use pocketmine\level\sound\BlazeShootSound;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ItemProvider
{

    public static function giveLobbyItems(Player $player): void
    {
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if($lobbyPlayer === null) return;

        self::clearAllInventories($player);

        $navigator = ItemUtils::addItemTag(Item::get(Item::FIREWORKS)->setCustomName(TextFormat::GREEN."Navigator"."\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]"), "navigator", "lobby_item");
        $lobbySwitcher = ItemUtils::addItemTag(Item::get(-219)->setCustomName(TextFormat::GREEN."Lobbyswitcher"."\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]"), "lobbyswitcher", "lobby_item");
        $profile = ItemUtils::addItemTag(Item::get(Item::MOB_HEAD, 3)->setCustomName(TextFormat::GREEN."Profile"."\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]"), "profile", "lobby_item");
        $gadgets = ItemUtils::addItemTag(Item::get(Item::FLINT_AND_STEEL)->setCustomName(TextFormat::GREEN."Gadgets"."\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]"), "gadgets", "lobby_item");
        $shield = ItemUtils::addItemTag(Item::get(Item::SHIELD)->setCustomName(TextFormat::GREEN."Shield"."\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]"), "shield", "lobby_item");

        $inventory = $player->getInventory();
        if($player->hasPermission("lobby.shield"))
            $inventory->setItem(5, $shield);

        foreach ($lobbyPlayer->getActiveCosmetics() as $activeCosmetic) {
            if($activeCosmetic->getCategory() === CosmeticManager::CATEGORY_SPECIALS) {
                if($activeCosmetic->getIdentifier() === (new SpidermanGunSpecialCosmetic())->getIdentifier()) {
                    $inventory->setItem(0, ItemUtils::addItemTag(Item::get(Item::DIAMOND_HOE)->setCustomName(TextFormat::GOLD."Spiderman Gun"), "spiderman_gun", "lobby_item"));
                }else if($activeCosmetic->getIdentifier() === (new HeadCanonSpecialCosmetic())->getIdentifier()) {
                    $inventory->setItem(0, ItemUtils::addItemTag(Item::get(Item::SKULL, 3)->setCustomName(TextFormat::GOLD."Head Canon"), "head_canon", "hypetrain_item"));
                }
            }
        }

        $inventory->setItem(1, $lobbySwitcher);
        $inventory->setItem(2, $gadgets);
        $inventory->setItem(4, $navigator);
        $inventory->setItem(4, $navigator);
        $inventory->setItem(7, $profile);
    }

    public static function execItem(Player $player): bool
    {
        $item = $player->getInventory()->getItemInHand();
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if(is_null($lobbyPlayer)) return false;

        if($player->hasItemCooldown($item)) return true;
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
                case "spiderman_gun":
                    $nbt = new CompoundTag("", [
                        "Pos" => new ListTag("Pos", [
                            new DoubleTag("", $player->x),
                            new DoubleTag("", $player->y + $player->getEyeHeight()),
                            new DoubleTag("", $player->z)
                        ]), "Motion" => new ListTag("Motion", [
                            new DoubleTag("", $player->getDirectionVector()->x),
                            new DoubleTag("", $player->getDirectionVector()->y),
                            new DoubleTag("", $player->getDirectionVector()->z)
                        ]), "Rotation" => new ListTag("Rotation", [
                            new FloatTag("", $player->yaw),
                            new FloatTag("", $player->pitch)]),
                    ]);

                    $nbt->setString("identifier", (new SpidermanGunSpecialCosmetic())->getIdentifier());
                    $entity = Entity::createEntity("Snowball", $player->getLevel(), $nbt, $player);
                    $entity->spawnToAll();
                    if ($entity instanceof Projectile)
                        $entity->setMotion($entity->getMotion()->multiply(2.0));

                    $player->playSound("mob.spider.say", 5.0, 1.0, [$player]);
                    $player->resetItemCooldown($item, 60);
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