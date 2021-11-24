<?php

namespace baubolp\ryzerbe\lobbycore\form;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\player\PMMPPlayer;
use ryzerbe\core\rank\RankManager;
use ryzerbe\core\RyZerBE;
use function implode;

class PlayerQuickOverviewForm {

    public static function open(PMMPPlayer $player, PMMPPlayer $interactPlayer){
        $rbePlayer = $interactPlayer->getRyZerPlayer();
        if($rbePlayer === null) return;
        $form = new SimpleForm(function(Player $player, $data) use ($interactPlayer, $rbePlayer){
            if($data === null) return;
            if(!$interactPlayer->isConnected()) return;

            switch($data){
                case "party":
                    if($rbePlayer->getNick() !== null){
                        LanguageProvider::getMessageContainer("party-invite-player", $player, ["#player" => $rbePlayer->getNick()]);
                    }else{
                        $player->getServer()->dispatchCommand($player, "party invite ".$interactPlayer->getName());
                    }
                    break;
                case "friend":
                    $player->sendMessage(RyZerBE::PREFIX.TextFormat::RED."Dieses Feature kommt in einem weiteren Update von uns!");
                    break;
                case "clan":
                    if($rbePlayer->getNick() !== null){
                        $player->getServer()->dispatchCommand($player, "clan invite ".$interactPlayer->getName());
                    }
                    break;
            }
        });

        $onlinetime = $rbePlayer->getOnlineTime();
        $clan = $rbePlayer->getClan();
        if($clan === null || $rbePlayer->getNick() !== null) $clan = "NO CLAN"; else $clan = $clan->getClanName().TextFormat::DARK_GRAY."[".$clan->getClanTag().TextFormat::DARK_GRAY."]";

        $backupRank = RankManager::getInstance()->getBackupRank();
        $rank = ($rbePlayer->getPlayerSettings()->isRankToggled() === true || $rbePlayer->getNick() !== null) ? $backupRank->getColor().$backupRank->getRankName() : $rbePlayer->getRank()->getColor().$rbePlayer->getRank()->getRankName();
        $deviceOs = $rbePlayer->getLoginPlayerData()->getDeviceOsName();
        $deviceInput = $rbePlayer->getLoginPlayerData()->getOsInputName();
        $level = $rbePlayer->getNetworkLevel()->getLevelColor().$rbePlayer->getNetworkLevel()->getLevel();
        $coins = $rbePlayer->getCoins();

        $content = [];
        $content[] = TextFormat::WHITE."Rank: ".$rank;
        $content[] = TextFormat::WHITE."Coins: ".TextFormat::YELLOW.$coins;
        $content[] = TextFormat::WHITE."Clan: ".TextFormat::YELLOW.$clan;
        $content[] = TextFormat::WHITE."Level: ".TextFormat::YELLOW.$level;
        $content[] = TextFormat::WHITE."Onlinetime: ".TextFormat::YELLOW.$onlinetime;
        $content[] = TextFormat::WHITE."Device: ".TextFormat::YELLOW.$deviceOs;
        $content[] = TextFormat::WHITE."Device Input: ".TextFormat::YELLOW.$deviceInput;
        $form->setContent(implode("\n", $content));
        if($rbePlayer->getPlayerSettings()->isPartyInvitesEnabled()) $form->addButton(TextFormat::DARK_PURPLE."Invite to your party", 1, "https://media.discordapp.net/attachments/602115215307309066/909802469658337290/confetti-party-popper-paper-spark-512.png?width=410&height=410", "party");
        $form->addButton(TextFormat::YELLOW."Invite to your clan", 1, "https://media.discordapp.net/attachments/412217468287713282/881162752280903750/War_Leagues.png?width=144&height=144", "clan");
        if($rbePlayer->getPlayerSettings()->isFriendRequestsEnabled()) $form->addButton(TextFormat::GREEN."Add as friend", 1, "https://media.discordapp.net/attachments/412217468287713282/881164230009684029/friends.png?width=160&height=160", "friend");
        $form->setTitle(TextFormat::GOLD . TextFormat::BOLD . "Quick Player overview");
        $form->sendToPlayer($player);
    }
}