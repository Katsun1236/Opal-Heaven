# 🌌 OPAL HEAVEN — LE LIVRE BLANC DU GAME DESIGN (PHASE 2 : LES NOUVELLES FRONTIÈRES)
> **Statut :** Phase 1 Entièrement Codée & Validée ➔ Phase 2 : Conception Avancée des 15 Systèmes Moddés  
> **Plateforme :** Minecraft Bedrock Edition (PocketMine-MP 5.x & Go Proxy)  
> **Inspiration Majeure :** Paladium (Java) & Histeria (Bedrock) réinventés avec l'identité unique d'Opal Heaven  

---

## 📋 REGISTRE DE VALIDATION : CE QUI EST DÉJÀ CODÉ & OPÉRATIONNEL (PHASE 1)

Tous les systèmes ci-dessous ont été intégralement rédigés, codés dans `OpalCore.phar` / `OpalMod.phar` et tournent sans erreur sur le serveur :
* ✅ **Combat de Posture & Parade au Sneak :** `PostureManager.php` (Jauge 0-100%, Stagger 40 ticks, fenêtre 1-9 ticks, répulsion).
* ✅ **Les 3 Ordres & 9 Spécialisations :** `OrderManager.php`, `SpecializationManager.php`, types et passifs.
* ✅ **Le Répertoire des 54 Méthodes Économiques :** `EconomicMethodManager.php`.
* ✅ **La Boutique du Sang (PvP Autonome) :** `BloodShopManager.php`.
* ✅ **L'Onboarding en 6 Minutes (« L'Éveil de l'Opale ») :** `OnboardingManager.php`.
* ✅ **Les 40 Biomes Moddés à Auras :** `BiomeRegistry.php`, `BiomeManager.php`, `OpalWorldGenerator.php`.
* ✅ **Base de Données & Claims Faction :** `FactionManager.php`, `TechManager.php`, `factions.db`.
* ✅ **Sécurité & 18 Patchs Anti-Exploits :** `AntiExploitManager.php` (`PlayerPlaced`, Anti-Boost XUID, etc.).
* ✅ **Interface Graphique FormUI :** `FormManager.php` (`ModalFormRequestPacket 0x64`).
* ✅ **Suite de Modération :** `OpalMod.phar` (Freeze, Vanish, Mute, Warn, SQLite).

---

## 📑 TABLE DES MATIÈRES : LES 15 SYSTÈMES MAJEURS DU GAME DESIGN (PHASE 2)

