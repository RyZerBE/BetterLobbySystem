<?php

namespace baubolp\ryzerbe\lobbycore\gui;

use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Packets\CreatePrivateServerPacket;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class PrivateServerGUI {
    /** @var Player */
    private $player;

    /**
     * PrivateServerGUI constructor.
     *
     * @param Player $player
     */
    public function __construct(Player $player){
        $this->player = $player;
    }

    public function send(){
        $menu = InvMenu::create(InvMenu::TYPE_CHEST)->setListener(function(InvMenuTransaction $transaction): InvMenuTransactionResult{
                $player = $transaction->getPlayer();
                $action = $transaction->getAction();
                $item = $transaction->getItemClicked();
                $cloudGroup = $item->getNamedTag()->getString("group", "error");
                if($cloudGroup === "error"){
                    $player->playSound("note.bass", 5.0, 1.0, [$player]);
                    $player->sendTitle("§c§lERROR!");
                    return $transaction->discard();
                }
                if(!CloudBridge::getCloudProvider()->existGroup($cloudGroup)){
                    $player->playSound("note.bass", 5.0, 1.0, [$player]);
                    $player->sendTitle("§c§lERROR!");
                    return $transaction->discard();
                }
                if(!$player->hasPermission("lobby.pserver")){
                    $player->playSound("note.bass", 5.0, 1.0, [$player]);
                    return $transaction->discard();
                }
                $packet = new CreatePrivateServerPacket();
                $packet->addData("groupName", $cloudGroup);
                $packet->addData("playerName", $player->getName());
                CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($packet);
                # $player->doCloseInventory(); -> CRASH
                return $transaction->discard();
            });
        $menu->setName(TextFormat::DARK_PURPLE . "Private Server");
        $inv = $menu->getInventory();
        $i = 0;
        foreach(CloudBridge::getCloudProvider()->getGroups() as $group){
            if(CloudBridge::getCloudProvider()->canGroupBePrivate($group)){
                if($group != "ReplayServer"){
                    $item = Item::get(Item::PAPER)->setCustomName(TextFormat::GREEN . $group . "\n" . TextFormat::GRAY . "Click to create");
                    $item->getNamedTag()->setString("group", $group);
                    $inv->setItem($i, $item);
                    $i++;
                }
            }
        }
        $menu->send($this->player);
    }
}