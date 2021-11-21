<?php

namespace baubolp\ryzerbe\lobbycore\form\shop;

use baubolp\ryzerbe\lobbycore\shop\ShopManager;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ShopCategoryOverviewForm {
    public static function open(Player $player): void{
        $form = new SimpleForm(function(Player $player, $data){
            if($data === null) return;
            $category = ShopManager::getCategories()[$data] ?? null;
            if($category === null) return;
            ShopCategoryArticleOverviewForm::open($player, $category);
        });
        $form->setTitle(TextFormat::GREEN . TextFormat::BOLD . "Shop");
        foreach(ShopManager::getCategories() as $category) $form->addButton($category->getName(), -1, "", $category->getName());
        $form->sendToPlayer($player);
    }
}