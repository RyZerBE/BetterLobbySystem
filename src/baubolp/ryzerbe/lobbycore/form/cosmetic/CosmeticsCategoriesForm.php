<?php

namespace baubolp\ryzerbe\lobbycore\form\cosmetic;

use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\Cosmetic;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use function array_filter;
use function count;
use function is_null;

class CosmeticsCategoriesForm {

    public static function open(Player $player): void {
        $rbePlayer = LobbyPlayerCache::getLobbyPlayer($player);
        $form = new SimpleForm(function(Player $player, $data): void {
            if($data === null) return;
            $category = CosmeticManager::getInstance()->getCategory($data);
            if(is_null($category)) return;
        });
        $form->setTitle("§lCosmetics");
        foreach(CosmeticManager::getInstance()->getCategories() as $category) {
            $unlocked = count(array_filter($rbePlayer->getActiveCosmetics(), function(Cosmetic $cosmetic) use ($category): bool {
                return $cosmetic->getCategory() === $category->getId();
            }));
            $form->addButton("§a" . $category->getName() . "\n§7" . $unlocked . "§8/§7" . count($category->getCosmetics()), $category->getIconType(), $category->getIcon(), $category->getId());
        }
        $form->sendToPlayer($player);
    }
}