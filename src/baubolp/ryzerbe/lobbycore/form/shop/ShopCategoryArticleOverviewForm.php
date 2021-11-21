<?php

namespace baubolp\ryzerbe\lobbycore\form\shop;

use baubolp\ryzerbe\lobbycore\shop\ShopCategory;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ShopCategoryArticleOverviewForm {
    public static function open(Player $player, ShopCategory $category): void{
        $form = new SimpleForm(function(Player $player, $data) use ($category){
            if($data === null) return;
            $article = $category->getArticles()[$data] ?? null;
            if($article === null) return;
            ShopArticleBuyForm::open($player, $article);
        });
        $form->setTitle(TextFormat::GREEN . TextFormat::BOLD . "Articles of " . $category->getName());
        foreach($category->getArticles() as $article) $form->addButton($article->getName(), -1, "", $article->getName());
        $form->sendToPlayer($player);
    }
}