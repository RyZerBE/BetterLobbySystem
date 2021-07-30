<?php

namespace baubolp\ryzerbe\lobbycore\form\cosmetic;

use baubolp\core\provider\CoinProvider;
use baubolp\core\provider\LanguageProvider;
use baubolp\ryzerbe\lobbycore\cosmetic\category\CosmeticCategory;
use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CosmeticsOverviewForm {

    /**
     * @param Player $player
     * @param CosmeticCategory $category
     */
    public static function open(Player $player, CosmeticCategory $category): void {
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        $form = new SimpleForm(function(Player $player, $data) use ($lobbyPlayer): void {
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
                    if($lobbyPlayer->isCosmeticActivated($cosmetic)) {
                        $lobbyPlayer->deactivateCosmetic($cosmetic);
                        if($cosmetic->getCategory() === CosmeticManager::CATEGORY_SPECIALS)
                            $lobbyPlayer->reloadInventory();
                    } elseif($lobbyPlayer->isCosmeticUnlocked($cosmetic)) {
                        $lobbyPlayer->activateCosmetic($cosmetic);
                        if($cosmetic->getCategory() === CosmeticManager::CATEGORY_SPECIALS)
                            $lobbyPlayer->reloadInventory();
                    } else {
                        $rbePlayer = $lobbyPlayer->asRyZerPlayer();
                        if($rbePlayer->getCoins() < $cosmetic->getPrice()) {
                            $form = new SimpleForm(function (Player $player, $data):void{});
                            $form->setTitle(TextFormat::RED."Oh man..");
                            $form->setContent(LanguageProvider::getMessageContainer("not-enough-coins", $player->getName()));
                            $form->addButton(TextFormat::RED.TextFormat::BOLD."✘ CANNOT BUY");
                            $form->sendToPlayer($player);
                            CosmeticsCategoriesForm::open($player);
                            return;
                        }

                        $form = new SimpleForm(function (Player $player, $data) use ($cosmetic, $lobbyPlayer):void {
                            if($data === null) return;

                            switch ($data) {
                                case "buy":
                                    if($cosmetic->getCategory() === CosmeticManager::CATEGORY_SPECIALS)
                                        $lobbyPlayer->reloadInventory();

                                    CoinProvider::removeCoins($player->getName(), $cosmetic->getPrice());
                                    $lobbyPlayer->unlockCosmetic($cosmetic);
                                    $lobbyPlayer->activateCosmetic($cosmetic);
                                    $player->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer('lobby-successful-bought', $player->getName(), ['#addon' => $cosmetic->getName(), '#cost' => $cosmetic->getPrice()]));
                                    $player->playSound("random.levelup", 5.0, 1.0, [$player]);
                                    CosmeticsCategoriesForm::open($player);
                                    break;
                            }
                        });
                        $form->setTitle(TextFormat::GOLD.TextFormat::BOLD."Sure?");
                        $form->addButton(TextFormat::GREEN.TextFormat::BOLD."✔ CLICK TO BUY", -1, "", "buy");
                        $form->setContent(LanguageProvider::getMessageContainer('lobby-really-buy-addon', $player->getName(), ['#addon' => $cosmetic->getName(), '#cost' => $cosmetic->getPrice()]));
                        $form->sendToPlayer($player);
                    }
                }
            }
        });
        $form->setTitle("§lCosmetics");
        foreach($category->getCosmetics() as $cosmetic) {
            //Mhhhhh - Not that good
            if($lobbyPlayer->isCosmeticActivated($cosmetic)) {
                $form->addButton("§a§l" . $cosmetic->getName()."\n§r§8(§2ACTIVATED§8)", $cosmetic->getIconType(), $cosmetic->getIcon(), $cosmetic->getIdentifier());
            } elseif($lobbyPlayer->isCosmeticUnlocked($cosmetic)) {
                $form->addButton("§a" . $cosmetic->getName()."\n§8(§bClick to activate§8)", $cosmetic->getIconType(), $cosmetic->getIcon(), $cosmetic->getIdentifier());
            } else {
                $form->addButton("§c" . $cosmetic->getName() . "\n§8(§6" . $cosmetic->getPrice()."§6 Coins§8)", $cosmetic->getIconType(), $cosmetic->getIcon(), $cosmetic->getIdentifier());
            }
        }
        $form->addButton("§aBack", 0, "textures/ui/undoArrow", "back");
        $form->sendToPlayer($player);
    }
}