1. [Système 1 : La Pêche Céleste & Abyssale (Mini-Jeu au Timing & Léviathans)](#-système-1--la-pêche-céleste--abyssale-mini-jeu-au-timing--léviathans)
2. [Système 2 : L'Élevage Spécial & La Génétique Vivante (ADN & Bêtes de Guerre)](#-système-2--lélevage-spécial--la-génétique-vivante-adn--bêtes-de-guerre)
3. [Système 3 : Les 5 Tiers d'Armures Moddées & Bâtons Tactiques](#-système-3--les-5-tiers-darmures-moddées--bâtons-tactiques)
4. [Système 4 : L'Hôtel des Ventes (HDV) & Les Ordres d'Achat Boursiers](#-système-4--lhôtel-des-ventes-hdv--les-ordres-dachat-boursiers)
5. [Système 5 : La Warzone Dynamique (Topographie, KOTH, Totem & Airdrops)](#-système-5--la-warzone-dynamique-topographie-koth-totem--airdrops)
6. [Système 6 : La Farmland & L'Agriculture Industrielle Moddée](#-système-6--la-farmland--lagriculture-industrielle-moddée)
7. [Système 7 : Les Forteresses de Faction, Défenses & Mécaniques de Raid](#-système-7--les-forteresses-de-faction-défenses--mécaniques-de-raid)
8. [Système 8 : Le Grand Système d'Enchantements Moddés (Custom Enchants)](#-système-8--le-grand-système-denchantements-moddés-custom-enchants)
9. [Système 9 : Les Donjons Instanciés & Boss de Donjons](#-système-9--les-donjons-instanciés--boss-de-donjons)
10. [Système 10 : L'Immersion Sensorielle, Animations & VFX Bedrock](#-système-10--limmersion-sensorielle-animations--vfx-bedrock)
11. [Système 11 : Le Réseau de PNJ Interactifs & Marchands Vivants](#-système-11--le-réseau-de-pnj-interactifs--marchands-vivants)
12. [Système 12 : Le Système de Quêtes Scénarisées & Contrats d'Ordre (`/quests`)](#-système-12--le-système-de-quêtes-scénarisées--contrats-dordre-quests)
13. [Système 13 : L'Architecture Multi-Mondes & Réseau de Téléportation Céleste](#-système-13--larchitecture-multi-mondes--réseau-de-téléportation-céleste)
14. [Système 14 : Le Système d'Échange Sécurisé Joueur-Joueur (`/trade`)](#-système-14--le-système-déchange-sécurisé-joueur-joueur-trade)
15. [Système 15 : Les Outils d'Immersion Sociale (Leaderboards Holographiques, Canaux de Chat & Morts Épiques)](#-système-15--les-outils-dimmersion-sociale-leaderboards-holographiques-canaux-de-chat--morts-épiques)
16. [Annexe Technique : Le Grand Répertoire des Blocs, Entités & Animations Bedrock](#-annexe-technique--le-grand-répertoire-des-blocs-entités--animations-bedrock)

---

## 🎣 Système 1 : La Pêche Céleste & Abyssale (Mini-Jeu au Timing & Léviathans)

La pêche devient une activité active, technique et spectaculaire qui récompense les réflexes plutôt que l'attente passive.

```
                   MINI-JEU DE FERRAGE DYNAMIQUE (ACTIONBAR)
          [ ░░░░░░░░░░░░░░░░░░░░░[  VERT  |★|  VERT  ]░░░░░░░░░░░░░░░░░░░░ ]
                                         ▲
                           Curseur de tension oscillant
```

### A. Mécanique de Ferrage au Timing (ActionBar Interactive)
1. Dès que le bouchon vibre et s'enfonce dans l'eau, le mini-jeu se déclenche pendant une fenêtre de **2.5 secondes**.
2. Une jauge d'oscillation apparaît dans l'ActionBar du joueur. Un curseur se déplace rapidement de gauche à droite.
3. Le joueur doit faire un clic droit au moment exact où le curseur traverse la **Zone Verte** :
   * **Ferrage Manqué (Zone Grise) :** L'appât est perdu, le poisson s'échappe.
   * **Ferrage Réussi (Zone Verte) :** Prise standard du poisson ou de l'objet.
   * **Ferrage Parfait (Zone Dorée ★ au centre) :** Butin doublé + chance de déclencher un trésor englouti.

### B. Les 4 Cannes à Pêche Spécialisées
* **Canne en Bambou Tressé (T1) :** Canne basique. Zone verte large ($30\%$ de la barre). Attrape des poissons comestibles ordinaires.
* **Canne Tellurique à Fil d'Acier (T2) :** Équipée d'un plomb en basalte. **Permet de pêcher dans la lave** des biomes magmatiques (poissons de feu, pépites d'or, fragments de magma).
* **Canne de Sève Céleste (T3) :** Imprégnée d'essences végétales. Zone verte plus étroite ($15\%$). Attire les poissons d'Aether conférant des potions instantanées.
* **Harpon Abyssal Primordial (T4) :** Zone dorée ultra-étroite ($5\%$). Permet de harponner des **Trésors Rares Scellés** et des fragments d'Opale.

### C. Les Prises Rares & Boss Léviathan
* **Poissons-Consommables d'Urgence :**
  * *Anguille Boréale :* Consommation instantanée ➔ `Speed III` pendant 15 secondes.
  * *Carpe d'Améthyste :* Restaure instantanément $40\%$ de la barre de posture.
  * *Piranha Magmatique :* Cuisiné en festin ➔ Immunité au feu pendant 10 minutes.
* **L'Évocation de Léviathan :**
  * Pêcher avec un appât légendaire dans le biome *Abysse Luminescent* a $2\%$ de chances de ferrer un **Léviathan des Profondeurs** (mini-boss marin à 5 000 HP qui jaillit de l'eau et doit être vaincu au harpon).

---

## 🧬 Système 2 : L'Élevage Spécial & La Génétique Vivante (ADN & Bêtes de Guerre)

L'élevage traditionnel est remplacé par une discipline de bio-ingénierie où chaque bête est façonnée selon les besoins de la faction.

```
                 CYCLE D'HYBRIDATION GÉNÉTIQUE DES CRÉATURES
     [ ANIMAL MÂLE ]    +    [ ANIMAL FEMELLE ]    +    [ CATALYSEUR D'ORDRE ]
                                                                 │
                                                                 ▼
                                                  [ DESCENDANCE SUPÉRIEURE ]
                                                  (Gènes améliorés & Mutations)
```

### A. Les Catalyseurs de Reproduction
Lorsqu'un joueur fait reproduire deux bêtes, il peut leur donner un **Catalyseur Spécial** préparé par l'Apothicaire :
1. **Feuille d'Opale Cristalline :** Confère à la portée le gène *Vigueur Céleste* ($+50\%$ de points de vie et pelage lumineux).
2. **Poudre Tellurique Dense :** Confère le gène *Cuirasse* (l'animal subit $-30\%$ de dégâts d'explosions et de flèches).
3. **Sève d'Élite d'Astrale :** Confère le gène *Vélocité Divine* (vitesse de sprint multipliée par $1.8$).

### B. Les 4 Bêtes Spéciales d'Opal Heaven
* **Le Destrier de Guerre Caparaçonné :**
  * Monture exclusive des guerriers de l'Ordre du Sang.
  * Équipé d'une armure en fer d'Aether. En sprint, il renverse les fantassins ennemis en infligeant $15\%$ de posture et du recul.
* **Le Molosse de Garde de Territoire :**
  * Loup dressé qui patrouille de manière autonome dans le rayon du Cœur de faction.
  * Attaque automatiquement les joueurs sans grade d'alliance dès qu'ils pénètrent sur le claim, leur appliquant `Slowness II` et un saignement de $1\text{ demi-cœur / s}$.
* **Le Ravageur de Bât à Deux Coffres :**
  * Bête de somme colossale équipée de deux grands coffres ($54\text{ slots}$ cumulés).
  * Vitesse lente, mais permet de transférer des coffres-forts entiers lors des déménagements de faction.
* **La Vache Alchimique Boréale :**
  * Nourrie à la verveine céleste, elle se trait avec un seau doré.
  * Son lait purifie immédiatement tous les effets négatifs (poisons, faiblesses, lenteurs) et confère **30 secondes d'immunité totale** aux nouveaux malus.

---

## 🛡️ Système 3 : Les 5 Tiers d'Armures Moddées & Bâtons Tactiques

Pour garantir un équilibre compétitif sans uniformisation, chaque palier d'armure possède une identité propre qui influe sur la vitesse et la posture.

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                        LES 5 TIERS D'ARMURES D'OPAL HEAVEN                             │
├────────┬─────────────────────────┬──────────────┬─────────────┬────────────────────────┤
│ Tier   │ Nom du Set              │ Niveau Requis│ Vitesse Move│ Impact sur la Parade   │
├────────┼─────────────────────────┼──────────────┼─────────────┼────────────────────────┤
│ **T1** │ **Cuir Céleste & Fer**  │ Niveau 1     │ 100% (Base) │ Fenêtre standard (0.45s│
│ **T2** │ **Quartz & Obsidienne** │ Niveau 15    │ 97% (-3%)   │ Résistance au recul +20│
│ **T3** │ **Lapis Tellurique**    │ Niveau 28    │ 95% (-5%)   │ Coût posture parade -15│
│ **T4** │ **Rubis du Sang**       │ Niveau 40    │ 98% (-2%)   │ Vol de vie mêlée +10%  │
│ **T5** │ **Opale Pure d'Aether** │ Niveau 50    │ 102% (+2%)  │ Réduction cooldowns -15│
└────────┴─────────────────────────┴──────────────┴─────────────┴────────────────────────┘
```

### Les Bâtons d'Action Tactiques (Action Sticks)
Inspirés des outils de combat emblématiques d'Histeria, craftés à base de fragments d'Opale :
* **Bâton de Guérison (Heal Stick) :** Possède 3 charges. Clic droit = restaure immédiatement 4 cœurs de vie. Cooldown de 45s entre chaque charge.
* **Bâton de Vitesse (Speed Stick) :** 5 charges. Octroie `Speed II` pendant 15 secondes (cooldown 30s).
* **Bâton de Répulsion (Force Stick) :** 3 charges. Libère une onde de choc repoussant tous les assaillants à 6 blocs. Outil vital pour briser un encerclement 1v3.
* **Bâton de Brèche (Breach Stick) :** 2 charges. Utilisable uniquement en période de siège (18h-23h) pour désactiver une tourelle arcanique ennemie pendant 20 secondes.

---

## 💎 Système 4 : L'Hôtel des Ventes (HDV) & Les Ordres d'Achat Boursiers

L'économie d'Opal Heaven repose sur un modèle d'échange asynchrone pour que les joueurs de fuseaux horaires différents commercent équitablement.

### A. L'Interface Tactile `/ah`
* **Navigation par Onglets Visuels :**
  * 🗡️ *Armes & Armures*
  * ⛏️ *Minerais, Lingots & Gemmes*
  * 🌾 *Agriculture, Semences & Festins*
  * 📜 *Parchemins d'Enchantement & Livres*
  * 📦 *Matériaux de Construction & Blocs Fortifiés*
* **Règles Anti-Spam :** Frais de dépôt fixes de **2% de la valeur de mise en vente** (non remboursés si l'objet n'est pas vendu). Durée d'exposition maximale de **48 heures**.

### B. Le Système d'Ordres d'Achat (Buy Orders)
Plutôt que d'attendre qu'un vendeur apparaisse, les acheteurs peuvent créer des ordres automatiques :
1. Un guerrier a besoin de 300 lingots de fer pour son armée.
2. Il tape `/ah buyorder` et dépose **900 Pièces d'Or** (3 Or par lingot). L'argent est bloqué par le serveur.
3. Dès qu'un mineur extrait du fer, il ouvre l'HDV, clique sur *« Remplir l'Ordre »* et encaisse les 900 pièces instantanément.

---

## 🔥 Système 5 : La Warzone Dynamique (Topographie, KOTH, Totem & Airdrops)

La Warzone est une arène vivante de 400 blocs de rayon autour du spawn, conçue pour l'action non-stop :

```
                        SCHÉMA TACTIQUE DE LA WARZONE
           ┌────────────────────────────────────────────────────────┐
           │     PLATEFORME DU SPAWN (Sanctuaire Invulnérable)      │
           └───────────────────────────┬────────────────────────────┘
                                       │ 4 Ponts de saut
                                       ▼
           ┌────────────────────────────────────────────────────────┐
           │                 LES TRANCHÉES DE RUINES                │
           │           Zones de repli, galeries souterraines        │
           └───────────────────────────┬────────────────────────────┘
                                       │
                                       ▼
           ┌────────────────────────────────────────────────────────┐
           │               LE CRATÈRE CENTRAL DE L'OPALE            │
           │        ★ ZONE DE CAPTURE KOTH (Tous les soirs 19h) ★    │
           │        ★ TOTEM DE CONQUÊTE (Contrôle territorial) ★    │
           └────────────────────────────────────────────────────────┘
```

1. **Le KOTH Quotidien (King of the Hill - 19h00) :**
   * Durée de capture : **4 minutes consécutives**.
   * Si l'occupant est éjecté ou tué, le chronomètre redescend.
   * *Butin :* Clé de Caisse Mythique de Warzone + 15 000 Or de guilde.
2. **Le Totem de Faction :**
   * Situé au flanc est de la warzone. Frapper le totem transfère des points d'influence à la faction attaquante. La faction détentrice gagne $+15\%$ d'expérience générale.
3. **Les Largages d'Airdrops Célestes :**
   * Toutes les 2 heures, une traînée de fumée dorée apparaît dans le ciel.
   * Un coffre descend lentement en parachute pendant 120 secondes, créant un rassemblement de mêlée générale pour piller les gemmes et armures T4 à l'atterrissage.

---

## 🌾 Système 6 : La Farmland & L'Agriculture Industrielle Moddée

Pour rivaliser avec les fermes de Paladium, Opal Heaven intègre une machinerie agricole complète :

1. **Les 4 Niveaux d'Arrosoirs Arcaniques (Watering Cans) :**
   * *Arrosoir en Bois :* Zone $1 \times 1$, vitesse x1.5.
   * *Arrosoir en Fer :* Zone $3 \times 3$, vitesse x2.0.
   * *Arrosoir en Diamant :* Zone $5 \times 5$, vitesse x3.0.
   * *Arrosoir d'Opale Pure :* Zone $9 \times 9$, vitesse x4.0 avec $30\%$ de chances de déclencher une mutation spontanée.
2. **Le Composteur d'Accélération :**
   * Transforme 64 résidus végétaux en 1 unité de **Poussière Fertile Céleste**.
   * Appliquée sur une plante, elle fait mûrir instantanément n'importe quelle culture, y compris les verrues du Nether.
3. **Les Graines de Minerais Hybrides (Magical Crops) :**
   * Cultivables uniquement sur terre enrichie au composteur :
     * *Graines de Fer :* Rendement en pépites de fer.
     * *Graines d'Or :* Rendement en pépites d'or.
     * *Graines de Diamant :* Rendement en éclats de diamant (4 éclats = 1 diamant).
     * *Graines d'Opale :* Pousse lente (45 min), nécessite un bloc d'améthyste sous la terre.

---

## 🏰 Système 7 : Les Forteresses de Faction, Défenses & Mécaniques de Raid

La guerre de territoire est codifiée avec des blocs défensifs à résistance mesurée :

1. **Les Blocs de Construction Fortifiés :**
   * **Pierre Fortifiée :** Encaisse **3 détonations de TNT** avant destruction.
   * **Obsidienne Dense de Faille :** Encaisse **5 détonations directes de TNT**.
   * **Verre Blindé d'Aether :** Transparent, résistant aux explosions de niveau 1 et incassable aux flèches.
2. **Les Pièges Intérieurs :**
   * **Piques d'Acier au Sol (Spikes) :** Infligent 3 cœurs de dégâts et un saignement à tout intrus.
   * **Balise Anti-Perles (Ender-Disruptor) :** Annule l'atterrissage de toute perle d'ender dans un rayon de 16 blocs autour du coffre de faction.
   * **Dalles d'Électrocution :** Projettent une décharge faisant monter la jauge de posture de l'assaillant de $+40\%$.
3. **L'Artillerie de Siège (18h-23h uniquement) :**
   * **TNT de Brèche (Breach TNT) :** Inflige des dégâts quadruplés aux blocs fortifiés.
   * **Marteau de Béliers :** Permet d'enfoncer les portes de fer et herses de faction au corps-à-corps.

---

## 📖 Système 8 : Le Grand Système d'Enchantements Moddés (Custom Enchants)

Un catalogue d'enchantements avancés géré par des parchemins d'arcanes et des taux de réussite :

```
                  SYSTÈME D'APPLICATION DES PARCHEMINS
       [ PARCHEMIN ] ──► [ APPLICATION SUR L'ARME (ENCLUME) ]
                                   │
         ┌─────────────────────────┴─────────────────────────┐
         ▼                                                   ▼
  [ SUCCÈS (ex: 75%) ]                               [ ÉCHEC (ex: 25%) ]
  L'enchantement est lié                              • Si Protégé : le livre disparaît
  définitivement à l'arme                             • Si Non-Protégé : l'arme est DÉTRUITE
```

### Les Objets de Sécurisation d'Enchantement
* **Poussière de Clarté d'Opale :** Augmente le taux de succès d'un parchemin de $+5\%$ à $+20\%$.
* **Sceau de Sauvegarde Céleste :** Protège l'arme contre la destruction en cas d'échec de l'enchantement.

### Catalogue des 12 Enchantements Majeurs
1. **Lame d'Impact (Épée - Légendaire) :** Chaque coup critique inflige $+15\%$ de posture adverse en plus.
2. **Saignement Profond (Hache - Épique) :** Applique une hémorragie de 1 demi-cœur/s pendant 4 secondes.
3. **Vampirisme Sanguin (Dague - Épique) :** Convertit $15\%$ des dégâts causés en régénération directe.
4. **Garde d'Airain (Plastron - Légendaire) :** Réduit la durée du Stagger de 2.0s à 1.2s.
5. **Esquive Boréale (Bottes - Rare) :** $10\%$ de chance d'ignorer totalement un coup de flèche ennemi.
6. **Plumes d'Aether (Bottes - Commun) :** Annule les dégâts de chute jusqu'à 25 blocs.
7. **Foreuse Tellurique (Pioche - Épique) :** Mine en $3 \times 3$ en continu sans surcharge.
8. **Télékinésie Arcanique (Outils - Rare) :** Les blocs brisés vont directement dans l'inventaire.
9. **Moisson Doublée (Houe - Rare) :** $25\%$ de chances de doubler la récolte de semences d'élite.
10. **Frappe Foudroyante (Arc - Épique) :** Fait tomber un éclair céleste sur la cible à l'impact.
11. **Brise-Armure (Hache - Légendaire) :** Inflige une usure de durabilité x3 à l'armure de la victime.
12. **Résilience Sismique (Bouclier - Rare) :** Absorbe $50\%$ de la répulsion des explosions.

---

## 🗝️ Système 9 : Les Donjons Instanciés & Boss de Donjons

Pour enrichir le contenu PvE en groupe de 1 à 4 joueurs :

1. **Accès par Clés de Donjon :**
   * Trouvables dans les failles de biomes ou craftables avec 4 *Fragments Célestes*.
2. **Le Déroulement d'une Run Instanciée (Chronomètre 20 min) :**
   * *Salle 1 (Le Nettoyage) :* Éliminer 3 vagues de monstres d'élite.
   * *Salle 2 (L'Épreuve d'Agilité) :* Parcours de sauts sur dalles mouvantes au-dessus d'un précipice sans fond.
   * *Salle 3 (Le Boss de Donjon) :* Affrontement tactique avec patterns d'attaques nécessitant la parade.
3. **Les 3 Donjons de Saison :**
   * **La Crypte du Seigneur Sanglant :** Boss Vampire régénérant sa vie sur les joueurs qui ratent leur parade.
   * **Le Cœur de la Forge Tectonique :** Boss Golem de Basalte projetant des vagues de roche brûlante.
   * **La Canopée de l'Arbre Primordial :** Boss Reine des Ronces invoquant des racines entraînantes.
4. **Récompenses :** Plans de craft d'armures de Tier 5, gemmes primordiales et inscription au tableau des records de vitesse (Speedrun).

---

## ✨ Système 10 : L'Immersion Sensorielle, Animations & VFX Bedrock

Pour offrir une sensation de jeu AAA sur Minecraft Bedrock, le serveur sublime chaque action par des retours haptiques, visuels et sonores précis, tout en respectant scrupuleusement les contraintes de performance des clients mobiles et consoles.

```
                    CHAÎNE DE RETOUR SENSORIEL (PARADE & POSTURE)
   [ ACTION JOUEUR ] ──► [ TRAITEMENT SERVEUR ] ──► [ PACKETS D'IMMERSION CLIENT ]
        (Sneak)             (Parade Parfaite)         │
                                                      ├──► SoundPacket (Enclume céleste)
                                                      ├──► SpawnParticleEffectPacket (Étincelles)
                                                      └──► Punch / Léger recul visuel
```

### A. Les Événements Clés et Leurs Signatures Visuelles & Sonores
1. **La Parade Parfaite (Perfect Parry) :**
   * **Son :** Mix combiné de `random.anvil_land` (pitch 1.85) et `beacon.activate` (pitch 2.0). Produit un son sec, cristallin et métallique très gratifiant.
   * **Particules :** Envoi d'un `SpawnParticleEffectPacket` avec `minecraft:sparkler_emitter` générant une gerbe d'étincelles dorées et azur dans un disque horizontal de rayon $r = 1.4\text{ m}$ à hauteur d'arme ($y + 1.1\text{ m}$).
   * **Retour Visuel :** Micro-recul optique côté attaquant pour lui faire ressentir le mur défensif qu'il vient de heurter.
2. **La Rupture de Posture (Posture Break / Stagger) :**
   * **Son :** Craquement lourd `entity.iron_golem.death` (pitch 0.75) suivi du tintement brisé `block.anvil.destroy` (pitch 1.1).
   * **Secousse Caméra (Bedrock CameraShakePacket) :** Envoi direct d'un `CameraShakePacket` (`shakeType: ShakeType::POSITIONAL`, `intensity: 0.30`, `duration: 1.0s`) au joueur étourdi. Le joueur subit un tremblement physique immersif qui symbolise sa perte d'équilibre.
   * **Particules :** Nuée d'étoiles dorées étourdissantes (`minecraft:villager_angry` customisé ou `minecraft:electric_spark_particle`) gravitant en couronne orbitale 0.5 bloc au-dessus de sa tête pendant les 40 ticks de Stagger.
3. **L'Exécution Critique de Finisher :**
   * Lorsque l'adversaire est en Stagger et subit un coup critique : explosion d'éclats cramoisis (`minecraft:huge_explosion_emitter` teinté sang) et onde de choc au sol.
4. **La Canalisation de Bâton Tactique :**
   * Rayonnement d'anneaux de particules concentriques convergeant vers les mains du joueur avec son de bourdonnement arcanique montant en fréquence.

### B. Optimisation Client & Throttling Mobile (Anti-Lag 60 FPS)
* **Plafond Dynamique de Particules :** Limite stricte de **40 particules/seconde par joueur** calculée côté serveur. Si une mêlée de 10 joueurs génère trop d'impacts simultanés, le serveur bascule automatiquement en mode LOD (Level of Detail) : les particules secondaires des joueurs distants de plus de 8 blocs sont masquées.
* **Zéro Entité Inutile :** Aucun recours aux armor stands invisibles superflus pour afficher des effets qui peuvent être rendus nativement par paquets de particules éphémères.

### C. Animations Personnalisées (Bedrock Custom Geometry)
* **Animation de Garde au Sneak :** L'épée se relève horizontalement devant le buste à deux mains, remplaçant la posture classique de furtivité par une garde défensive de dueliste.
* **Animation de Mort Glorieuse :** Au lieu de la simple bascule latérale rouge vanilla, le joueur vaincu se transforme en une silhouette d'énergie opale qui s'élève vers le ciel avant de se dissiper en poussière stellaire.

---

## 🧙 Système 11 : Le Réseau de PNJ Interactifs & Marchands Vivants

Le Hub et les sanctuaires d'Opal Heaven ne sont pas de simples décors froids : ils sont peuplés de personnages non-joueurs (PNJ) vivants, dotés de répliques narratives et d'interfaces immersives.

```
                   CYCLE D'INTERACTION AVEC UN PNJ CÉLESTE
         [ JOUEUR ] ──── (Clic Droit / Tap Écran) ────► [ PNJ VIVANT ]
                                                             │
         ┌───────────────────────────────────────────────────┘
         ▼
   [ FORMUI MODAL AVEC AVATAR DU PNJ ]
   • Dialogue narratif & lore immersif
   • Choix d'action 1 : "Ouvrir la Forge"
   • Choix d'action 2 : "En savoir plus sur l'Ordre"
   • Choix d'action 3 : "Prendre congé"
```

### A. Anatomie & Réalisme des Entités PNJ
* **Modèles & Textures HD :** Entités `CustomHuman` équipées de skins personnalisés 128x128 représentant l'iconographie des 3 Ordres Célestes.
* **Comportement Vivant (Idle Animation) :**
  * Suivi du regard : La tête du PNJ pivote de manière fluide vers le joueur le plus proche dans un rayon de 7 blocs.
  * Respiration et micro-mouvements : Légère ondulation des épaules et balancement naturel pour éviter l'effet "statue de cire".
* **Interaction Instantanée :** Le clic droit ouvre immédiatement une interface FormUI native sans délai de chargement.

### B. Le Panthéon des 6 PNJ Majeurs du Monde Céleste
1. **Sire Gabriel, Le Paladin Déchu (Maître des Ordres - Sanctuaire Central) :**
   * *Rôle :* Guide d'accueil des nouveaux arrivants, transmission du Lore de la Faille Céleste, réinitialisation de spécialisation (coût : 50 000 pièces ou 1 Jeton d'Ordre).
   * *Phrase d'accueil :* *"Que la lumière de l'Opale guide tes pas, voyageur. Quel serment ton âme est-elle prête à prêter ?"*
2. **Thorgar le Forgeron des Abysses (Maître Forgeron & Enclume Vivante) :**
   * *Rôle :* Réparation d'équipements de tiers avancés contre des fragments de minerais, application sécurisée des parchemins d'enchantement, extraction d'orbes d'essence sur du matériel recyclé.
   * *Phrase d'accueil :* *"Approche ton acier de ma forge céleste. Prends garde, le feu de l'abysse ne pardonne pas la maladresse."*
3. **Dame Sylvana, Gardienne des Moissons (Marchande de la Nature) :**
   * *Rôle :* Échange de semences moddées, rachat des récoltes de graines hybrides d'élite au cours du jour, vente de Poussière Fertile et d'outils d'arrosage.
   * *Phrase d'accueil :* *"La terre chante sous les sabots des bêtes. Apporte-moi tes moissons géantes et reçois la bénédiction végétale."*
4. **Cassandra la Banquière Céleste (Coffres Sécurisés & Bourse) :**
   * *Rôle :* Accès aux coffres de dépôt personnels inviolables (Safe Deposit Boxes de 9 à 54 slots) et gestion des ordres d'achat boursiers de l'HDV.
   * *Phrase d'accueil :* *"Ici, votre fortune est plus en sécurité que dans le donjon le plus imprenable. Déposez, empruntez, prospérez."*
5. **Zephyr le Marchand Nomade (The Wandering Celestial) :**
   * *Rôle :* **PNJ Événementiel.** Apparaît aléatoirement toutes les 4 heures dans l'un des 40 biomes moddés de l'Overworld pendant une durée stricte de 45 minutes (annoncé par un message broadcast avec coordonnées partielles).
   * *Boutique Exclusive :* Vend 3 objets mythiques en quantité très limitée (ex: Parchemin de Garde d'Airain 100% de réussite, Clé de Donjon Primordiale, Graine d'Opale Légendaire).
6. **Eldrin l'Archiviste des Quêtes (Grand Maître du Savoir) :**
   * *Rôle :* Gestionnaire du carnet de bord (`/quests`), validation des exploits de faction et distribution des récompenses de prestige saisonnier.

---

## 📜 Système 12 : Le Système de Quêtes Scénarisées & Contrats d'Ordre (`/quests`)

Pour guider les débutants et dynamiser le quotidien des vétérans, le système de quêtes combine narration immersive, défis quotidiens et accomplissements de guilde.

```
                      ARBORESCENCE DU SYSTÈME DE QUÊTES
                              [ COMMANDE /quests ]
                                       │
        ┌──────────────────────────────┼──────────────────────────────┐
        ▼                              ▼                              ▼
 [ LA VOIE INITIATIQUE ]    [ CONTRATS DU JOUR ]         [ DÉFIS DE FACTION ]
  10 Chapitres de Lore       3 Objectifs quotidiens       Milestones de Guilde
  Découverte progressive     Adaptés à votre Ordre        Bonus de Tech & Territoire
```

### A. La Voie Initiatique (Trame Scénarisée Débutant en 10 Chapitres)
Cette campagne scénarisée accompagne le joueur sans jamais l'enfermer dans un carcan rigide :
1. **Chapitre 1 — Le Choix du Destin :** Sélectionner son Ordre et réussir 3 parades parfaites contre le Mannequin d'Entraînement du Hub. *(Récompense : 1 000 pièces + Armure en Cuir Renforcé T1)*.
2. **Chapitre 2 — L'Écho des Failles :** Franchir le Portail de l'Overworld et extraire 32 blocs de Cobalt dans un biome moddé. *(Récompense : Pioche en Acier T2 + 250 XP d'Ordre)*.
3. **Chapitre 3 — Le Premier Sanctuaire :** Fonder ou rejoindre une Faction et participer au dépôt du premier bloc de Cœur de Guilde. *(Récompense : 1 Clé de Box Céleste)*.
4. **Chapitre 4 — L'Art du Commerce :** Mettre en vente un objet à l'Hôtel des Ventes ou effectuer un échange réussi via `/trade`. *(Récompense : 2 500 pièces)*.
5. **Chapitre 5 — La Domestication Primordiale :** Capturer ou reproduire une bête avec un gène supérieur à $60\%$. *(Récompense : Seringue d'Extraction Génétique)*.
6. **Chapitre 6 — L'Appel des Moissons :** Produire un engrais céleste au composteur et faire pousser une graine hybride. *(Récompense : Arrosoir en Fer T2)*.
7. **Chapitre 7 — La Forge Arcanique :** Appliquer son premier parchemin d'enchantement avec une poussière de clarté. *(Récompense : 1 Sceau de Sauvegarde)*.
8. **Chapitre 8 — L'Épreuve du Donjon :** Compléter une run d'un Donjon Instancié de Tier 1 avec son escouade. *(Récompense : 1 Gemme Primordiale)*.
9. **Chapitre 9 — Le Sang de la Warzone :** Infliger 500 points de dégâts de posture en zone hostile et collecter 3 Marques du Sang. *(Récompense : Épée en Titane T3)*.
10. **Chapitre 10 — L'Apogée Céleste :** Atteindre le niveau 20 de son Ordre et débloquer son premier passif d'élite. *(Récompense : Titre exclusif « Champion de l'Opale » + 25 000 pièces)*.

### B. Les Contrats Quotidiens d'Ordre (Daily Bounties)
Chaque jour à minuit, 3 nouveaux contrats sont générés selon l'Ordre choisi par le joueur :
* **Guerriers du Sang :**
  * *Contrat 1 :* Éliminer 5 joueurs ennemis dans la Warzone.
  * *Contrat 2 :* Réaliser 5 parades parfaites au cours de combats PvP réels.
  * *Contrat 3 :* Participer à la capture d'un Totem ou d'un KOTH.
* **Mineurs des Abysses :**
  * *Contrat 1 :* Extraire 128 blocs de minerais moddés dans le Monde Minage.
  * *Contrat 2 :* Raffiner 16 lingots de Titane ou d'Opale à la fonderie.
  * *Contrat 3 :* Vendre pour 10 000 pièces de minerais bruts via l'HDV.
* **Druides de la Terre :**
  * *Contrat 1 :* Arroser 300 cultures moddées avec un arrosoir arcanique.
  * *Contrat 2 :* Extraire 3 doses d'ADN animal à la seringue génétique.
  * *Contrat 3 :* Moissonner 64 cultures d'arbres ou de graines hybrides.

### C. Règles Anti-Triche & Anti-Farm sur les Quêtes
* **Anti-Kill Farming :** Deux éliminations du même joueur (même XUID ou même adresse IP) dans un intervalle de 30 minutes ne comptent qu'une seule fois.
* **Marquage Anti-Mineur :** Tout bloc placé par un joueur (`PlayerPlaced: true`) est disqualifié des quêtes de minage.
* **Anti-AFK Farming :** Les récoltes effectuées par des systèmes automatisés sans interaction directe du joueur ne font pas progresser les quêtes de récolte.

---

## 🌌 Système 13 : L'Architecture Multi-Mondes & Réseau de Téléportation Céleste

Pour préserver la fluidité du serveur, séparer les usages et éviter que le monde de faction ne soit défiguré par des carrières géantes de minage, Opal Heaven déploie une architecture en 5 mondes interconnectés.

```
                   CARTE DU RÉSEAU DES MONDES OPAL HEAVEN
                                  [ HUB CÉLESTE ]
                                  (Sanctuaire 0 PvP)
                                         │
        ┌───────────────────┬────────────┴────────────┬───────────────────┐
        ▼                   ▼                         ▼                   ▼
 [ OVERWORLD FACTION ] [ WARZONE ACTIVE ]      [ MONDE MINAGE ]    [ ROYAUME DONJONS ]
 10 000 x 10 000        1 500 x 1 500          5 000 x 5 000       Instances PvE
 40 Biomes Moddés       PvP Hardcore           Reset mensuel       Chronométrées
 Bases & Territoires    KOTH & Totems          Ressources brutes   Groupes de 1-4
```

### A. Rôle Spécifique de Chacun des 5 Mondes
1. **Le Hub Céleste (`hub`) :**
   * *Propriétés :* Zone sanctuaire flottante. Dégâts désactivés, faim gelée, inventaires protégés.
   * *Contenu :* Mannequins d'entraînement pour tester sa parade, l'ensemble des PNJ vivants, la Grande Bourse, la Banque et les Portails Dimensionnels.
2. **L'Overworld de Faction (`opal_main`) :**
   * *Dimensions :* $10\,000 \times 10\,000$ blocs générés avec les 40 biomes moddés exclusifs.
   * *Règles :* C'est le monde permanent de construction, de conquête de claims, d'agriculture et de guerres de bases. Aucune réinitialisation de terrain en cours de saison.
3. **La Warzone Sanglante (`warzone`) :**
   * *Dimensions :* $1\,500 \times 1\,500$ blocs.
   * *Règles :* Aucun claim possible. Perte de stuff intégrale en cas de mort. Terrain accidenté, Forteresses KOTH, Totem de Faction et Airdrops réguliers.
4. **Le Monde Minage & Ressources (`mining_world`) :**
   * *Dimensions :* $5\,000 \times 5\,000$ blocs.
   * *Régénération Mensuelle :* Ce monde est **entièrement réinitialisé le 1er de chaque mois**. Cela garantit des gisements de minerais neufs en continu et protège l'Overworld principal contre les tranchées de minage disgracieuses.
5. **Le Sanctuaire des Donjons (`dungeon_realm`) :**
   * *Nature :* Monde instancié hébergeant les salles de donjon PvE fermées et sécurisées par groupe.

### B. Mécanique de Téléportation & Portails Stellaires
* **Portails Visuels du Hub :** Anneaux de blocs d'opale sculptée encadrant un vortex de particules lumineuses. Un hologramme suspendu au-dessus affiche en temps réel :
  * Le nom du monde cible.
  * Le nombre de joueurs actifs dans la dimension (`ex: Warzone : 42 joueurs`).
  * L'état du monde (ex: `KOTH en cours !` ou `Reset dans 12 jours`).
* **Protocoles des Commandes `/spawn`, `/f home`, `/rtp` :**
  * **Canalisation Immobile :**
    * Zone Sécurisée : **3 secondes** de canalisation sans bouger.
    * Zone Sauvage / Hostile : **5 secondes** de canalisation sans bouger.
  * **Affichage de Progression :** L'ActionBar affiche une jauge dynamique :  
    `[ ⏳ Téléportation céleste dans 3s... Restez immobile ! ]`
  * **Interruption Immédiate :** Tout déplacement supérieur à 0.5 bloc, tout dégât subi ou ouverture d'inventaire annule immédiatement la téléportation avec un son d'échec (`note.bass`).
  * **Interdiction Absolue en Combat :** Impossible d'initier une téléportation sous l'état de **Combat-Tag** (15 secondes après un coup infligé ou reçu).

---

## 🤝 Système 14 : Le Système d'Échange Sécurisé Joueur-Joueur (`/trade`)

Pour en finir définitivement avec les arnaques au drop d'objets, les vols par perles d'ender ou les déconnexions malhonnêtes, Opal Heaven met en place un système de troc direct sécurisé basé sur l'interface FormUI native Bedrock.

```
                     PROTOCOLE DE TRANSACTION SÉCURISÉE
   [ JOUEUR A ] ─── (/trade JoueurB) ───► [ NOTIFICATION / ACCEPTATION ]
        │                                           │
        ▼                                           ▼
   [ SÉLECTION DES ITEMS (max 6) ]             [ SÉLECTION DES ITEMS (max 6) ]
   [ MONTANT DE PIÈCES CÉLESTES  ]             [ MONTANT DE PIÈCES CÉLESTES  ]
        │                                           │
        ▼                                           ▼
   [ CLIC SUR « PRÊT » ]                       [ CLIC SUR « PRÊT » ]
        │                                           │
        └─────────────────────┬─────────────────────┘
                              ▼
        [ ANTI-SWITCH : VÉRIFICATION D'INTÉGRITÉ 3 SECONDES ]
        (Si une offre change, verrouillage & reset obligatoire)
                              │
                              ▼
          [ VALIDATION FINALE & TRANSACTION ATOMIQUE SQL/RAM ]
```

### A. Conditions Préalables à l'Échange
1. **Proximité Physique :** Les deux joueurs doivent se trouver à une distance maximale de **6 blocs** l'un de l'autre au moment du lancement.
2. **Statut Hors Combat :** Aucun des deux joueurs ne doit avoir frappé ou été frappé lors des 15 dernières secondes (`CombatTag == false`).
3. **Espace Libre dans l'Inventaire :** Le système calcule dynamiquement si les inventaires des deux joueurs disposent de suffisamment de slots libres pour recevoir les objets échangés avant de valider la transaction.

### B. Les 5 Étapes du Protocole `/trade`
1. **L'Initiation :** Le joueur A tape `/trade <Pseudo>`. Le joueur B reçoit un message sonore doux (`random.orb`) et une invite cliquable dans le chat :  
   `[ 🤝 Bastien vous propose un échange sécurisé. Cliquez ici ou tapez /trade accept ]`
2. **La Constitution de l'Offre :**
   * Chaque joueur sélectionne jusqu'à **6 objets** de son inventaire.
   * Chaque joueur peut spécifier une somme d'argent (Monnaie Céleste) transférée de son portefeuille vers celui de l'autre.
3. **L'Inspection Précise :**
   * L'interface affiche en miroir l'offre complète de l'adversaire : noms d'objets personnalisés, liste exhaustive des enchantements moddés, niveau de durabilité restant en points et pourcentages.
4. **Le Verrouillage « Prêt » & Sécurité Anti-Switch :**
   * Lorsque le joueur A est satisfait, il clique sur le bouton **« Prêt »**.
   * **Bouclier Anti-Arnaque :** Si le joueur B retire un objet, change un montant ou remplace un livre d'enchantement par un autre, **le statut « Prêt » des deux joueurs s'annule instantanément**, l'écran clignote en rouge avec un son d'alerte, et un **compte à rebours de sécurité de 3 secondes** est imposé avant que quiconque ne puisse re-cliquer sur « Prêt ».
5. **La Confirmation Finale & Swap Atomique :**
   * Dès que les deux joueurs ont validé leur état « Prêt », un bouton de confirmation finale apparaît.
   * Au double-clic de validation, l'échange s'exécute en **transaction atomique** : les inventaires et soldes monétaires sont intervertis en mémoire vive simultanément avec sauvegarde immédiate dans la base de données.
   * Si une déconnexion intempestive survient à la milliseconde de l'échange, **la transaction est rollback** : chaque joueur conserve strictement ses biens d'origine. Aucun dupe ni perte d'item possible.

---

## 🏆 Système 15 : Les Outils d'Immersion Sociale (Leaderboards Holographiques, Canaux de Chat & Morts Épiques)

La dimension communautaire d'un grand serveur repose sur la clarté de sa communication et la mise en gloire des exploits de ses joueurs.

```
                     HIÉRARCHIE DES CANAUX DE COMMUNICATION
       PREFIXE        CANAL               PORTÉE             COULEUR
       ───────        ─────               ──────             ───────
       (aucun / !)    Global              Tout le serveur    §f Blanc / Gris
       $              Local               Rayon de 30 blocs  §e Jaune pâle
       @              Faction             Membres de guilde  §a Vert Émeraude
       #              Alliance            Factions alliées   §b Bleu Céleste
       /r /msg        Privé               Joueur à joueur    §d Rose Arcanique
```

### A. Les Leaderboards Holographiques Flottants du Hub
Au cœur de la place centrale du Hub Céleste, des textes flottants 3D (Floating Text Entities) se mettent à jour automatiquement toutes les 60 secondes :
1. **L'Hologramme du Panthéon des Factions :**
   * Affiche le Top 5 des factions de la saison selon leurs points de Tech, victoires KOTH et valeur de territoire.
2. **L'Hologramme des Gladiateurs Célestes :**
   * Classement des 5 meilleurs duellistes selon leur ratio K/D en Warzone, série de victoires (Killstreak) et nombre de parades parfaites réussies.
3. **L'Hologramme des Titans de l'Industrie :**
   * Classement des 5 plus grands extracteurs de minerais et des plus riches négociants de l'HDV.

### B. Canaux de Communication Spécialisés
* **Canal Global (`!` ou message ordinaire) :** Pour les discussions générales. Équipé d'un cooldown de 2 secondes anti-spam et d'un filtre automatique anti-injures. Affiche le préfixe de l'Ordre et de la Faction :  
  `[Sang][Immortal] Bastien : GG pour le KOTH d'hier soir !`
* **Canal Local (`$` ou `/local`) :** Limité à un rayon de **30 blocs**. Permet de converser en toute discrétion autour d'une table d'enchantement ou dans une forteresse sans encombrer le chat global.
* **Canal de Faction (`@message`) :** Visible exclusivement par les membres de la même faction. Idéal pour coordonner un raid ou un appel de détresse.
* **Canal d'Alliance (`#message`) :** Visible par sa faction et toutes les factions officiellement alliées pour monter des opérations conjointes.

### C. Journal des Morts Épiques & Célébration des Séries d'Éliminations (Killstreaks)
* **Messages de Mort Contextualisés :**
  * Mort classique : `☠️ [Victime] est tombé sous la lame de [Tueur].`
  * Mort sur Stagger : `⚔️ [Tueur] a brisé la garde de [Victime] avant de l'exécuter d'un coup impitoyable !`
  * Mort par Piège de Faction : `⚡ [Intrus] s'est empalé sur les piques défensives de la forteresse de [Faction].`
* **Annonces Célestes de Killstreak :**
  * À **5 éliminations d'affilée :** Annonce locale + son de cor de guerre.  
    `🔥 [Bastien] est en Série Sanglante ! (5 éliminations sans mourir)`
  * À **10 éliminations :** Annonce serveur globale + éclair visuel sans dégâts au-dessus de sa position.  
    `⚡ [Bastien] est DEVENU UNE LÉGENDE ! Sa tête est mise à prix pour 10 000 pièces !`

---

## 🛠️ Annexe Technique : Le Grand Répertoire des Blocs, Entités & Animations Bedrock

Cette annexe technique dresse l'inventaire exhaustif de tous les composants personnalisés requis pour déployer l'infrastructure moddée d'Opal Heaven sur Minecraft Bedrock. Chaque élément est documenté avec son identifiant technique de ressource, ses propriétés physiques, son comportement de jeu et ses signatures visuelles.

```
                    ARCHITECTURE DES RESSOURCES MODDÉES BEDROCK
  ┌─────────────────────────┬─────────────────────────┬─────────────────────────┐
  ▼                         ▼                         ▼                         ▼
[ 35 BLOCS MODDÉS ]       [ 24 ENTITÉS MODDÉES ]    [ 18 ANIMATIONS BEDROCK ] [ FX & PARTICULES ]
• Minerais & Fonderie     • PNJ Vivants (FormUI)    • Stance Parade au Sneak  • CameraShakePacket
• Fortifications de Raid  • Boss de Donjons (Patterns)• Stagger & Finishers    • Éclats & Étincelles
• Agriculture Hybride     • Bêtes de Guerre (ADN)   • Vortex de Téléportation • Sons customisés
• Nexus & Balises         • Mannequin de Parade     • Battements du Cœur Nexus• Throttling 40 part/s
```

---

### 🧱 PARTIE I : LE CATALOGUE EXHAUSTIF DES BLOCS MODDÉS (35 BLOCS)

#### A. Minerais, Cristaux & Matériaux Primordiaux (9 Blocs)

| Identifiant Technique | Nom en Jeu | Dureté | Résistance TNT | Outil Requis | Drop Naturel | Lueur Émise | Propriétés Particulières |
| :--- | :--- | :---: | :---: | :---: | :--- | :---: | :--- |
| `opal:cobalt_ore` | **Minerai de Cobalt** | 3.5 | 6.0 | Pioche Acier (T2+) | 1× Cobalt Brut | 0 | Abondant dans les biomes de failles rocheuses. Nécessite une fusion à la Fonderie. |
| `opal:titanium_ore` | **Minerai de Titane** | 5.0 | 9.0 | Pioche Titane (T3+) | 1× Titane Brut | 0 | Génère exclusivement sous $y = 0$ dans les zones magmatiques et les cavernes profondes. |
| `opal:aetherite_ore` | **Minerai d'Aetherite** | 6.5 | 12.0 | Pioche Aetherite (T4+) | 1× Cristal d'Aetherite | Niveau 4 | Génère dans les îles célestes flottantes et les pics d'altitude ($y > 180$). |
| `opal:celestial_opal_ore` | **Minerai d'Opale Céleste** | 8.0 | 15.0 | Pioche d'Opale (T5) | 1× Gemme d'Opale Pure | Niveau 7 | Minerai légendaire ultra-rare (1 filon par chunk max). Effet sonore cristallin au minage. |
| `opal:stellar_amethyst` | **Amas d'Améthyste Stellaire** | 4.0 | 5.0 | Pioche Acier (T2+) | 2-4× Éclats Stellaires | Niveau 5 | Cristal résonnant indispensable sous la terre fertile pour faire mûrir les Graines d'Opale. |
| `opal:block_cobalt` | **Bloc de Cobalt** | 5.0 | 6.0 | Pioche Acier (T2+) | Soi-même (9 lingots) | 0 | Matériau de construction lourd et base pour activer les balises d'ordre de niveau 1. |
| `opal:block_titanium` | **Bloc de Titane** | 7.0 | 9.0 | Pioche Titane (T3+) | Soi-même (9 lingots) | 0 | Composant structurel des machineries et blindages de coffre de faction. |
| `opal:block_aetherite` | **Bloc d'Aetherite** | 8.5 | 12.0 | Pioche Aetherite (T4+) | Soi-même (9 cristaux)| Niveau 6 | Bloc résonnant réduisant le recul des explosions de $25\%$ dans son voisinage immédiat. |
| `opal:block_pure_opal` | **Bloc d'Opale Céleste** | 10.0 | 20.0 | Pioche d'Opale (T5) | Soi-même (9 gemmes)  | Niveau 12 | Bloc de prestige ultime, dégage une aura de particules scintillantes en continu. |

---

#### B. Machinerie Industrielle & Blocs Utilitaires (8 Blocs)

1. `opal:acceleration_composter` (**Composteur d'Accélération**) :
   * *Hitbox :* $1.0 \times 1.0 \times 1.0$ bloc. Modèle customisé avec trémie supérieure et rouages visibles.
   * *Fonctionnement :* Accepte n'importe quel déchet végétal (feuilles, graines, fleurs, herbes). Un compteur interne stocke jusqu'à 64 unités. À 64 unités, les rouages tournent (`animation.composter.churn`), un panache de vapeur verte jaillit, et 1 unité de **Poussière Fertile Céleste** est expulsée.
2. `opal:celestial_foundry` (**Fonderie Céleste**) :
   * *Hitbox :* $1.0 \times 1.0 \times 1.0$ bloc. Four tubulaire en titane et basalte chauffé au cœur d'améthyste.
   * *Fonctionnement :* Vitesse de fusion **triplée (x3.0)** par rapport à un haut-fourneau classique. Possède un réservoir de lave interne (capacité : 10 seaux) et permet d'affiner le Titane et l'Aetherite sans perte thermique. Clic droit = Interface FormUI de contrôle.
3. `opal:thorgar_anvil` (**Enclume d'Acier Céleste**) :
   * *Propriété unique :* **Incassable.** Ne subit aucune dégradation ou fêlure lors des réparations.
   * *Fonction :* Sert de support d'application pour les **Parchemins d'Enchantement Moddés**, avec prévisualisation des pourcentages de réussite ($50\%$ à $100\%$) et slot de sécurité pour insérer les Sceaux de Sauvegarde.
4. `opal:faction_heart` (**Cœur de Faction / Nexus de Guilde**) :
   * *Hitbox :* $1.0 \times 2.0 \times 1.0$ bloc (colonne cristalline biparti).
   * *Durabilité :* **1 000 Points de Structure (HP)**. Impossible à miner à la main ou à la pioche normale.
   * *Mécanique de Siège :* Subit des dégâts uniquement par explosions de TNT de Brèche ou Marteaux de Béliers entre 18h et 23h. Émet un dôme sphérique translucide délimitant la zone du claim. Si sa vie descend sous $25\%$, il déclenche une alarme sonore de cor de guerre entendue par tous les membres connectés.
5. `opal:dna_extractor_station` (**Poste d'Extraction Génétique**) :
   * *Apparence :* Table d'alchimie équipée de fioles d'incubation sous verre et d'une centrifugeuse à manivelle.
   * *Fonction :* Permet d'insérer des Seringues d'ADN prélevées sur les animaux sauvages et de recombiner deux génomes pour tenter d'obtenir un embryon à gènes purs ($90-100\%$).
6. `opal:safe_deposit_box` (**Coffre Inviolable / Safe Box Personnelle**) :
   * *Hitbox :* $1.0 \times 1.0 \times 1.0$ bloc.
   * *Sécurité Totale :* Lié par signature XUID au joueur qui le pose. Même en territoire ennemi ou claim pillé, aucun intrus ne peut l'ouvrir ou le casser. Capacité : 9 slots de base, extensible à 54 slots via des améliorations de banque auprès de Cassandra.
7. `opal:arcanum_table` (**Table de Désenchantement Arcanique**) :
   * *Fonction :* Permet de détruire une arme ou armure moddée obsolète pour en extraire des **Orbes d'Essence d'Ordre** (utilisées pour confectionner de nouveaux parchemins).
8. `opal:ender_disruptor` (**Balise Anti-Perles / Ender Disruptor**) :
   * *Rayon de neutralisation :* **16 blocs sphériques**.
   * *Mécanique :* Toute Perle de l'Ender lancée par un assaillant qui pénètre dans cette sphère est immédiatement vaporisée en fumée violette sans téléporter le joueur. Émet un son de sifflement d'annulation.

---

#### C. Blocs Défensifs & Fortifications de Base (6 Blocs)

| Identifiant Technique | Nom en Jeu | Résistance Explosion | Dégâts Infligés | Outil de Dépose | Rôle Tactique & Propriétés |
| :--- | :--- | :---: | :---: | :---: | :--- |
| `opal:fortified_stone` | **Pierre Fortifiée** | **3 Détonations TNT** | 0 | Pioche Titane (T3+) | Pierre taillée rivetée d'acier. Idéale pour les murs extérieurs de bases de faction. |
| `opal:dense_void_obsidian`| **Obsidienne Dense**| **5 Détonations TNT** | 0 | Pioche d'Opale (T5) | Obsidienne compressée dans le vide céleste. Matériau roi des bunkers de coffres. |
| `opal:aether_armored_glass`| **Verre Blindé** | Résiste TNT T1 | 0 | Pioche Soie | Verre feuilleté transparent. Insensible aux flèches perçantes et aux ricochets de sorts. |
| `opal:ground_spikes` | **Piques d'Acier au Sol**| Dureté 2.0 | **6 HP + Saignement** | Pioche Fer+ | Hauteur 0.3 bloc. Inflige 3 cœurs de dégâts directs et un ralentissement à quiconque marche dessus sans bottes T3+. |
| `opal:shock_plate` | **Dalle d'Électrocution**| Dureté 1.5 | **+40% Posture + Slow**| Pioche Fer+ | Dalle de pression piégée : décharge voltaïque faisant grimper la jauge de posture ennemie instantanément. |
| `opal:reinforced_door` | **Porte Blindée de Guilde**| **100 HP Structure** | 0 | Marteau de Béliers | Porte à double battant en titane. S'ouvre uniquement pour les membres de la faction. |

---

#### D. Blocs Végétaux & Agriculture Industrielle (7 Blocs)

1. `opal:fertile_celestial_soil` (**Terre Fertile Céleste**) :
   * Terre labourée luminescente enrichie à l'engrais d'Aether. Ne se dégrade jamais en marchant ou en sautant dessus. Auto-hydratée (pas besoin de source d'eau adjacente). Accélère la croissance de base de $+100\%$.
2. `opal:iron_crop` (**Plant de Graines de Fer**) :
   * 4 stades de croissance. Texture de tige métallique argentée. Drop : 1 à 3 Pépites de Fer au stade adulte.
3. `opal:gold_crop` (**Plant de Graines d'Or**) :
   * 4 stades de croissance. Texture de panicules dorées scintillantes. Drop : 1 à 3 Pépites d'Or.
4. `opal:diamond_crop` (**Plant de Graines de Diamant**) :
   * 5 stades de croissance. Bourgeons translucides cyan. Drop : 1 à 2 Éclats de Diamant (4 éclats = 1 diamant pur).
5. `opal:celestial_opal_crop` (**Plant de Graines d'Opale**) :
   * 7 stades de croissance. Temps de maturation : 45 minutes réelles. **Exigence stricte :** Doit impérativement être planté sur une terre fertile ayant un bloc de `opal:stellar_amethyst` directement sous elle, sinon la pousse s'arrête au stade 3. Drop : 1 Fragment d'Opale Céleste.
6. `opal:primordial_log` & `opal:primordial_leaves` (**Bois & Feuillage Primordial**) :
   * Arbre géant des biomes druidiques. Le bois donne des planches d'écorce dense insensibles au feu.
7. `opal:venomous_brambles` (**Ronces Vénéneuses**) :
   * Végétation défensive épineuse. Ralentit le déplacement de $60\%$ et inflige l'effet Poison II (4s) aux intrus non membres de la faction.

---

#### E. Blocs Événementiels & Warzone (5 Blocs)

1. `opal:koth_beacon_point` (**Balise Centrale KOTH**) :
   * Pilier de capture interactif situé au sommet de la forteresse de la Warzone. Émet un faisceau lumineux vertical vers le ciel. La couleur du faisceau passe au rouge si contesté, au bleu si capturé par la faction dominante.
2. `opal:faction_totem_core` (**Cœur de Totem de Faction**) :
   * Pilier de 3 blocs totémiques superposés apparaissant lors des événements de Totem en Warzone. Chaque coup de pioche réussi par une faction retire une couche et rapporte des points de siège.
3. `opal:celestial_airdrop_chest` (**Caisse d'Airdrop Larguée**) :
   * Coffre métallique renforcé attaché à un parachute céleste. Lorsqu'il touche le sol en Warzone, il commence un décompte d'ouverture de 60 secondes visible par un hologramme. Passé ce délai, le premier joueur qui interagit s'empare du butin de haut rang.
4. `opal:stargate_portal_frame` (**Cadre de Portail Stellaire**) :
   * Bloc d'obsidienne stellaire inaltérable constituant l'armature des grands anneaux de téléportation dimensionnelle du Hub Céleste.
5. `opal:stargate_vortex` (**Vortex Stellaire de Portail**) :
   * Bloc immatériel traversable générant une nappe de particules luminescentes spiralées. Téléporte instantanément le joueur qui le traverse vers le monde configuré (`warzone`, `opal_main`, `mining_world`, etc.).

---

### 👤 PARTIE II : LE CATALOGUE EXHAUSTIF DES ENTITÉS MODDÉES (24 ENTITÉS)

#### A. Les PNJ Vivants & Guides du Hub (7 Entités)

Toutes les entités PNJ sont basées sur le contrôleur d'entité `CustomHuman` avec skins HD 128x128, animations Idle dynamiques, gestion de la collision et ouverture instantanée d'interfaces FormUI sans freeze réseau.

| Identifiant Technique | Nom du Personnage | Rôle & Fonction | Localisation | Interaction FormUI |
| :--- | :--- | :--- | :--- | :--- |
| `opal:npc_sire_gabriel` | **Sire Gabriel** | Paladin Déchu & Grand Maître des Ordres | Rotonde Centrale du Hub | Arborescence narrative, choix d'Ordre, réinitialisation de spécialisation. |
| `opal:npc_thorgar` | **Thorgar le Forgeron**| Maître Forgeron des Abysses | Grande Forge Céleste | Enclume d'enchantement, réparation contre éclats d'opale, recyclage d'armes. |
| `opal:npc_dame_sylvana` | **Dame Sylvana** | Gardienne des Moissons & Botaniste | Serre Primordiale du Hub | Boutique de semences moddées, engrais céleste, rachat des récoltes géantes. |
| `opal:npc_cassandra` | **Cassandra la Banquière**| Trésorière & Régulatrice des Bourses | Banque Royale Céleste | Accès aux coffres de dépôt inviolables (Safe Boxes) et ordres d'achat HDV. |
| `opal:npc_zephyr` | **Zephyr le Nomade** | Marchand Ambulant Événementiel | Aléatoire (1 des 40 biomes) | Propose 3 objets mythiques en quantité très limitée toutes les 4h pendant 45 min. |
| `opal:npc_eldrin` | **Eldrin l'Archiviste**| Maître du Savoir & Greffier | Hall des Héros du Hub | Carnet de quêtes scénarisées (`/quests`), récompenses de réputation et de saison. |
| `opal:npc_celestial_guard`| **Garde Céleste** | Sentinelle du Sanctuaire (Neutre) | Portes du Hub & Escaliers | Patrouille fluide, salut militaire au passage des champions titrés. |

---

#### B. Entités d'Entraînement & Techniques (4 Entités)

1. `opal:combat_dummy` (**Mannequin d'Entraînement de Parade**) :
   * *Modèle :* Automate de bois renforcé de plaques de cuivre avec un bouclier et une épée d'entraînement.
   * *Hitbox :* $0.6 \times 1.8$ bloc. Invulnérable (0 HP de perte, n'enregistre que les stats).
   * *Cycle d'Apprentissage Interactif :*
     * Toutes les **2.5 secondes**, le mannequin arme une frappe télégraphiée (il lève son épée pendant 15 ticks avec une particule d'alerte orange au-dessus de sa tête).
     * Il déclenche l'attaque à la 16e frame.
     * Si le joueur effectue un Sneak dans la **fenêtre de 9 ticks** : son de parade parfaite retentissant (`random.anvil_land`), gerbe d'étincelles, et message ActionBar :  
       `[ ⭐ PARADE PARFAITE ! Timing : 4 ticks / 9 ]`.
     * Si le joueur rate : le mannequin repousse le joueur et affiche :  
       `[ ❌ PARADE RATÉE - Trop tôt ou trop tard ]`.
2. `opal:floating_leaderboard` (**Hologramme Flottant de Classement**) :
   * *Nature :* Entité invisible avec `CustomName` multiligne persistant et tag `NoGravity`.
   * *Contenu dynamique :* Mis à jour toutes les 60 secondes via requête SQLite locale :
     * Top 5 Factions (Points de Tech / KOTHs gagnés).
     * Top 5 Duellistes (Killstreak le plus élevé, K/D, parades réussies).
     * Top 5 Mineurs (Quantité de minerais d'Opale et Titane extraits).
3. `opal:claim_beacon_hologram` (**Marqueur de Claim de Faction**) :
   * Hologramme flottant suspendu au-dessus du Cœur de Faction indiquant le nom de la guilde, le niveau de Tech actuel et le taux de régénération du Nexus.
4. `opal:loot_beacon_vfx` (**Pilier Visuel de Butin Rare**) :
   * Entité visuelle émettant une colonne de lumière verticale (bleue pour Rare, violette pour Épique, dorée pour Légendaire) au-dessus des objets de grande valeur tombés au sol en Warzone pour éviter qu'ils ne soient manqués.

---

#### C. Faune Vivante & Génétique Moddée — Les Bêtes de Guerre (4 Entités)

Chaque animal issu de l'élevage génétique possède un pool de données NBT comprenant son pourcentage d'ADN ($0-100\%$) et 3 coefficients d'attributs.

1. `opal:war_wolf` (**Loup de Guerre Génétique**) :
   * *Hitbox :* $0.8 \times 0.9$ bloc. Modèle de loup élargi avec caparaçon de fer ou de titane.
   * *Attributs génétiques :* Points de vie (20 à 60 HP), Dégâts de morsure (4 à 12 HP), Force d'impact sur la posture adverse ($+5\%$ à $+20\%$).
   * *Comportement :* Cible en priorité les ennemis désignés par le propriétaire avec un Bâton de Commandement.
2. `opal:tectonic_boar` (**Sanglier Tectonique**) :
   * *Hitbox :* $1.2 \times 1.0$ bloc. Sanglier massif à défenses de roche volcanique.
   * *Rôle :* Monture de siège. Capable de charger en ligne droite (vitesse doublée pendant 3 secondes) et de briser les blocs de bois ou d'enfoncer les portes de faction.
3. `opal:aether_falcon` (**Faucon d'Aether**) :
   * *Hitbox :* $0.4 \times 0.5$ bloc. Oiseau céleste au plumage étincelant.
   * *Rôle :* Familier de reconnaissance. Vole en cercle au-dessus de son maître dans un rayon de 25 blocs et applique un effet de surbrillance (Glowing) aux joueurs ennemis cachés dans les buissons ou sous des potions d'invisibilité.
4. `opal:celestial_steed` (**Destrier Céleste**) :
   * *Hitbox :* $1.4 \times 1.6$ bloc. Cheval ailé aux sabots crépitants.
   * *Attributs génétiques :* Vitesse de pointe de 0.45 (1.5x le cheval vanilla le plus rapide), saut d'obstacle de 4.5 blocs et immunité totale aux dégâts de chute jusqu'à 20 blocs.

---

#### D. Boss de Donjons & Monstres d'Élite (5 Entités)

1. `opal:boss_vampire_lord` (**Seigneur Sanglant — Boss de la Crypte**) :
   * *Hitbox :* $0.9 \times 2.4$ blocs. Humanoïde élancé en armure pourpre brandissant une Faux d'Aetherite.
   * *Points de Vie :* **500 HP**. Barre de boss personnalisée au sommet de l'écran.
   * *Patterns Tactiques :*
     * *Attaque Circulaire (Slash) :* Balayage de faux à $360^\circ$ toutes les 6 secondes. **Doit impérativement être paré au sneak.** Si un joueur rate sa parade, le Seigneur Sanglant lui vole 8 HP et régénère 40 HP.
     * *Frappe d'Ombre (Shadow Dash) :* Se dématérialise en brume noire et réapparaît dans le dos du joueur le plus vulnérable.
2. `opal:boss_basalt_golem` (**Golem de Basalte Tectonique — Boss de la Forge**) :
   * *Hitbox :* $2.2 \times 3.8$ blocs. Colosse de roche incandescente et de plaques de titane.
   * *Points de Vie :* **800 HP**. Résistance naturelle de $40\%$ aux dégâts physiques classiques.
   * *Patterns Tactiques :*
     * *Écrasement Tectonique (Ground Slam) :* Lève ses deux bras au-dessus de sa tête (télégraphie de 1.5s), puis frappe le sol : onde sismique circulaire repoussant tous les joueurs à 12 blocs et brisant leur posture s'ils ne sautent pas au moment de l'impact.
     * *Éruption Magmatique :* Fait jaillir des geysers de lave sous les pieds des joueurs immobiles.
3. `opal:boss_bramble_queen` (**Reine des Ronces — Boss de la Canopée**) :
   * *Hitbox :* $1.0 \times 2.2$ blocs. Entité végétale éthérée entourée de racines vivantes.
   * *Points de Vie :* **650 HP**.
   * *Patterns Tactiques :*
     * *Prison Végétale :* Fait pousser instantanément des ronces autour de 2 joueurs, les immobilisant pendant 4 secondes à moins que leurs coéquipiers ne détruisent les ronces à la hache.
     * *Salve d'Épines Perforantes :* Tire 16 épines téléguidées dans toutes les directions.
4. `opal:abyssal_leviathan` (**Léviathan des Abysses — Monstre de Pêche T4**) :
   * *Hitbox :* $2.5 \times 1.8$ blocs. Serpent de mer cuirassé d'écailles de cobalt.
   * *Points de Vie :* **350 HP**. Déclenché uniquement lors d'un Ferrage Parfait ★ à la Canne Abyssale dans les océans profonds.
   * *Butin :* Écailles abyssales, Plans d'armure T4 et chair de léviathan conférant l'effet Force II et Respiration Aquatique (10 min).
5. `opal:elite_corrupted_knight` (**Chevalier Corrompu — Minion de Donjon**) :
   * *Hitbox :* $0.7 \times 2.0$ blocs. Garde lourd équipé d'un pavois impénétrable.
   * *Mécanique :* Insensible aux attaques frontales tant que sa jauge de posture (100 pts) n'est pas brisée par des coups lourds ou des parades parfaites.

---

#### E. Projectiles & Entités Dynamiques (4 Entités)

1. `opal:breach_tnt_primed` (**TNT de Brèche Amorcée**) :
   * *Gravité & Mèche :* Décompte de **3.0 secondes** (60 ticks). Texture clignotante blanc/rouge avec étincelles incandescentes.
   * *Pouvoir destructeur :* Inflige des dégâts quadruplés ($x4.0$) aux blocs de construction fortifiés (`opal:fortified_stone`, `opal:dense_void_obsidian`).
2. `opal:tactical_ice_shard` (**Éclat de Givre Arcanique**) :
   * Projectile lancé par le Bâton Glacial. Vitesse : 2.2 blocs/tick.
   * À l'impact : crée une zone de glace éphémère de $3 \times 3$ au sol pendant 3 secondes et applique l'effet Lenteur III (3s) à la cible.
3. `opal:seismic_shockwave` (**Onde de Choc Sismique**) :
   * Entité linéaire invisible avançant au ras du sol à la vitesse de 1 bloc/tick sur une distance de 10 blocs. Soulève une traînée de particules de terre et inflige $+35\%$ de dégâts de posture à quiconque se trouve sur sa trajectoire.
4. `opal:airdrop_parachute` (**Parachute Céleste de Largage**) :
   * Entité modèle attachée au sommet du coffre d'Airdrop. Régule la vitesse de chute verticale à exactement **$0.25\text{ m/s}$**, oscillant doucement dans l'air avant de se détacher dès que le coffre touche le sol.

---

### 🎬 PARTIE III : LE RÉPERTOIRE DÉTAILLÉ DES ANIMATIONS BEDROCK (18 ANIMATIONS)

Chaque animation est conçue pour les modèles géométriques Bedrock (`geometry.humanoid` et modèles d'entités spécifiques) et déclenchée par des expressions Molang précises ou des événements de paquets serveur.

```
                      STRUCTURE D'UNE ANIMATION BEDROCK
  [ DÉCLENCHEUR MOLANG ] ──► [ ANIMATION CONTROLLER ] ──► [ TRANSITION & INTERPOLATION ]
   ex: q.is_sneaking          controller.animation.parry     Blend: 0.1s (Linear / Smooth)
   && q.is_holding_sword                                     Bones: right_arm / left_arm / body
```

#### A. Animations Joueur (Player Model — 6 Animations)

1. `animation.player.parry_stance` (**Garde de Parade au Sneak**) :
   * *Modèle :* `geometry.humanoid.custom`.
   * *Bones affectés :* `right_arm`, `left_arm`, `body`.
   * *Condition Molang :* `query.is_sneaking && query.is_item_name_any('slot.weapon.mainhand', 'opal:sword_tier1', 'opal:sword_tier2', 'opal:sword_tier3', 'opal:sword_tier4', 'opal:sword_tier5')`.
   * *Interpolation :* 2 ticks ($0.1\text{s}$) d'entrée, transition fluide.
   * *Chorégraphie :* Le bras droit remonte à $45^\circ$ devant le torse, le bras gauche vient soutenir la garde de l'épée à deux mains horizontalement devant le sternum. Le buste s'incline légèrement en avant en appui sur les jambes fléchies.
2. `animation.player.stagger` (**Rupture de Posture / Étourdissement**) :
   * *Bones affectés :* `head`, `body`, `right_arm`, `left_arm`, `leg0`, `leg1`.
   * *Durée totale :* 40 ticks ($2.0\text{s}$). Boucle de tremblement.
   * *Chorégraphie :*
     * *Frame 0 à 5 :* Recul brutal du buste vers l'arrière de $20^\circ$, la tête part en arrière.
     * *Frame 6 à 35 :* La tête retombe lourdement vers l'avant, les bras se relâchent le long des hanches (impossibilité d'attaquer), le corps vacille avec une oscillation latérale d'ébriété/choc ($f = 3\text{Hz}$).
     * *Frame 36 à 40 :* Redressement progressif du corps et reprise de l'appui sur le pied avant.
3. `animation.player.finisher_strike` (**Frappe Critique d'Exécution**) :
   * *Bones affectés :* `right_arm`, `torso`, `head`.
   * *Durée :* 8 ticks ($0.4\text{s}$).
   * *Chorégraphie :* Le torse effectue une rotation brusque de $60^\circ$ en armant l'arme au-dessus de l'épaule droite, suivi d'un abaissement vertical fulgurant qui foudroie l'adversaire étourdi.
4. `animation.player.staff_channeling` (**Canalisation de Bâton Tactique**) :
   * *Bones affectés :* `right_arm`, `left_arm`, `head`, `body`.
   * *Durée :* 25 ticks ($1.25\text{s}$).
   * *Chorégraphie :* Les deux bras s'élèvent majestueusement vers le ciel tenant le bâton à la verticale à deux mains. La tête se redresse vers le zénith. Un halo de particules spiralées ascendante s'enroule autour du joueur.
5. `animation.player.heavy_reeling` (**Tension de Ferrage de Pêche**) :
   * *Bones affectés :* `right_arm`, `left_arm`, `torso`.
   * *Durée :* Boucle d'oscillation rapide ($6\text{Hz}$) pendant toute la durée du mini-jeu d'ActionBar.
   * *Chorégraphie :* Le buste tire en arrière par saccades, les bras fléchis luttent contre la traction du fil comme si une créature gigantesque tentait de plonger.
6. `animation.player.celestial_ascension` (**Mort Céleste Épique**) :
   * *Bones affectés :* `root`, `body`, tous les membres.
   * *Durée :* 20 ticks ($1.0\text{s}$).
   * *Chorégraphie :* Au lieu de basculer sur le côté comme en vanilla, le corps se redresse à la verticale, devient évanescent (opacité réduite progressivement via le render controller), lévite doucement de 2 blocs vers le ciel en ouvrant les bras, puis se dissout dans une gerbe d'étoiles opalescentes.

---

#### B. Animations PNJ (NPCs Vivants — 4 Animations)

7. `animation.npc.idle_breathing` (**Respiration & Éveil du PNJ**) :
   * *Bones affectés :* `chest`, `shoulders`, `head`.
   * *Durée :* Boucle de 3.5 secondes ($0.28\text{Hz}$).
   * *Chorégraphie :* Légère élévation de 2 centimètres de la cage thoracique à l'inspiration, relâchement lent à l'expiration. Clignement périodique des paupières toutes les 4.5 secondes.
8. `animation.npc.track_player_head` (**Suivi Fluidifié du Regard**) :
   * *Bones affectés :* `head`, `neck`.
   * *Comportement Molang :* `math.clamp(query.target_x_rotation, -45, 45)`, amorti avec une vélocité angulaire maximale de $120^\circ/\text{s}$ pour que le PNJ regarde naturellement le joueur qui s'approche de son étal.
9. `animation.npc.thorgar_hammering` (**Forgeage Actif de Thorgar**) :
   * *Bones affectés :* `right_arm`, `torso`, `left_arm`.
   * *Durée :* Boucle de 1.8 seconde.
   * *Chorégraphie :* Le bras droit lève son lourd marteau de forge à $80^\circ$, prend un court élan de 4 ticks, puis frappe l'enclume d'acier céleste. À l'impact exact ($t = 0.9\text{s}$), émission d'une gerbe d'étincelles synchronisée avec le son de métal frappé. La main gauche maintient une pièce d'armure incandescente avec des tenailles.
10. `animation.npc.eldrin_book_levitation` (**Lévitation du Grimoire d'Eldrin**) :
    * *Bones affectés :* `bone_book` (bone détaché du modèle).
    * *Durée :* Boucle d'oscillation verticale sinusoïdale de 2.0s ($y = \sin(t) \times 0.08\text{m}$). Les pages du grimoire se tournent lentement d'une feuille toutes les 8 secondes.

---

#### C. Animations Boss & Créatures de Guerre (4 Animations)

11. `animation.vampire.shadow_dash` (**Dash d'Ombre du Seigneur Sanglant**) :
    * *Durée :* 4 ticks ($0.2\text{s}$).
    * *Chorégraphie :* Le modèle du boss se comprime horizontalement en une silhouette de fumée noire étirée le long de son vecteur de vélocité, parcourt 8 blocs instantanément, puis se déploie à nouveau derrière la victime dans un coup de faux fendant.
12. `animation.golem.ground_slam` (**Écrasement Sismique du Golem de Basalte**) :
    * *Durée :* 35 ticks ($1.75\text{s}$).
    * *Chorégraphie :*
      * *Ticks 0 à 20 (Télégraphie d'alerte) :* Le golem se cabre sur ses jambes, lève ses deux poings colossaux au-dessus de sa tête. Les fissures de lave sur son torse s'illuminent au maximum (glow texture).
      * *Ticks 21 à 24 :* Abaissement foudroyant des deux poings qui percutent le sol de plein fouet.
      * *Tick 25 :* Déclenchement de l'onde de choc au sol, secousse de caméra pour tous les joueurs à 15 blocs et projection de débris de roche.
13. `animation.bramble.root_sprout` (**Éruption des Ronces de la Reine**) :
    * *Durée :* 15 ticks ($0.75\text{s}$).
    * *Chorégraphie :* La reine projette ses deux mains vers la terre. En face d'elle, 4 tentacules épineux sortent de terre en vrille ascendante hélicoïdale pour encercler les joueurs ciblés.
14. `animation.leviathan.water_leap` (**Bond Hors de l'Eau du Léviathan**) :
    * *Durée :* 40 ticks ($2.0\text{s}$).
    * *Chorégraphie :* Le serpent marin géant jaillit verticalement hors de l'océan sur une hauteur de 6 blocs, arque son corps d'écailles de cobalt au sommet de sa trajectoire, puis replonge la gueule ouverte en créant une énorme vague d'écume blanche.

---

#### D. Animations de Blocs & Objets Dynamiques (4 Animations)

15. `animation.portal.stargate_rotation` (**Rotation des Anneaux du Portail**) :
    * *Bones affectés :* `ring_outer`, `ring_inner`.
    * *Comportement :* L'anneau extérieur tourne dans le sens horaire à la vitesse constante de $20^\circ/\text{s}$. L'anneau intérieur tourne en sens antihoraire à $35^\circ/\text{s}$, créant un effet d'accélérateur de particules dimensionnel fascinant.
16. `animation.faction_heart.heartbeat` (**Pulsation Cardiaque du Nexus de Faction**) :
    * *Modèle :* `geometry.faction_heart`.
    * *Chorégraphie :*
      * En temps normal (base paisible) : Dilatation et rétraction d'échelle ($1.00 \to 1.06 \to 1.00$) au rythme apaisé de 60 battements/minute, illuminé d'un bleu céleste doux.
      * En état de raid (base attaquée) : Rythme cardiaque accéléré à 140 battements/minute, la couleur vire au rouge vermillon pulsant avec émission d'ondes de choc d'alerte.
17. `animation.airdrop.parachute_sway` (**Balancement Pendulaire de l'Airdrop**) :
    * *Bones affectés :* `canopy`, `crate`.
    * *Durée :* Boucle d'oscillation pendulaire de 2.4 secondes ($12^\circ$ d'angle maximum), simulant l'effet de portance et le vent céleste lors de la descente du coffre.
18. `animation.composter.churn_gears` (**Broyage Mécanique du Composteur d'Accélération**) :
    * *Bones affectés :* `gear_left`, `gear_right`, `crusher_blades`.
    * *Comportement :* Rotation continue des engrenages à $180^\circ/\text{s}$ et mouvement alternatif vertical des lames de broyage intérieures chaque fois que le joueur y dépose des résidus végétaux.
