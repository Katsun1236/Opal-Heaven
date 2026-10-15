<?php

declare(strict_types=1);

namespace opalheaven\core;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\Position;

use opalheaven\core\orders\OrderManager;
use opalheaven\core\orders\OrderType;
use opalheaven\core\orders\SpecializationType;
use opalheaven\core\orders\EconomicMethodManager;
use opalheaven\core\combat\PostureManager;
use opalheaven\core\mining\ResonanceManager;
use opalheaven\core\agriculture\MysticAgricultureManager;
use opalheaven\core\ui\FormManager;
use opalheaven\core\biomes\BiomeRegistry;
use opalheaven\core\biomes\BiomeManager;
use opalheaven\core\world\OpalWorldGenerator;
use opalheaven\core\faction\FactionManager;
use opalheaven\core\security\AntiExploitManager;
use opalheaven\core\onboarding\OnboardingManager;

class OpalCore extends PluginBase implements Listener {

    private static self $instance;

    private OrderManager $orderManager;
    private PostureManager $postureManager;
    private ResonanceManager $resonanceManager;
    private MysticAgricultureManager $agricultureManager;
    private FormManager $formManager;
    private BiomeRegistry $biomeRegistry;
    private BiomeManager $biomeManager;
    private FactionManager $factionManager;
    private AntiExploitManager $antiExploitManager;
    private OnboardingManager $onboardingManager;

    public static function getInstance() : self {
        return self::$instance;
    }

