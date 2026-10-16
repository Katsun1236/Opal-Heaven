<?php

declare(strict_types=1);

namespace opal\core\trade;

use opal\core\combat\CombatTagManager;
use opal\core\economy\EconomyManager;
use opal\core\ui\CustomForm;
use opal\core\ui\FormManager;
use opal\core\ui\ModalForm;
use opal\core\ui\SimpleForm;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class TradeSession{
    public Player $player1;
    public Player $player2;

    /** @var Item[] */
    public array $offer1 = [];
    /** @var Item[] */
    public array $offer2 = [];

    public int $money1 = 0;
    public int $money2 = 0;

    public bool $ready1 = false;
    public bool $ready2 = false;

    public int $antiSwitchCooldown1 = 0;
    public int $antiSwitchCooldown2 = 0;

    public function __construct(Player $p1, Player $p2){
        $this->player1 = $p1;
        $this->player2 = $p2;
    }
}

class TradeManager implements Listener{

    private static ?self $instance = null;
    private EconomyManager $economy;
    private CombatTagManager $combatTag;

    /** @var array<string, string> target_name => sender_name */
    private array $pendingRequests = [];

    /** @var array<string, TradeSession> */
    private array $activeSessions = [];

    public function __construct(EconomyManager $economy, CombatTagManager $combatTag){
        self::$instance = $this;
        $this->economy = $economy;
        $this->combatTag = $combatTag;
    }

    public static function getInstance() : self{
        return self::$instance ?? throw new \RuntimeException("TradeManager has not been initialized yet.");
    }

    public function onPlayerQuit(PlayerQuitEvent $event) : void{
        $name = $event->getPlayer()->getName();
        unset($this->pendingRequests[$name]);

        foreach($this->pendingRequests as $target => $sender){
            if($sender === $name){
                unset($this->pendingRequests[$target]);
            }
        }

        if(isset($this->activeSessions[$name])){
            $session = $this->activeSessions[$name];
            $this->cancelTrade($session, "Le troc a été interrompu suite à la déconnexion d'un joueur.");
        }
    }

    public function requestTrade(Player $sender, Player $target) : void{
        if($sender->getName() === $target->getName()){
            $sender->sendMessage("§c❌ Vous ne pouvez pas échanger avec vous-même !");
            return;
        }

        if($sender->getWorld() !== $target->getWorld() || $sender->getPosition()->distance($target->getPosition()) > 6.0){
            $sender->sendMessage("§c❌ Vous devez être dans le même monde et à moins de 6 blocs du joueur !");
            return;
        }

        if($this->combatTag->isTagged($sender) || $this->combatTag->isTagged($target)){
            $sender->sendMessage("§c❌ Impossible d'échanger en état de combat !");
            return;
        }

        if(isset($this->activeSessions[$sender->getName()])){
            $sender->sendMessage("§c❌ Vous êtes déjà dans un échange en cours !");
            return;
        }

        if(isset($this->activeSessions[$target->getName()])){
            $sender->sendMessage("§c❌ Ce joueur est déjà dans un échange avec quelqu'un d'autre !");
            return;
        }

        $this->pendingRequests[$target->getName()] = $sender->getName();
        $sender->sendMessage("§a🤝 Demande d'échange envoyée à §e" . $target->getName());
        $target->sendMessage("§6🤝 §e" . $sender->getName() . " §fvous propose un échange sécurisé ! Tapez §a/trade accept §fpour ouvrir le troc.");
    }

    public function acceptTrade(Player $receiver) : void{
        $senderName = $this->pendingRequests[$receiver->getName()] ?? null;
        if($senderName === null){
            $receiver->sendMessage("§c❌ Aucune demande d'échange en attente.");
            return;
        }

        $sender = $receiver->getServer()->getPlayerExact($senderName);
        if($sender === null || !$sender->isOnline()){
            $receiver->sendMessage("§c❌ Le joueur n'est plus en ligne.");
            unset($this->pendingRequests[$receiver->getName()]);
            return;
        }

        if(isset($this->activeSessions[$receiver->getName()]) || isset($this->activeSessions[$sender->getName()])){
            $receiver->sendMessage("§c❌ L'un des joueurs est déjà dans un échange actif.");
            return;
        }

        if($sender->getWorld() !== $receiver->getWorld() || $sender->getPosition()->distance($receiver->getPosition()) > 6.0){
            $receiver->sendMessage("§c❌ Le joueur s'est trop éloigné ou a changé de dimension (> 6 blocs).");
            return;
        }

        unset($this->pendingRequests[$receiver->getName()]);
        $session = new TradeSession($sender, $receiver);
        $this->activeSessions[$sender->getName()] = $session;
        $this->activeSessions[$receiver->getName()] = $session;

        $this->openTradeMenu($sender, $session);
        $this->openTradeMenu($receiver, $session);
    }

