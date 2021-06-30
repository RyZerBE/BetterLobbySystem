<?php

namespace baubolp\ryzerbe\lobbycore\form\cosmetic;

use baubolp\ryzerbe\lobbycore\cosmetic\category\CosmeticCategory;
use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use function is_null;

class CosmeticsOverviewForm {

    /**
     * @param Player $player
     * @param CosmeticCategory $category
     */
    public static function open(Player $player, CosmeticCategory $category): void {
        $rbePlayer = LobbyPlayerCache::getLobbyPlayer($player);
        $form = new SimpleForm(function(Player $player, $data) use ($rbePlayer): void {
            if($data === null) return;
            switch($data) {
                case "back": {
                    CosmeticsCategoriesForm::open($player);
                    break;
                }
                default: {
                    $cosmetic = CosmeticManager::getInstance()->getCosmetic($data);
                    if($cosmetic === null) return;
                    //Mhhhhh - Not that good
                    if($rbePlayer->isCosmeticActivated($cosmetic)) {
                        //Nothing
                    } elseif($rbePlayer->isCosmeticUnlocked($cosmetic)) {
                        $rbePlayer->activateCosmetic($cosmetic);
                    } else {
                        //todo: Buy form
                    }
                }
            }
        });
        $form->setTitle("§lCosmetics");
        foreach($category->getCosmetics() as $cosmetic) {
            //Mhhhhh - Not that good
            if($rbePlayer->isCosmeticActivated($cosmetic)) {
                $form->addButton("§a" . $cosmetic->getName(), $cosmetic->getIconType(), $cosmetic->getIcon(), $cosmetic->getIdentifier());
            } elseif($rbePlayer->isCosmeticUnlocked($cosmetic)) {
                $form->addButton("§7" . $cosmetic->getName(), $cosmetic->getIconType(), $cosmetic->getIcon(), $cosmetic->getIdentifier());
            } else {
                $form->addButton("§c" . $cosmetic->getName() . "\n§8Buy: " . $cosmetic->getPrice(), $cosmetic->getIconType(), $cosmetic->getIcon(), $cosmetic->getIdentifier());
            }
        }
        $form->addButton("§aBack", 0, "textures/ui/undoArrow", "back");
        $form->sendToPlayer($player);
    }
}