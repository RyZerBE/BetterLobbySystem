<?php


namespace baubolp\ryzerbe\lobbycore\gui;

use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Packets\PlayerMessagePacket;
use baubolp\core\provider\CoinProvider;
use baubolp\core\provider\LanguageProvider;
use baubolp\ryzerbe\lobbycore\Loader;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use baubolp\ryzerbe\lobbycore\provider\LottoProvider;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use function in_array;
use function mt_rand;

class LottoGUI
{
    /** @var \pocketmine\Player */
    private $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }


    public function send(): void
    {
        $player = $this->player;
        $inv = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST)
            ->setName(Loader::PREFIX . TextFormat::AQUA . "500.000 Coins")
            ->setListener(function (InvMenuTransaction $transaction) use ($player): InvMenuTransactionResult {
                $item = $transaction->getItemClicked();
                $action = $transaction->getAction();
                if ($item->getId() === Item::PAPER) {
                    for ($i = 0; $i < 54; $i++)
                        $transaction->getAction()->getInventory()->setItem($i, Item::get(Item::ENDER_CHEST)->setCustomName(TextFormat::GOLD . "???"));

                    for ($i = 0; $i < 10; $i++)
                        $player->playSound("random.pop", 5.0, 1.0, [$player]);

                    LottoProvider::removeTicket(LobbyPlayerCache::getLobbyPlayer($player->getName()));
                    return $transaction->discard();
                }
                if ($item->getId() != Item::ENDER_CHEST) return $transaction->discard();
                $slot = $action->getSlot();
                $win = LottoProvider::getRandomWin();
                $winItem = LottoProvider::getItemByInt($win);
                $inv = $action->getInventory();

                $inv->setItem($slot, $winItem);
                if (($obj = LobbyPlayerCache::getLobbyPlayer($player)) != null) {
                    $obj->addLottoWin($win);
                    if (count($obj->getLottoWin()) > 4) {
                        $meta = mt_rand(0, 15);
                        $usedMeta = [$meta];
                        for ($i = 0; $i < 54; $i++) {
                            if($i % 9 === 0) {
                                while(in_array($meta, $usedMeta)) $meta = mt_rand(0, 15);
                                $usedMeta[] = $meta;
                            }
                            $inv->setItem($i, Item::get(Item::STAINED_GLASS_PANE, $meta));
                        }
                        if ($obj->getTickets() > 0)
                            $inv->setItem(45, Item::get(Item::PAPER)->setCustomName(TextFormat::GREEN . "Play again"));
                        else
                            $inv->setItem(45, Item::get(-161)->setCustomName(TextFormat::RED . "You cant play again"));
                        $coins = 0;
                        foreach ($obj->getLottoWin() as $win)
                            $coins += $win;

                        $inv->setItem(20, LottoProvider::getItemByInt($obj->getLottoWin()[0]));
                        $inv->setItem(21, LottoProvider::getItemByInt($obj->getLottoWin()[1]));
                        $inv->setItem(22, LottoProvider::getItemByInt($obj->getLottoWin()[2]));
                        $inv->setItem(23, LottoProvider::getItemByInt($obj->getLottoWin()[3]));
                        $inv->setItem(24, LottoProvider::getItemByInt($obj->getLottoWin()[4]));

                        $inv->setItem(31, Item::get(Item::HOPPER)->setCustomName("§r"));
                        $inv->setItem(40, Item::get(Item::CHEST)->setCustomName(TextFormat::AQUA . "Total".TextFormat::GRAY.": ".TextFormat::GOLD.($coins > 1000 ? TextFormat::BOLD : "").$coins));

                        CoinProvider::addCoins($player->getName(), $coins);
                        $obj->setLottoWin([]);

                        $player->playSound("lodestone_compass.link_compass_to_lodestone", 5.0, 1, [$player]);

                        if ($coins > 5000 && $coins < 25000) {
                            foreach (Server::getInstance()->getOnlinePlayers() as $players)
                                $players->sendMessage("\n\n" . Loader::PREFIX . LanguageProvider::getMessageContainer('lobby-lotto-win', $players->getName(), ['#coins' => $coins, '#playername' => $player->getName()]));
                        } else if ($coins >= 25000) {
                            $pk = new PlayerMessagePacket();
                            $pk->addData("players", "ALL");
                            $pk->addData("message", "\n\n&aLOTTERIE &8-> &a" . $player->getName() . " &fhat einen Gewinn von &e" . $coins . " Coins &fgemacht! MASHALLA :)");
                            CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
                        }
                    } else {
                        $player->playSound("note.pling", 5.0, 1 + (mt_rand(0, 4) * 0.2), [$player]);
                    }
                }
                return $transaction->discard();
            });
        for ($i = 0; $i < 54; $i++)
            $inv->getInventory()->setItem($i, Item::get(Item::ENDER_CHEST)->setCustomName(TextFormat::GOLD . "???"));
        $inv->send($player);
    }
}