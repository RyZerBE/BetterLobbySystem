<?php


namespace baubolp\ryzerbe\lobbycore\command;


use baubolp\ryzerbe\lobbycore\form\shop\ShopCategoryOverviewForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class ShopCommand extends Command
{

    public function __construct()
    {
        parent::__construct("shop", "buy articles", "", []);
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player) return;

        ShopCategoryOverviewForm::open($sender);
    }
}