<?php

namespace baubolp\ryzerbe\lobbycore\form\shop;

use baubolp\ryzerbe\lobbycore\shop\ShopArticle;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ShopArticleBuyForm {
    public static function open(Player $player, ShopArticle $article): void{
        $form = new SimpleForm(function(Player $player, $data) use ($article){
            if($data === null) return;
            switch($data){
                case "buy":
                    $article->buyArticle($player);
                    break;
            }
        });
        $form->setTitle(TextFormat::GREEN . TextFormat::BOLD . "Shop");
        $form->setContent($article->getDescription());
        $form->addButton(TextFormat::GOLD . $article->getPrice() . " Coins", -1, "", "coins");
        $form->addButton(TextFormat::GREEN . TextFormat::BOLD . "✔ CLICK TO BUY", -1, "", "buy");
        $form->addButton(TextFormat::RED . TextFormat::BOLD . "✘ CANCEL", -1, "", "cancel");
        $form->sendToPlayer($player);
    }
}