    public function openTradeMenu(Player $player, TradeSession $session) : void{
        $isP1 = ($player->getName() === $session->player1->getName());
        $other = $isP1 ? $session->player2 : $session->player1;
        $myOffer = $isP1 ? $session->offer1 : $session->offer2;
        $otherOffer = $isP1 ? $session->offer2 : $session->offer1;
        $myMoney = $isP1 ? $session->money1 : $session->money2;
        $otherMoney = $isP1 ? $session->money2 : $session->money1;
        $myReady = $isP1 ? $session->ready1 : $session->ready2;
        $otherReady = $isP1 ? $session->ready2 : $session->ready1;

        $form = new SimpleForm(function(Player $player, $data) use ($session, $isP1) : void{
            if($data === null){
                $this->cancelTrade($session, "Échange annulé par un joueur.");
                return;
            }

            switch($data){
                case 0: // Ajouter objet en main
                    $item = $player->getInventory()->getItemInHand();
                    if($item->isNull()){
                        $player->sendMessage("§c❌ Vous devez tenir un objet en main !");
                        $this->openTradeMenu($player, $session);
                        return;
                    }
                    $this->triggerAntiSwitch($session);
                    if($isP1){
                        $session->offer1[] = clone $item;
                    }else{
                        $session->offer2[] = clone $item;
                    }
                    $player->getInventory()->setItemInHand(VanillaItems::AIR());
                    $this->refreshTradeViews($session);
                    break;

                case 1: // Définir pièces
                    $this->openMoneyInput($player, $session, $isP1);
                    break;

                case 2: // Basculer PRÊT
                    $now = time();
                    $cooldown = $isP1 ? $session->antiSwitchCooldown1 : $session->antiSwitchCooldown2;
                    if($now < $cooldown){
                        $wait = $cooldown - $now;
                        $player->sendMessage("§c⏳ Sécurité anti-switch active ! Patientez {$wait}s.");
                        $this->openTradeMenu($player, $session);
                        return;
                    }

                    if($isP1){
                        $session->ready1 = !$session->ready1;
                    }else{
                        $session->ready2 = !$session->ready2;
                    }

                    if($session->ready1 && $session->ready2){
                        $this->executeAtomicTrade($session);
                    }else{
                        $this->refreshTradeViews($session);
                    }
                    break;

                case 3: // Annuler
                    $this->cancelTrade($session, "Échange annulé.");
                    break;
            }
        });

        $form->setTitle("§l§dÉCHANGE SÉCURISÉ — " . $other->getName());
        $distance = ($player->getWorld() === $other->getWorld()) 
            ? round($player->getPosition()->distance($other->getPosition()), 1) . "m"
            : "Monde différent";

        $text = "§7Distance: §a" . $distance . "\n\n" .
            "§6--- VOTRE OFFRE ---\n" .
            "§fPièces Célestes: §e" . number_format($myMoney) . " Or\n" .
            "§fObjets proposés: §b" . count($myOffer) . " items\n" .
            "§fStatut: " . ($myReady ? "§a✔ PRÊT" : "§c✖ EN ATTENTE") . "\n\n" .
            "§6--- OFFRE DE " . strtoupper($other->getName()) . " ---\n" .
            "§fPièces Célestes: §e" . number_format($otherMoney) . " Or\n" .
            "§fObjets proposés: §b" . count($otherOffer) . " items\n" .
            "§fStatut: " . ($otherReady ? "§a✔ PRÊT" : "§c✖ EN ATTENTE") . "\n";

        $form->setContent($text);
        $form->addButton("§e➕ Ajouter l'item en main");
        $form->addButton("§6💰 Déposer de l'Or");
        $form->addButton($myReady ? "§cAnnuler mon statut Prêt" : "§a✔ VALIDER MON OFFRE (PRÊT)");
        $form->addButton("§c✖ Abandonner l'échange");

        FormManager::getInstance()->sendForm($player, $form);
    }