    protected function onEnable() : void {
        self::$instance = $this;

        $this->saveDefaultConfig();
        @mkdir($this->getDataFolder());

        // 1. Déploiement et chargement des 40 Biomes Moddés
        $biomesDataDir = rtrim($this->getDataFolder(), '/\\') . DIRECTORY_SEPARATOR . "biomes" . DIRECTORY_SEPARATOR;
        if (!is_dir($biomesDataDir)) {
            @mkdir($biomesDataDir, 0777, true);
        }

        $existing = glob($biomesDataDir . "*.yml");
        if ($existing === false || count($existing) === 0) {
            $resourcesDir = rtrim($this->getFile(), '/\\') . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "biomes" . DIRECTORY_SEPARATOR;
            if (is_dir($resourcesDir)) {
                foreach (glob($resourcesDir . "*.yml") ?: [] as $resFile) {
                    copy($resFile, $biomesDataDir . basename($resFile));
                }
            }
        }

        $this->biomeRegistry = new BiomeRegistry($biomesDataDir);
        $this->biomeManager = new BiomeManager($this->biomeRegistry);

        // 2. Enregistrement du générateur de monde moddé
        GeneratorManager::getInstance()->addGenerator(
            OpalWorldGenerator::class,
            "opal_custom",
            fn() => null,
            overwrite: true,
            fast: true
        );

        // 3. Initialisation des sous-systèmes
        $dbPath = $this->getDataFolder() . "orders.db";
        $factionDbPath = $this->getDataFolder() . "factions.db";

        $this->orderManager = new OrderManager($dbPath);
        $this->postureManager = new PostureManager();
        $this->resonanceManager = new ResonanceManager();
        $this->agricultureManager = new MysticAgricultureManager();
        $this->factionManager = new FactionManager($factionDbPath);
        $this->antiExploitManager = new AntiExploitManager();
        $this->onboardingManager = new OnboardingManager($this->orderManager->getDatabase());
        $this->formManager = new FormManager($this);

        // 4. Enregistrement des événements
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        // 5. Tâche périodique (chaque seconde = 20 ticks) pour posture, résonance et auras de biomes
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void {
            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                if (!$player->isConnected() || !$player->isAlive() || !$player->spawned) {
                    continue;
                }
                $this->postureManager->tickDecay($player);
                $this->resonanceManager->tickDecay($player);
                $this->biomeManager->tickPlayer($player);
            }
        }), 20);

        // 6. Préchargement et maintien en mémoire des chunks de spawn
        $defaultWorld = $this->getServer()->getWorldManager()->getDefaultWorld();
        if ($defaultWorld !== null) {
            $spawn = $defaultWorld->getSpawnLocation();
            $spawnChunkX = $spawn->getFloorX() >> 4;
            $spawnChunkZ = $spawn->getFloorZ() >> 4;
            $loader = new class implements \pocketmine\world\ChunkLoader{};
            for ($cx = $spawnChunkX - 2; $cx <= $spawnChunkX + 2; ++$cx) {
                for ($cz = $spawnChunkZ - 2; $cz <= $spawnChunkZ + 2; ++$cz) {
                    $defaultWorld->registerChunkLoader($loader, $cx, $cz, true);
                    $defaultWorld->loadChunk($cx, $cz);
                    $defaultWorld->orderChunkPopulation($cx, $cz, $loader);
                }
            }
        }

        $loadedBiomes = $this->biomeRegistry->getCount();
        $this->getLogger()->info("§d[OpalCore] §aLe coeur d'Opal Heaven est active avec succes !");
        $this->getLogger()->info("§d[OpalCore] §eSystemes : 3 Ordres, 9 Specialisations, Posture 40tks, Factions SQLite, Securite Anti-Exploit.");
        $this->getLogger()->info("§d[OpalCore] §bMoteur de Monde : {$loadedBiomes} Biomes Moddes uniques charges !");
    }

    public function getOrderManager() : OrderManager { return $this->orderManager; }
    public function getPostureManager() : PostureManager { return $this->postureManager; }
    public function getResonanceManager() : ResonanceManager { return $this->resonanceManager; }
    public function getAgricultureManager() : MysticAgricultureManager { return $this->agricultureManager; }
    public function getFactionManager() : FactionManager { return $this->factionManager; }
    public function getAntiExploitManager() : AntiExploitManager { return $this->antiExploitManager; }
    public function getOnboardingManager() : OnboardingManager { return $this->onboardingManager; }
    public function getFormManager() : FormManager { return $this->formManager; }
    public function getBiomeRegistry() : BiomeRegistry { return $this->biomeRegistry; }
    public function getBiomeManager() : BiomeManager { return $this->biomeManager; }

    /* ------------------------------------------------------------- */
    /*                         ÉVÉNEMENTS                            */
    /* ------------------------------------------------------------- */

    public function onPlayerJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();
        $this->orderManager->loadPlayer($player);
        $this->onboardingManager->handleJoin($player);
    }

    public function onPlayerQuit(PlayerQuitEvent $event) : void {
        $player = $event->getPlayer();
        if ($this->antiExploitManager->isCombatTagged($player)) {
            $this->getLogger()->warning("§c[CombatLogger] Le joueur {$player->getName()} s'est déconnecté en plein combat !");
        }
        $this->orderManager->savePlayer($player);
        $this->postureManager->reset($player);
        $this->biomeManager->removePlayer($player);
    }

    public function onToggleSneak(PlayerToggleSneakEvent $event) : void {
        $this->postureManager->handleSneakToggle($event->getPlayer(), $event->isSneaking());
    }

    public function onBlockPlace(BlockPlaceEvent $event) : void {
        // Tag de sécurité Anti-Exploit (Livre Blanc Section 8.1)
        foreach ($event->getTransaction()->getBlocks() as [$x, $y, $z, $block]) {
            $this->antiExploitManager->markBlockPlaced($block);
        }
    }

    public function onBlockBreak(BlockBreakEvent $event) : void {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        // 1. Détecteur de bot séismique angulaire
        if ($this->antiExploitManager->recordAngleAndCheckBot($player)) {
            $player->sendActionBarMessage("§c§l[ALERTE SYSTÈME] §r§7Comportement automatisé suspect détecté !");
        }

        // 2. Vérification Anti-Duplication PlayerPlaced
        if ($this->antiExploitManager->isPlayerPlaced($block)) {
            $this->antiExploitManager->unmarkBlockPlaced($block);
            // Pas d'XP d'ordre pour les blocs posés par des joueurs
            return;
        }

        $drops = $event->getDrops();
        $this->resonanceManager->handleBlockBreak($player, $block, $drops);
        $this->agricultureManager->handleCropHarvest($player, $block, $drops);
        $event->setDrops($drops);

        // 3. Attribution d'XP d'Ordre organique selon le bloc
        $activeOrder = $this->orderManager->getActiveOrder($player);
        $activeSpec = $this->orderManager->getSpecializationManager()->getSpecialization($player);

        if ($activeOrder === OrderType::ABYSSES) {
            $bname = strtolower($block->getName());
            $xpGain = 1;
            if (str_contains($bname, "coal") || str_contains($bname, "copper")) $xpGain = 6;
            elseif (str_contains($bname, "iron") || str_contains($bname, "lapis")) $xpGain = 15;
            elseif (str_contains($bname, "gold") || str_contains($bname, "redstone")) $xpGain = 25;
            elseif (str_contains($bname, "diamond") || str_contains($bname, "emerald")) $xpGain = 85;
            elseif (str_contains($bname, "debris")) $xpGain = 250;

            $this->orderManager->addXp($player, OrderType::ABYSSES, $xpGain);

            if ($activeSpec === SpecializationType::TERRASCULPTEUR) {
                EconomicMethodManager::trigger($player, $activeSpec, 2, $this->orderManager);
            }
        } elseif ($activeOrder === OrderType::TERRE) {
            $this->orderManager->addXp($player, OrderType::TERRE, 5);
        }
    }

    public function onEntityDamage(EntityDamageByEntityEvent $event) : void {
        $damager = $event->getDamager();
        $victim = $event->getEntity();

        if ($damager instanceof Player && $victim instanceof Player) {
            // 1. Protection du Bouclier de Bénédiction (Onboarding)
            if ($this->onboardingManager->hasBlessingShield($victim)) {
                $rem = $this->onboardingManager->getBlessingRemaining($victim);
                $damager->sendActionBarMessage("§bCe joueur est protégé par la Bénédiction céleste ({$rem}s restantes) !");
                $event->cancel();
                return;
            }
            if ($this->onboardingManager->hasBlessingShield($damager)) {
                $damager->sendMessage("§cVous ne pouvez pas attaquer d'autres joueurs tant que votre Bénédiction est active.");
                $event->cancel();
                return;
            }

            // 2. Tag CombatLogger (15s)
            $this->antiExploitManager->tagCombat($damager, $victim);

            // 3. Spécialisations des combattants
            $damagerSpec = $this->orderManager->getSpecializationManager()->getSpecialization($damager);
            $victimSpec = $this->orderManager->getSpecializationManager()->getSpecialization($victim);

            // 4. Tentative de parade parfaite (Just-in-Time Parry)
            if ($this->postureManager->attemptParry($victim, $damager, $victimSpec)) {
                $event->cancel();
                // Méthode économique du Bretteur
                if ($victimSpec === SpecializationType::BRETTEUR) {
                    EconomicMethodManager::trigger($victim, $victimSpec, 1, $this->orderManager);
                }
                return;
            }

            // 5. Bonus passif Traqueur : Lame Furtive (cône arrière de 120° = +35% dégâts)
            if ($damagerSpec === SpecializationType::TRAQUEUR) {
                $victimDir = $victim->getDirectionVector();
                $attackerDir = $damager->getDirectionVector();
                $dot = ($victimDir->x * $attackerDir->x) + ($victimDir->z * $attackerDir->z);
                // Si l'attaquant regarde dans la même direction que la victime (dans son dos)
                if ($dot > 0.50) {
                    $event->setBaseDamage($event->getBaseDamage() * 1.35);
                    $damager->sendActionBarMessage("§8§l[LAME FURTIVE] §r§c+35% Dégâts dans le dos !");
                    EconomicMethodManager::trigger($damager, $damagerSpec, 1, $this->orderManager);
                }
            }

            // 6. Bonus passif Berserker : Soif de Bataille (+1% dégâts par 5% PV manquants)
            if ($damagerSpec === SpecializationType::BERSERKER) {
                $missingHpPercent = max(0.0, (1.0 - ($damager->getHealth() / $damager->getMaxHealth())) * 100.0);
                $bonusPercent = (int)($missingHpPercent / 5.0) * 0.01;
                if ($bonusPercent > 0.0) {
                    $event->setBaseDamage($event->getBaseDamage() * (1.0 + $bonusPercent));
                    // Lifesteal proportionnel
                    $heal = $event->getBaseDamage() * ($bonusPercent * 0.5);
                    $damager->setHealth(min($damager->getMaxHealth(), $damager->getHealth() + $heal));
                }
            }

            // 7. Compétence active Rupture Ciblée du Bretteur (ignore 50% armure, +35% posture)
            $isRupture = $this->orderManager->getSpecializationManager()->isTargetedRuptureActive($damager);
            if ($isRupture) {
                $this->orderManager->getSpecializationManager()->consumeTargetedRupture($damager);
                $event->setBaseDamage($event->getBaseDamage() * 1.25);
            }

            // 8. État de Stagger (Rupture de garde à 100%) : Ignore 30% d'armure
            if ($this->postureManager->isStaggered($victim)) {
                $event->setBaseDamage($event->getBaseDamage() * 1.30);
                $damager->sendActionBarMessage("§c§l[STAGGER VULNÉRABLE] §r§ePerforation d'armure +30% !");
                return;
            }

            // 9. Calcul exact du gain de posture mathématique
            $weapon = $damager->getInventory()->getItemInHand();
            $postureGain = $this->postureManager->calculatePostureGain((float)$event->getBaseDamage(), $weapon, $victim);
            if ($isRupture) {
                $postureGain += 35.0;
            }

            $broken = $this->postureManager->addPosture($victim, $postureGain, $damager);
            if ($broken && $damagerSpec === SpecializationType::BRETTEUR) {
                EconomicMethodManager::trigger($damager, $damagerSpec, 2, $this->orderManager);
            }
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event) : void {
        $player = $event->getPlayer();
        $cause = $player->getLastDamageCause();

        if ($cause instanceof EntityDamageByEntityEvent) {
            $killer = $cause->getDamager();
            if ($killer instanceof Player && $killer->getId() !== $player->getId()) {
                // Évaluation Anti-Boost XUID
                $multiplier = $this->antiExploitManager->evaluateKillXpMultiplier($killer->getXuid(), $player->getXuid());
                $baseXp = (int)(200 * $multiplier);

                $killerOrder = $this->orderManager->getActiveOrder($killer);
                if ($killerOrder !== null && $baseXp > 0) {
                    $this->orderManager->addXp($killer, $killerOrder, $baseXp);
                    $this->orderManager->addBloodMarks($killer, 5);
                    $killer->sendMessage("§d[Opal Heaven] §a+{$baseXp} XP et §c+5 Marques de Sang §apour l'élimination de §e" . $player->getName() . " §a!");
                }
            }
        }
    }

    /* ------------------------------------------------------------- */
    /*                         COMMANDES                             */
    /* ------------------------------------------------------------- */

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        if (!$sender instanceof Player) {
            if ($command->getName() === "biomes" && isset($args[0]) && $args[0] === "reload") {
                $this->biomeRegistry->loadAll();
                $sender->sendMessage("§d[Biomes] §a40 fichiers YAML recharges avec succes ! Total : " . $this->biomeRegistry->getCount());
                return true;
            }
            $sender->sendMessage("Cette commande doit être exécutée en jeu.");
            return true;
        }

        switch ($command->getName()) {
            case "opal":
                $this->formManager->openMainMenu($sender);
                return true;

            case "ordres":
            case "ordre":
                $this->formManager->openOrdersMenu($sender);
                return true;

            case "f":
            case "faction":
                $this->formManager->openFactionMenu($sender);
                return true;

            case "bloodshop":
                $this->formManager->openBloodShopMenu($sender);
                return true;

            case "skill":
                $this->orderManager->getSpecializationManager()->triggerActiveSkill($sender);
                return true;

            case "combat":
            case "posture":
                $this->formManager->openCombatMenu($sender);
                return true;

            case "minage":
            case "resonance":
                $this->formManager->openMiningMenu($sender);
                return true;

            case "agriculture":
            case "faune":
            case "metiers":
                $this->formManager->openAgricultureMenu($sender);
                return true;

            case "biomes":
                $sub = strtolower($args[0] ?? "");
                if ($sub === "" || $sub === "list" || $sub === "menu") {
                    $this->formManager->openBiomesMenu($sender);
                    return true;
                }

                if ($sub === "info") {
                    $biome = $this->biomeManager->getPlayerCurrentBiome($sender);
                    $sender->sendMessage("§7=========================================");
                    $sender->sendMessage("§d§lBiome Actuel : §r" . $biome->getName());
                    $sender->sendMessage("§7Royaume : §e" . $biome->getCategory() . " §7| Rareté : §6" . $biome->getRarity() . "/10");
                    $sender->sendMessage("§7Description : §f" . $biome->getDescription());
                    $sender->sendMessage("§7Blocs : §b" . $biome->getTopBlockId() . " §7(surface), §b" . $biome->getSubBlockId() . " §7(sous-sol)");
                    $sender->sendMessage("§7Aura : §a" . ($biome->getAuraEffect() ?? "Aucune") . " §7| Particule : §d" . $biome->getParticle());
                    $sender->sendMessage("§7=========================================");
                    return true;
                }

                if ($sub === "reload") {
                    if (!$sender->hasPermission("opalheaven.admin")) {
                        $sender->sendMessage("§cVous n'avez pas la permission d'administrateur.");
                        return true;
                    }
                    $this->biomeRegistry->loadAll();
                    $sender->sendMessage("§d[Biomes] §aLes 40 fichiers de biomes YAML ont ete recharges a chaud ! (" . $this->biomeRegistry->getCount() . " biomes actifs)");
                    return true;
                }

                $sender->sendMessage("§7Usage: §e/biomes [list|info|reload]");
                return true;
        }

        return false;
    }
}