    private function openMoneyInput(Player $player, TradeSession $session, bool $isP1) : void{
        $form = new CustomForm(function(Player $player, $data) use ($session, $isP1) : void{
            if($data === null || !isset($data[0])) return;
            $amount = (int)$data[0];
            $bal = $this->economy->getBalance($player);
            if($amount < 0 || $amount > $bal){
                $player->sendMessage("§c❌ Montant invalide ou solde insuffisant (" . number_format($bal) . " Or max).");
            }else{
                $this->triggerAntiSwitch($session);
                if($isP1){
                    $session->money1 = $amount;
                }else{
                    $session->money2 = $amount;
                }
            }
            $this->refreshTradeViews($session);
        });

        $form->setTitle("§6Déposer de l'Or Céleste");
        $form->addInput("Montant d'Or à inclure dans l'échange :", "Ex: 500");
        FormManager::getInstance()->sendForm($player, $form);
    }

    private function triggerAntiSwitch(TradeSession $session) : void{
        $session->ready1 = false;
        $session->ready2 = false;
        $session->antiSwitchCooldown1 = time() + 3;
        $session->antiSwitchCooldown2 = time() + 3;
    }

    private function refreshTradeViews(TradeSession $session) : void{
        if($session->player1->isOnline()){
            $this->openTradeMenu($session->player1, $session);
        }
        if($session->player2->isOnline()){
            $this->openTradeMenu($session->player2, $session);
        }
    }

    private function executeAtomicTrade(TradeSession $session) : void{
        $p1 = $session->player1;
        $p2 = $session->player2;

        if(!$p1->isOnline() || !$p2->isOnline()){
            $this->cancelTrade($session, "Un des joueurs s'est déconnecté avant la validation finale.");
            return;
        }

        // Vérification de sécurité des soldes au moment exact de la validation
        if($session->money1 > 0 && $this->economy->getMoney($p1) < $session->money1){
            $this->cancelTrade($session, "Échange annulé : " . $p1->getName() . " ne possède plus les fonds requis.");
            return;
        }
        if($session->money2 > 0 && $this->economy->getMoney($p2) < $session->money2){
            $this->cancelTrade($session, "Échange annulé : " . $p2->getName() . " ne possède plus les fonds requis.");
            return;
        }

        // Swap monétaire sécurisé (compatible offline mode via objet Player)
        if($session->money1 > 0){
            $this->economy->transfer($p1, $p2, $session->money1);
        }
        if($session->money2 > 0){
            $this->economy->transfer($p2, $p1, $session->money2);
        }

        // Swap d'items avec protection contre la perte d'inventaire plein
        foreach($session->offer1 as $it){
            $leftovers = $p2->getInventory()->addItem($it);
            foreach($leftovers as $l){
                $p2->getWorld()->dropItem($p2->getPosition(), $l);
            }
        }
        foreach($session->offer2 as $it){
            $leftovers = $p1->getInventory()->addItem($it);
            foreach($leftovers as $l){
                $p1->getWorld()->dropItem($p1->getPosition(), $l);
            }
        }

        unset($this->activeSessions[$p1->getName()]);
        unset($this->activeSessions[$p2->getName()]);

        $p1->sendMessage("§a🤝 Échange sécurisé réussi avec succès !");
        $p2->sendMessage("§a🤝 Échange sécurisé réussi avec succès !");
    }

    private function cancelTrade(TradeSession $session, string $reason) : void{
        $p1 = $session->player1;
        $p2 = $session->player2;

        // Rendre les items avec sécurité inventaire plein
        foreach($session->offer1 as $it){
            if($p1->isOnline()){
                $leftovers = $p1->getInventory()->addItem($it);
                foreach($leftovers as $l){
                    $p1->getWorld()->dropItem($p1->getPosition(), $l);
                }
            }
        }
        foreach($session->offer2 as $it){
            if($p2->isOnline()){
                $leftovers = $p2->getInventory()->addItem($it);
                foreach($leftovers as $l){
                    $p2->getWorld()->dropItem($p2->getPosition(), $l);
                }
            }
        }

        unset($this->activeSessions[$p1->getName()]);
        unset($this->activeSessions[$p2->getName()]);

        if($p1->isOnline()) $p1->sendMessage("§e[Troc] §c" . $reason);
        if($p2->isOnline()) $p2->sendMessage("§e[Troc] §c" . $reason);
    }
}
