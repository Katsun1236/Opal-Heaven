# 🛡️ OPAL HEAVEN — LE LIVRE BLANC DÉFINITIF DU GAME DESIGN
> **Type de Serveur :** PvP-Faction Moddé Compétitif & Économique  
> **Plateforme :** Minecraft Bedrock Edition (PocketMine-MP 5.x PHP 8.2 + Proxy Go)  
> **Inspiration Directe :** Le meilleur du PvP-Faction français (Paladium, Histeria), axé sur l'efficacité, le farm utile, le pillage et le combat sans délires cosmiques ni magie abstraite.  
> **Statut :** Cahier des charges et architecture de production définitive.

---

## 📑 TABLE DES MATIÈRES GÉNÉRALE

1. [Piliers Fondateurs & Vision du Serveur](#1-piliers-fondateurs--vision-du-serveur)
2. [La Hiérarchie des Minerais & Matériaux](#2-la-hiérarchie-des-minerais--matériaux)
3. [Les Équipements Moddés (Armures, Armes & Bâtons PvP)](#3-les-équipements-moddés-armures-armes--bâtons-pvp)
4. [Les Outils Moddés & Utilitaires de Farm](#4-les-outils-moddés--utilitaires-de-farm)
5. [Le Système de Combat Bedrock & La Parade Tactique](#5-le-système-de-combat-bedrock--la-parade-tactique)
6. [Les 4 Métiers (Jobs) & Progression Niveau 1 à 50](#6-les-4-métiers-jobs--progression-niveau-1-à-50)
7. [L'Agriculture Industrielle & Les Machines Moddées](#7-lagriculture-industrielle--les-machines-moddées)
8. [Le Système de Factions, Claims & Le DTR (Power)](#8-le-système-de-factions-claims--le-dtr-power)
9. [L'Art du Pillage & Les Blocs Fortifiés de Base](#9-lart-du-pillage--les-blocs-fortifiés-de-base)
10. [La Warzone & Les Événements PvP (KOTH, Totem, Airdrops, Boss)](#10-la-warzone--les-événements-pvp-koth-totem-airdrops-boss)
11. [L'Économie d'Airain, Bourse & Hôtel des Ventes (`/ah`)](#11-léconomie-dairain-bourse--hôtel-des-ventes-ah)
12. [L'Échange Sécurisé de Joueur à Joueur (`/trade`)](#12-léchange-sécurisé-de-joueur-à-joueur-trade)
13. [L'Organisation des 4 Mondes du Serveur](#13-lorganisation-des-4-mondes-du-serveur)
14. [Le Grand Registre des 18 Patchs Anti-Exploits](#14-le-grand-registre-des-18-patchs-anti-exploits)

---

## 1. Piliers Fondateurs & Vision du Serveur

Opal Heaven est pensé pour être **simple à comprendre, rapide à prendre en main et compétitif sur la durée**.

```
                           LE MODÈLE OPAL HEAVEN
   ┌────────────────────────────────────────────────────────────────────────┐
   │                          LE JOUEUR EST LIBRE                           │
   │                                                                        │
   │   ⚔️ LE GUERRIER         ⛏️ LE MINEUR          🌾 LE FERMIER            │
   │   Fait du PvP H24       Mine des pioches 3x3   Cultive des minerais    │
   │   Gagne de l'argent     Vend son minerai       Vend de la nourriture   │
   │   en tuant et en KOTH   aux guerriers          et des engrais au shop  │
   │                                                                        │
   │   AUCUN joueur n'est forcé de faire un métier qu'il déteste pour jouer.│
   └────────────────────────────────────────────────────────────────────────┘
```

* **Zéro Farm Forcé :** Un joueur qui ne veut faire que du PvP peut s'équiper et réparer son armure uniquement grâce aux récompenses de combat, arènes, KOTHs et primes de têtes.
* **L'Opale comme Graal :** Comme le Paladium sur Paladium ou l'Histerite sur Histeria, **l'Opale** est le minerai le plus rare, le plus cher et le plus résistant du serveur.
* **Ergonomie Mobile & Console :** 100% des menus sont tactiles (`FormUI`). Zéro commande complexe à taper dans le chat.

---

## 2. La Hiérarchie des Minerais & Matériaux

La progression repose sur les bases vanilla complétées par **les 3 seuls minerais et tiers moddés du serveur** :

```
                  ÉCHELLE DE PROGRESSION DES MINERAIS
       [ FER ] ──► [ DIAMANT ] ──► [ RUBIS ] ──► [ ARGENT ] ──► [ OPALE ]
       (Vanilla)    (Vanilla)     (Moddé T1)    (Moddé T2)    (Moddé T3 - Ultime)
```

1. **Le Fer & L'Or (Vanilla) :** Matériaux de départ classiques pour les crafts de base, les composants de machines et le premier argent.
2. **Le Diamant (Vanilla) :** Équipement de transition indispensable pour forger la première pioche capable de miner le Rubis.
3. **Le Rubis (Tier 1 Moddé) :**
   * *Outil requis pour miner :* Pioche en Diamant.
   * *Localisation :* Cavernes entre $y = 0$ et $y = 32$.
   * *Usage :* Gemme écarlate vive. Armure légère orientée vitesse, dague d'assaut, premier Hammer $3 \times 3$.
4. **L'Argent (Tier 2 Moddé) :**
   * *Outil requis pour miner :* Pioche en Rubis.
   * *Localisation :* Profondeurs entre $y = -64$ et $y = 0$.
   * *Usage :* Métal lourd et résistant. Armure de tanking et résistance aux impacts, hache de guerre, composants de fortification et machines.
5. **L'Opale (Tier 3 Moddé - Le Graal Suprême) :**
   * *Outil requis pour miner :* Pioche en Argent.
   * *Localisation :* Très rare filon isolé entre $y = -64$ et $y = -32$ (maximum 1 filon par chunk).
   * *Usage :* L'équipement le plus puissant du jeu (force et réduction de dégâts), Hammer $3 \times 3$ auto-fondoir, les 4 bâtons d'action et la monnaie de réserve suprême d'Opal Heaven.

---

## 3. Les Équipements Moddés (Les 3 Seuls Sets d'Armures du Serveur)

Pour éviter toute dispersion et garantir un équilibre parfait en Warzone, il n'existe **strictement que 3 armures moddées** sur Opal Heaven :

```
┌──────────────────┬────────────────┬──────────────┬────────────────────────────────┐
│ Nom du Set       │ Protection     │ Durabilité   │ Effet Spécial Passif           │
├──────────────────┼────────────────┼──────────────┼────────────────────────────────┤
│ **1. Rubis**     │ +15% / Diamant │ 1 200 pts    │ Vitesse I permanente (Full Set)│
│ **2. Argent**    │ +30% / Diamant │ 2 500 pts    │ Résistance au recul +25%       │
│ **3. Opale**     │ +50% / Diamant │ 5 000 pts    │ Force I + Réduction dégâts 15% │
└──────────────────┴────────────────┴──────────────┴────────────────────────────────┘
```

### Les Armes Moddées :
* **Épée en Opale :** 11 points de dégâts de base (5.5 cœurs).
* **Hache de Guerre en Argent :** 13 points de dégâts, brise le bouclier adverse plus vite.
* **Dague en Rubis :** Attaque ultra-rapide, inflige un saignement de 1 cœur sur coup critique.

### Les 4 Bâtons d'Action PvP (Action Sticks) :
Outils tactiques à charges limitées, indispensables pour le PvP de faction :
1. **Bâton de Soin (Heal Stick) :** 3 charges. Clic droit ➔ Restaure instantanément 4 cœurs de vie. Cooldown de 40 secondes entre chaque utilisation.
2. **Bâton de Vitesse (Speed Stick) :** 5 charges. Clic droit ➔ Confère `Speed II` pendant 12 secondes (cooldown 25s).
3. **Bâton de Force (Knockback Stick) :** 3 charges. Émet une onde de répulsion qui éjecte tous les ennemis proches à 6 blocs (idéal pour briser un encerclement 1v3).
4. **Bâton de Brèche (Breach Stick) :** 2 charges. Utilisable uniquement pendant les heures de raid (18h-23h) pour geler une porte de faction pendant 15 secondes.

---

## 4. Les Outils Moddés & Utilitaires de Farm

Pour accélérer le farm sans tricher, le serveur intègre des outils à large zone :

1. **Le Hammer en Rubis ($3 \times 3$) :**
   * Mine instantanément une zone de $3 \times 3$ blocs de roche d'un seul coup.
   * Durabilité : 2 000 blocs.
2. **Le Hammer en Opale ($3 \times 3$ Fondoir) :**
   * Mine en $3 \times 3$ et **cuit automatiquement** le fer et l'or en lingots directement dans l'inventaire.
3. **La Super-Pelle ($3 \times 3$) :**
   * Creuse la terre, le sable et le gravier en $3 \times 3$.
4. **La Hache d'Abattage (Lumberjack Axe) :**
   * En coupant le bas d'un arbre, tout le tronc tombe d'un coup.

---

## 5. Le Système de Combat Bedrock & La Parade Tactique

Pour en finir avec le simple spam-clic sans stratégie, Opal Heaven utilise un système de **Parade au Sneak** adapté à la manette, au mobile et au clavier.

```
                          LA PARADE AU SNEAK
     [ COUP ENNEMI ] ──► [ LE DÉFENSEUR S'ACCROUPIT (SNEAK) ]
                                    │
           ┌────────────────────────┴────────────────────────┐
           ▼                                                 ▼
   [ DANS LES 0.4 SECONDES ]                         [ SNEAK EN RETARD OU CONTINU ]
   ⭐ PARADE PARFAITE !                              🛡️ GARDE PASSIVE
   • 0 dégât subi                                    • -50% de dégâts subis
   • L'attaquant est repoussé en arrière             • La posture monte de 25%
   • L'attaquant est étourdi 1 seconde
```

* **Le Stagger (Rupture de Garde) :** Si un joueur spamme sa garde sans réfléchir, sa jauge de posture atteint $100\%$. Il est alors étourdi pendant 2 secondes (impossibilité de frapper ou boire une potion). Le coup suivant lui inflige un coup critique garanti.
* **Le Combat-Tag :** Dès qu'un coup est porté ou reçu, un décompte de **15 secondes** s'enclenche dans l'ActionBar. Toute déconnexion ou téléportation pendant ce délai entraîne la mort instantanée et le drop du stuff.

---

## 6. Les 4 Métiers (Jobs) & Progression Niveau 1 à 50

Les joueurs choisissent librement un ou plusieurs métiers (`/jobs`) pour débloquer des compétences passives et de l'argent :

```
┌──────────────┬─────────────────────────────┬──────────────────────────────────────┐
│ Métier       │ Comment monter d'XP         │ Déblocages Majeurs                   │
├──────────────┼─────────────────────────────┼──────────────────────────────────────┤
│ ⚔️ **Guerrier** │ Kills PvP, KOTH, Primes     │ Armures moins chères, Force passive, │
│              │                             │ Réparation de stuff en tuant.        │
├──────────────┼─────────────────────────────┼──────────────────────────────────────┤
│ ⛏️ **Mineur**   │ Minage de pierre et minerais│ Déblocage des Hammers 3x3,           │
│              │                             │ Double drop de minerais, Auto-fonte. │
├──────────────┼─────────────────────────────┼──────────────────────────────────────┤
│ 🌾 **Fermier**  │ Récolte et plantation       │ Arrosoirs grande zone, Composteur,   │
│              │                             │ Double récolte, Graines de minerais. │
├──────────────┼─────────────────────────────┼──────────────────────────────────────┤
│ 🏹 **Chasseur** │ Kills d'animaux et monstres │ Butin de viande x2, Cuir renforcé,   │
│              │                             │ Apprivoisement de montures rapides.  │
└──────────────┴─────────────────────────────┴──────────────────────────────────────┘
```

Chaque niveau passé rapporte de l'argent cash sur le compte du joueur et des jetons d'achat pour la boutique du métier.

---

## 7. L'Agriculture Industrielle & Les Machines Moddées

Pour que les fermes de faction soient rentables et utiles au PvP :

```
                          LES MACHINES AGRICOLES
   [ 64 DÉCHETS VÉGÉTAUX ] ──► [ COMPOSTEUR ] ──► [ 1 ENGRAIS INSTANTANÉ ]
   [ GRAINE DE FER ]       ──► [ TERRE ARROSÉE ]──► [ PÉPITES DE FER ]
```

1. **Les 3 Arrosoirs Moddés :**
   * *Arrosoir en Fer :* Fait pousser instantanément une zone de $3 \times 3$ cultures (10 utilisations).
   * *Arrosoir en Diamant :* Zone de $5 \times 5$ (25 utilisations).
   * *Arrosoir en Opale :* Zone de $9 \times 9$ (illimité, recharge à la source d'eau).
2. **Le Composteur d'Accélération :**
   * Machine posable dans la base. On y jette des graines, herbes ou feuilles inutiles. Tous les 64 items insérés, il produit **1 sac d'Engrais Rapide** qui fait mûrir n'importe quelle culture instantanément.
3. **Les Graines de Minerais (Magical Crops Simples) :**
   * *Graines de Fer :* Donnent des pépites de fer à maturité.
   * *Graines d'Or :* Donnent des pépites d'or.
   * *Graines de Diamant :* Donnent des éclats de diamant (4 éclats = 1 diamant).
   * *Graines d'Opale :* Pousse lente (30 min réelles), produit des pépites d'Opale.
4. **La Fonderie Rapide :**
   * Four compacté qui cuit les minerais 3 fois plus vite qu'un haut-fourneau vanilla avec un seul seau de lave pour 100 blocs.

---

## 8. Le Système de Factions, Claims & Le DTR (Power)

Les factions sont gérées avec les commandes standard bien connues des joueurs : `/f create`, `/f claim`, `/f home`, `/f disband`, `/f who`.

```
                    FONCTIONNEMENT DU DTR DE FACTION
   FACTION INTACTE (DTR > 0)             FACTION RAIDABLE (DTR <= 0)
   ┌──────────────────────────────┐      ┌──────────────────────────────┐
   │ • Territoire imprenable      │      │ ⚠️ BASES EN DANGER !          │
   │ • Portes & coffres bloqués   │ ──►  │ • Portes ouvrables par tous  │
   │ • Explosions actives 18h-23h │      │ • Coffres pillables sans TNT │
   └──────────────────────────────┘      │ • Claims volables            │
                                         └──────────────────────────────┘
```

* **Membres Max :** 10 joueurs par faction.
* **Le DTR (Points de Vie de la Faction) :**
  * Chaque joueur apporte **3 points de DTR** (max 30 DTR pour 10 membres).
  * Chaque mort d'un membre retire **1 point de DTR**.
  * Si le DTR descend à **0 ou en négatif** : la faction passe en statut **RAIDABLE**. Les ennemis peuvent alors ouvrir les portes, fouiller les coffres et sur-claimer le territoire.
  * **Régénération :** Le DTR remonte de +1 point toutes les 45 minutes, avec un gel de 15 minutes après chaque mort.
* **Le Coffre de Faction (`/f chest`) :** Inventaire partagé de 54 slots accessible par les officiers et le chef pour stocker le matériel de guerre.

---

## 9. L'Art du Pillage & Les Blocs Fortifiés de Base

Pour éviter que n'importe quelle base soit détruite en 2 secondes à la simple TNT, la construction défensive utilise des blocs à résistance mesurée :

```
┌──────────────────────────┬─────────────────────────────┬──────────────────────────┐
│ Bloc Défensif            │ Résistance aux Explosions   │ Utilité Tactique         │
├──────────────────────────┼─────────────────────────────┼──────────────────────────┤
│ **Pierre Renforcée**     │ Encaisse **3 TNT directes** │ Murs extérieurs de base  │
│ **Obsidienne Renforcée** │ Encaisse **5 TNT directes** │ Bunkers de coffres       │
│ **Verre Blindé**         │ Insensible aux blasts T1    │ Visières d'observation   │
│ **Piques en Acier**      │ Inflige 3 cœurs au contact  │ Piège au sol dans couloirs│
│ **Porte Blindée**        │ 50 coups de bélier pour briser│ SAS d'entrée sécurisé  │
└──────────────────────────┴─────────────────────────────┴──────────────────────────┘
```

* **Horaires Officiels de Raid (18h00 - 23h00) :** Les dégâts d'explosions sur les claims ne sont actifs **que le soir entre 18h et 23h**. En dehors de cette tranche horaire, la TNT explose sans casser les blocs de claim. Fini le stress de se faire piller à 4 heures du matin pendant qu'on dort.
* **La TNT de Brèche (Breach TNT) :** TNT moddée qui inflige 4 fois plus de dégâts aux blocs renforcés pour forcer les lignes de défense.

---

## 10. La Warzone & Les Événements PvP (KOTH, Totem, Airdrops, Boss)

La Warzone est une vaste zone circulaire de **1 500 blocs de rayon** autour du spawn. Le claim y est strictement interdit, la perte de stuff y est active à 100%.

```
                         PLAN DE LA WARZONE CENTRALE
                                   [ NORD ]
                         🌋 Golem d'Argent (Boss WZ)
                                      │
       [ OUEST ]                      ▼                      [ EST ]
    📦 Zone Airdrop      ◄── [ CRATÈRE CENTRAL ] ──►     🛡️ Le Totem
    (Toutes les 2h)          ★ KOTH Quotidien ★          (Mercredi & Samedi)
                                      ▲
                                      │
                                   [ SUD ]
                             Ponts de sortie Spawn
```

1. **Le KOTH (King of the Hill - Tous les soirs à 19h00) :**
   * Une zone de $5 \times 5$ blocs au centre de la Warzone.
   * La première faction qui tient la zone pendant **4 minutes consécutives** remporte l'événement.
   * *Butin :* 1 Clé de Box KOTH (contenant armes en Opale, armures et lingots) + $15\,000\$$.
2. **Le Totem de Faction (Mercredi 16h & Samedi 15h) :**
   * Un pilier de 30 blocs d'obsidienne et d'opale apparaît en Warzone.
   * Les factions doivent casser les blocs du totem à la pioche tout en combattant les rivaux. Chaque bloc cassé rapporte des points de classement de faction.
3. **Les Airdrops (Largages Célestes - Toutes les 2 heures) :**
   * Un coffre tombe lentement en parachute dans la Warzone (annoncé dans le chat avec coordonnées).
   * À l'atterrissage, un compte à rebours de 60 secondes s'affiche au-dessus du coffre avant qu'il ne puisse être ouvert. La faction qui contrôle le terrain rafle le stuff.
4. **Le Boss de Warzone (Samedi 21h00 & Dimanche 16h00) :**
   * Un **Golem d'Argent Géant** (2 000 PV) apparaît au nord de la Warzone.
   * Il frappe en zone au sol et repousse les joueurs. C'est un événement **PvPvE ouvert** : les factions s'entretuent tout en essayant de donner le dernier coup au boss pour faire apparaître le grand coffre de butin !

---

## 11. L'Économie d'Airain, Bourse & Hôtel des Ventes (`/ah`)

L'argent du jeu est le Dollar ($). L'économie est pensée pour durer 6 mois sans dévaluation :

```
                  LE CIRCUIT FERMÉ DE L'ARGENT
        [ REVENTE DE MINERAIS & CULTURES AU SHOP PNJ ]
                             │  Injecte de l'argent
                             ▼
                    [ MASSE MONÉTAIRE ]
                             │  Détruit de l'argent
                             ▼
         [ TAXE DE 5% SUR TOUTES LES VENTES /ah ]
         [ FRAIS DE CRÉATION DE FACTION & CLAIMS ]
```

1. **L'Hôtel des Ventes Tactile (`/ah`) :**
   * Permet de vendre et d'acheter des objets entre joueurs sans risque.
   * Taxe fixe de **5% sur chaque vente réussie**, détruite par le serveur pour éliminer l'inflation.
2. **Le Système d'Ordres d'Achat (Bourse) :**
   * Un joueur peut créer un ordre d'achat : *"J'achète 200 lingots d'Argent à 40$ l'unité"*. L'argent est bloqué par le serveur.
   * N'importe quel joueur qui mine de l'argent peut ouvrir l'HDV, cliquer sur "Remplir l'ordre" et toucher son argent immédiatement.
3. **Le Shop PNJ Fixe (`/shop`) :**
   * Prix planchers garantis : la cobble, le bois, le blé et les minerais bruts peuvent toujours être revendus au PNJ pour avoir un revenu garanti même pour un débutant.

---

## 12. L'Échange Sécurisé de Joueur à Joueur (`/trade`)

Pour supprimer définitivement les vols au drop par terre et les arnaques :

```
                     MENU FORMUI DE L'ÉCHANGE SÉCURISÉ
   ┌────────────────────────────────────────────────────────────────────────┐
   │ 🤝 ÉCHANGE SÉCURISÉ : Bastien  ◄──►  JoueurB                           │
   ├────────────────────────────────────┬───────────────────────────────────┤
   │ [ Mon Offre (Max 6 items) ]        │ [ Offre Adverse ]                 │
   │ • 1x Épée en Opale Tranchant V     │ • 64x Lingots d'Argent           │
   │ • 5 000$ ajoutés                   │ • 1x Bâton de Soin (3 charges)    │
   ├────────────────────────────────────┴───────────────────────────────────┤
   │ [ ✅ JE SUIS PRÊT ]                                                     │
   │ ⚠️ ANTI-ARNAQUE : Tout changement réinitialise et bloque pendant 3s.    │
   └────────────────────────────────────────────────────────────────────────┘
```

* Portée max : 6 blocs.
* **Sécurité Anti-Switch 3 Secondes :** Si l'un des deux joueurs modifie son offre ou retire un item après avoir cliqué sur Prêt, **l'accord est annulé**, l'écran clignote et un verrouillage de 3 secondes empêche de revalider trop vite.
* **Transaction Atomique :** Les inventaires s'échangent en un seul bloc dans la base de données SQLite. Impossible de perdre ou de dupliquer un item en cas de déconnexion.

---

## 13. L'Organisation des 4 Mondes du Serveur

Pour garantir 20 TPS stables avec 300 joueurs connectés :

1. **Le Spawn / Hub (`hub`) :** Sanctuaire sans PvP. PNJ de vente, zone d'entraînement, tableau des classements, portails vers les mondes.
2. **La Warzone (`warzone`) :** Arène circulaire de 1 500 blocs autour du spawn. 100% PvP, sans claims, théâtre du KOTH, Totem, Airdrops et Boss.
3. **Le Monde Faction (`opal_main`) :** Carte infinie de $10\,000 \times 10\,000$ blocs. C'est ici que les factions posent leurs claims, construisent leurs forteresses et leurs fermes. Aucun reset.
4. **Le Monde Minage (`mining_world`) :** Carte de $5\,000 \times 5\,000$ blocs dédiée uniquement au minage intensif. **Ce monde est réinitialisé automatiquement le 1er de chaque mois** pour renouveler les filons sans saccager le monde faction.

---

## 14. Le Grand Registre des 18 Patchs Anti-Exploits

Le plugin `AntiExploitManager.php` protège le serveur contre les abus classiques :

1. **Tag `PlayerPlaced` :** Tout minerai posé par un joueur est tagué. Il ne donne aucun XP de métier et ne drop aucun minerai doublé.
2. **Anti-Kill Farm XUID/IP :** Tuer le même joueur en boucle ou un joueur sur la même connexion ne donne ni argent ni points de faction (cooldown de 30 min).
3. **Anti-Combat Logging :** Déconnexion en combat ➔ un clone vulnérable reste 15 secondes sur place et peut être tué pour récupérer le stuff.
4. **Anti-Pearl Glitch :** Les perles qui atterrissent dans les dalles ou les coins de blocs sont annulées pour empêcher de traverser les murs de base.
5. **Anti-Duplication UI :** Les inventaires sont verrouillés côté serveur dès qu'un menu FormUI (`/ah`, `/trade`, `/shop`) est ouvert.
6. **Anti-Speedmine & Noclip :** Rejet côté serveur de tout paquet de casse de bloc trop rapide ou hors de portée.
7. **Anti-FastPlace :** Limite de 8 blocs posés par seconde.
8. **Anti-Fly & Speedhack :** Calcul de trajectoire côté serveur avec refoulement immédiat au sol.
9. **Anti-Killaura & Reach :** Portée de frappe strictement limitée à 3.2 blocs et angle de vue vérifié.
10. **Anti-Spam Chat :** 2 secondes entre les messages et filtre automatique anti-liens/insultes.
11. **Anti-XRay :** Les minerais non exposés à l'air libre sont cachés dans les paquets de chunks envoyés au client.
12. **Anti-Glitch de Bordure :** Barrière physique inviolable aux limites de carte.
13. **Anti-Macro Pêche :** Timings de ferrage aléatoires rendant inopérants les autoclickers.
14. **Anti-Dupe Sacs & Shulkers :** Interdiction d'ouvrir un sac dans un autre sac.
15. **Anti-Vol de Coffre Alliés :** Système de permissions de rôle au sein de la faction (`/f rank`).
16. **Anti-Macro Parade :** Maintenir le Sneak en continu annule la parade parfaite au profit de la garde passive.
17. **Anti-Glitch Wither :** Le Wither ne peut pas casser les blocs protégés hors des heures de raid.
18. **Anti-Desync Inventaire :** Checksum vérifié à chaque transaction financière.
---

## 15. Le Grand Répertoire des Commandes & Ergonomie Complète

Pour offrir une expérience fluide, intuitive et sans friction sur mobile comme sur PC, l'ensemble des commandes a été conçu pour être rapide à taper, doté d'autocomplétion native et secondé par des interfaces FormUI Bedrock tactiles.

### 15.1. Commandes Utilitaires & Qualité de Vie Joueur (QoL)

| Commande | Raccourcis | Description & Fonctionnement | Cooldown |
| :--- | :--- | :--- | :--- |
| `/condense` | `/compact` | Transforme instantanément tous les lingots/pépites de l'inventaire en blocs (Rubis, Argent, Opale, Fer, Or, Diamant, Redstone, Charbon). Zéro clic manuel. | Aucun |
| `/trash` | `/poubelle` | Ouvre une interface de poubelle sécurisée. Les objets déposés sont détruits à la fermeture avec confirmation anti-fausse manip. | Aucun |
| `/sell all` | `/sell` | Vend tous les items vendables de l'inventaire au prix du `/shop` en une milliseconde avec récapitulatif du gain dans le chat. | Aucun |
| `/sell hand` | `/sh` | Vend uniquement le stack d'items tenu en main. | Aucun |
| `/nv` | `/nightvision` | Active/désactive l'effet Vision Nocturne permanent (indispensable pour miner dans l'obscurité sans placer 50 torches). | Aucun |
| `/near` | Aucun | Scanne et affiche la distance des joueurs non-alliés dans un rayon de 30 blocs (utile pour détecter les embuscades). | 15s |
| `/settings` | `/options` | Ouvre le panneau FormUI de préférences : toggle des `/msg`, des `/tpa`, de l'auto-pickup, des sons de killmark et du mode Low-Spec. | Aucun |
| `/rtp` | `/wild` | Téléporte aléatoirement le joueur dans une zone sûre (anti-lave, anti-océan) du Monde Minage ou Monde Faction. | 60s |
| `/feed` | Aucun | Restaure instantanément la barre de nourriture du joueur (réservé aux grades boutique). | 15 min |
| `/reclaim` | Aucun | Permet de récupérer en un clic les récompenses de vote mensuel, les compensations de maintenance ou les avantages de début de saison. | Unique |
| `/claiminfo` | `/f info chunk` | Affiche l'état du chunk actuel : faction propriétaire, niveau de protection du claim, intégrité du bouclier anti-creeper. | Aucun |
| `/loadout` | `/gear` | Interface permettant d'enregistrer et de rééquiper en 1 clic un ensemble complet (armure, épée, potions, perles) depuis son coffre. | 10s |
| `/sort` | `/trier` | Trie automatiquement l'inventaire du joueur ou le coffre ouvert par catégorie (Armures > Armes > Outils > Minerais > Consommables). | 3s |

---

### 15.2. Commandes de Gestion de Faction (`/f`)

L'intégralité du système de faction est accessible via la commande `/f` ou un menu tactile centralisé `/f menu` :

```
             ┌─────────────────────────────────────────────────────────┐
             │                 MENU FACTION OPAL HEAVEN                │
             ├─────────────────────────────────────────────────────────┤
             │ [ 🏰 Ma Faction ]         [ 🗺️ Carte Radar (/f map) ]    │
             │ [ 🛡️ Permissions Rôles ]  [ 📦 Coffre Partagé (/f chest) ]│
             │ [ 💰 Banque de Faction ]  [ ⚔️ Ralliement Warzone ]     │
             │ [ 🏆 Classement Top ]     [ ⚙️ Paramètres & Recrutement]│
             └─────────────────────────────────────────────────────────┘
```

* **Création & Territoire :**
  * `/f create <nom>` : Fonde une faction (coût : 5 000$).
  * `/f claim` : Revendique le chunk où se tient le joueur (consomme 1 point de Power).
  * `/f unclaim` / `/f unclaimall` : Libère un ou tous les claims.
  * `/f autoclaim` : Revendique automatiquement chaque nouveau chunk traversé par le chef ou officier en marchant.
* **Radar & Tactique :**
  * `/f map` : Affiche une matrice 9x9 chunks dans le chat ou sous FormUI avec code couleur :
    * 🟩 Vert = Vos claims de faction.
    * 🟦 Bleu = Factions alliées.
    * 🟥 Rouge = Factions ennemies.
    * ⬜ Gris = Factions neutres.
    * ⬛ Noir = Wilderness (territoire libre).
  * `/f rally` : Place une balise tactique d'urgence visible par tous les membres sur la boussole et l'ActionBar pendant 60 secondes (indispensable pour coordonner un assaut ou une défense en Warzone).
* **Hiérarchie & Permissions fines (`/f perm`) :**
  * Matrice de permissions par grade (Recrue, Membre, Capitaine, Officier, Co-Leader, Chef) paramétrable par clic :
    * *Droit d'ouvrir les coffres / conteneurs.*
    * *Droit de casser / poser des blocs dans le claim.*
    * *Droit d'inviter ou exclure des joueurs.*
    * *Droit d'activer la dynamite ou d'utiliser les Foreuses.*
    * *Droit de retirer de l'argent de la banque de faction.*
* **Coffre & Économie Faction :**
  * `/f chest` : Coffre virtuel partagé (taille évolutive selon le niveau de faction, de 27 à 108 slots). Chaque mouvement d'item est horodaté et consigné dans un journal anti-vol (`/f chest logs`).
  * `/f deposit <montant>` & `/f withdraw <montant>` : Gestion de la trésorerie commune.

---

### 15.3. Commandes de Modération & Administration Staff

Pour assurer un ordre absolu, une réactivité chirurgicale et une transparence exemplaire :

* **Interpellation & Vérification Anti-Cheat :**
  * `/freeze <pseudo>` : Immobilise totalement le joueur suspect. Sa caméra est figée, un bandeau FormUI d'avertissement s'affiche avec le lien Discord du serveur et un compte à rebours de 5 minutes pour se présenter en vérification vocale/partage d'écran. **Toute déconnexion sous freeze entraîne un bannissement automatique de 30 jours pour refus de vérification.**
  * `/vanish` (ou `/v`) : Invisibilité absolue. Masque le modérateur de la liste TAB, désactive les bruits de pas, les particules, et permet d'ouvrir les coffres et fours en mode fantôme sans déclencher l'animation d'ouverture pour les joueurs alentours.
  * `/alts <pseudo>` : Recherche instantanée dans la base SQLite de tous les comptes ayant partagé la même adresse IP, le même XUID Xbox Live ou le même identifiant matériel (DeviceID Bedrock). Détecte immédiatement les doubles comptes de ban-evade.
* **Sanctions & Régulation :**
  * `/mute <pseudo> [durée] [raison]` & `/tempmute` : Sanctionne les infractions textuelles. Le joueur peut toujours lire le chat mais ne peut plus envoyer de messages publics ni privés.
  * `/ban <pseudo> [durée] [raison]` & `/tempban` : Bannissement avec broadcast discret ou public et archivage webhook Discord.
  * `/kick <pseudo> [raison]` : Expulsion immédiate.
  * `/staffchat` (ou `/sc <texte>`) : Canal de communication chiffré exclusif au staff, synchronisé en direct avec un salon privé Discord Staff via webhook.
* **Inspection & Restauration :**
  * `/invsee <pseudo>` : Ouvre l'inventaire en direct d'un joueur connecté pour inspecter ses armes, armures et slots de raccourcis.
  * `/endersee <pseudo>` : Ouvre et permet d'éditer le contenu de l'Enderchest d'un joueur.
  * `/clearlag` : Déclenche manuellement un scan et nettoyage d'entités orphelines avec annonce de décompte (15s, 10s, 5s).
  * `/tpall` & `/tphere <pseudo>` : Outils de téléportation administrative.

---

### 15.4. Outils Développeur & Diagnostics Système en Direct

Pour surveiller la santé de la machine sans impacter le gameplay :

* `/status` : Tableau de bord instantané affichant les métriques PocketMine fondamentales :
  * TPS (Ticks Per Second) sur 1m, 5m, 15m.
  * Mémoire vive RAM (Allouée, Utilisée, Pic, Garbage Collector stats).
  * Nombre d'entités vivantes, de tuiles (TileEntities) et de chunks actifs par monde (`hub`, `warzone`, `opal_main`, `mining_world`).
* `/timings <on|off|paste>` : Profileur natif PocketMine de haute précision. Enregistre la durée de chaque événement d'écoute, chaque tâche asynchrone et chaque tick d'entité, puis génère un rapport web pour identifier le moindre plugin consommateur de millisecondes.
* `/reload-config [plugin]` : Recharge à chaud les fichiers de configuration YAML (`shop.yml`, `bosses.yml`, `kits.yml`, `anti_exploit.yml`) sans redémarrer le serveur et sans interrompre les sessions de jeu en cours.
* `/rollback <player|chunk> <temps>` : Annule toutes les modifications de blocs d'un joueur ou restaure l'état d'un chunk donné via les logs SQLite d'audit (indispensable en cas d'erreur de modérateur ou d'abus avéré).
* `/packetstat` : Affiche le débit réseau en temps réel (paquets entrants/sortants par seconde, compression ratio, bande passante en KB/s) entre le Proxy Go et les clients connectés.

---

## 16. L'Ingénierie Anti-Lag & les Sensations Jouissives (Game Feel & Dopamine)

Un serveur PvP-Faction réussit ou échoue sur deux critères déterminants : **la fluidité absolue (zéro latence, 20 TPS constants)** et **le plaisir physique de jouer (le hit feel, le sound design, la gratification instantanée)**. Voici l'architecture technique mise en œuvre sur Opal Heaven.

---

### 16.1. L'Architecture Réseau & Multi-Thread Zéro-Lag

```
 [ Client Joueur Mobile / PC ]
              │  (UDP RakNet Chiffré - 60 à 100 paquets/sec)
              ▼
   ┌────────────────────────────────────────────────────────┐
   │             PROXY GO DÉDIÉ (C-Go / Golang)             │
   │  * Terminaison RakNet UDP & Déchiffrement ECDH         │
   │  * Compression / Décompression Zlib & Snappy           │
   │  * Filtre Anti-DDoS Volumétrique & Cache de Skins      │
   │  * Régulateur de Mouvement (Agrégation à 20 paquets/s) │
   └────────────────────────────────────────────────────────┘
              │  (Paquets binaires décompressés via Loopback TCP 127.0.0.1)
              ▼
   ┌────────────────────────────────────────────────────────┐
   │               POCKETMINE-MP 5.X CORE ENGINE             │
   │  * Boucle Principale Dédiée : Combat & Ticks Blocs     │
   │  * 0% de charge CPU gaspillée en compression réseau    │
   │  * 4 Threads Asynchrones (AsyncTask) pour SQL/Disque   │
   └────────────────────────────────────────────────────────┘
```

1. **Découplage Intégral Réseau / Gameplay :**
   * Sur un serveur Bedrock classique, la compression Zlib/Snappy des paquets réseau consomme jusqu'à **45% de la puissance CPU**.
   * Sur Opal Heaven, le **Proxy Go** absorbe l'intégralité du protocole RakNet, de la négociation MTU et de la compression. PocketMine reçoit des paquets bruts, légers et désérialisés, libérant 100% du thread principal pour la physique du combat et la réactivité des coups.
2. **Régulation des Paquets de Déplacement (`PlayerAuthInputPacket`) :**
   * Les clients Bedrock modernes (surtout Windows 10/11 à 144Hz+) envoient jusqu'à 80 paquets de position par seconde, saturant le thread principal.
   * Le Proxy Go échantillonne et fusionne ces données pour délivrer exactement **20 états de déplacement par seconde** (1 par tick serveur), divisant par 3 les calculs de collision sans perte de précision pour la visée.
3. **Distance de Vue Dynamique & Culling d'Entités Intelligent :**
   * Par défaut, la distance de vue est fixée à **8 chunks**.
   * Dès qu'un rassemblement massif est détecté en Warzone (plus de 25 joueurs dans une zone de 64x64 blocs lors d'un KOTH ou d'un Boss), la distance d'envoi des chunks est temporairement ajustée à **5 chunks** pour les participants, maintenant 20.0 TPS même lors des assauts à 50 contre 50.
   * **Smart Tick Culling :** Les entités passives ou neutres situées à plus de 24 blocs d'un joueur réduisent leur fréquence d'IA de 20 ticks/sec à 4 ticks/sec, réduisant de 75% l'empreinte CPU des entités.

---

### 16.2. L'Élimination Radicale des Entités au Sol (Anti-Drop Lag)

Les entités au sol (items jetés, minerais minés, drops de mobs) sont la cause n°1 des micro-freezes sur Bedrock :

1. **Auto-Pickup Direct dans l'Inventaire (Minage & Récolte) :**
   * Dès qu'un joueur brise un minerai ou une culture avec un outil moddé (ou avec le mode `/toggle pickup` activé), **le drop est injecté directement dans son inventaire ou dans son Sac en Opale**.
   * Aucun item flottant n'est créé dans le monde. Plus de 60 000 entités de cobble et de minerais sont ainsi évitées chaque heure.
2. **Caisse de Mort Éphémère (Death Urn) :**
   * À la mort d'un joueur en Warzone ou en monde Faction, ses 36 slots d'inventaire ne s'éparpillent pas en 36 entités distinctes qui font chuter les FPS des téléphones portables.
   * L'intégralité du butin est compactée en un **Coffre de Mort temporaire (Hologramme interactif)** pendant 5 minutes. Un simple clic dessus permet au vainqueur de ramasser le tout ou de looter ce qui l'intéresse.
3. **Bridage Anti-Lag Redstone :**
   * Tout circuit redstone qui pulse à plus de 4 Hz (horloges rapides) est automatiquement limité par le plugin `RedstoneGovernor`.
   * Si une boucle infinie de pistons ou de répéteurs tourne dans le vide pendant plus de 30 secondes sans joueur à proximité, elle est désactivée et son propriétaire reçoit une alerte dans l'ActionBar.

---

### 16.3. Le Fast-Respawn & l'Éradication des Délais Frustrants

Sur Bedrock vanilla, la mort d'un joueur est lente et frustrante : un écran rouge/noir bloque la vue pendant 2 à 3 secondes avec un bouton "Réapparaître" qui freeze parfois sur mobile.

* **Bypass Instantané de l'Écran de Mort :**
  * Dès que la santé d'un joueur tombe à 0, le serveur intercepte l'événement et envoie instantanément un paquet `RespawnPacket`.
  * Le joueur est téléporté immédiatement à son Faction Home ou au Spawn sans subir l'écran de mort ni de transition noire.
* **Système de Re-Gear Instantané (`/loadout`) :**
  * En tapant `/loadout 1` ou en cliquant sur le panneau de sa base, le joueur vide son inventaire et rééquipe en **0.1 seconde** son armure en Rubis/Argent/Opale, son épée, ses potions de soin et ses perles depuis son coffre de faction.
  * Retour au combat en Warzone en moins de 10 secondes après une défaite !

---

### 16.4. Le "Hit Feel" Jouissif & le Sound Design Dopaminergique

Ce qui rend un serveur PvP addictif et agréable, c'est le retour sensoriel de chaque action :

1. **Sound Design Tranchant & Réactif :**
   * **Frappe Critique :** Son d'enclume métallique cristalline (`random.anvil_land` à pitch 1.8x) indiquant immédiatement que le coup a porté son multiplicateur de dégâts.
   * **Coup Fatal (Kill Chime) :** Un tintement de clochette jubilatoire (`random.orb` à pitch 2.0x, inspiré du hitmarker de Call of Duty et de Paladium) retentit instantanément dans le casque du vainqueur, accompagné d'un battement de cœur sourd pour le vaincu.
   * **Brisure d'Armure Ennemie :** Bruit d'explosion de verre brisé (`random.glass`) couplé à une alerte visuelle : *"Armure Ennemie Brisée !"*.
   * **Parade Parfaite Réussie :** Son lourd de tôle d'acier percutée (`item.shield.block`) avec micro-secousse de la caméra de l'assaillant, signalant son étourdissement pendant 1 seconde.
2. **Indicateurs de Dégâts Flottants (Floating Damage Particles) :**
   * Chaque coup porté fait jaillir une micro-particule discrète affichant les dégâts exacts infligés (ex: `💥 -6.4 ❤` en coup critique, `-3.2 ❤` en coup standard).
   * Les particules disparaissent en 0.5 seconde pour ne jamais obstruer la vision de visée.
3. **Physique du Knockback Équilibrée & Nerveuse :**
   * Vitesse horizontale : **0.385 bloc/tick** (permet d'enchaîner des combos W-Tap fluides sans projeter l'adversaire à 10 mètres).
   * Vitesse verticale : **0.360 bloc/tick** (retombée rapide au sol, éliminant l'effet de flottaison lunaire désagréable de Minecraft Bedrock de base).
   * Plafond de prise en compte CPS fixé à **12 CPS côté serveur** : neutralise les macros et autoclickers tout en récompensant le timing pur et la précision du curseur.

---

### 16.5. Le Minage Dopaminergique (Sound & Visual Crunch)

Miner pendant des heures ne doit jamais être une corvée monotone :

* **Le Crunch des Blocs 3x3 :** Casser un bloc avec un Marteau en Rubis ou en Opale déclenche un son de craquement de roche dense et percutant, doublé d'une gerbe d'étincelles irisées.
* **Jauge d'XP Métier Dynamique :** Une barre de progression discrète apparaît au-dessus de la barre de raccourcis lors du minage (`Mineur Lv. 34 : [██████░░░░] +24 XP`), ponctuée d'un accord sonore ascendant à chaque passage de niveau.
* **Auto-Compactage Silencieux :** Les minerais bruts se transforment automatiquement en lingots dans le Sac en Opale, évitant d'avoir à s'arrêter toutes les 2 minutes pour trier son sac.

---

### 16.6. Killstreaks & Adrénaline en Warzone

Pour inciter les joueurs à sortir de leur base et à livrer des combats spectaculaires en Warzone :

* **Séries d'Éliminations (Killstreaks) :**
  * **3 Kills d'affilée :** *Frénésie Guerrière* ➔ Effet Vitesse I pendant 15 secondes + 150$ de prime.
  * **5 Kills d'affilée :** *Carnage* ➔ Annonce sonore de gong pour tous les joueurs en Warzone + message rouge éclatant dans le chat + 500$ de prime.
  * **10 Kills d'affilée :** *Légende d'Opale* ➔ Une prime de 2 500$ est automatiquement posée sur sa tête + une traînée de particules dorées éphémères suit ses pas. Le joueur devient la cible prioritaire de toute la Warzone !
* **Tension Sonore des Coffres Contestés :**
  * Lors de la mort d'un World Boss ou de l'atterrissage d'un Airdrop, les **30 secondes de déverrouillage** du coffre sont rythmées par un tic-tac d'horloge qui accélère toutes les 5 secondes (`random.click` ➔ `note.hat`), créant une montée d'adrénaline insoutenable jusqu'à l'explosion d'étincelles marquant l'ouverture du butin.

---

# 🏁 Conclusion Générale

Ce Livre Blanc définit l'ensemble des piliers fondateurs d'**Opal Heaven** :
1. **Un Game Design Épuré et Terre-à-Terre :** 3 tiers d'armures moddées (Rubis, Argent, Opale), aucun délire mystique ou cosmique déconnecté, un gameplay inspiré des plus grands succès du PvP-Faction français (*Paladium*, *Histeria*).
2. **Une Concentration Totale en Warzone :** Zéro donjon instancié. Les World Boss, les Airdrops, le KOTH et le Totem se déroulent à ciel ouvert, générant des batailles de territoire épiques et continues.
3. **Une Économie Fermée et Anti-Inflation :** Des prix de vente fixes, des taxes d'hôtel des ventes, et une rareté millimétrée garantissant 6 mois de progression sans saturation.
4. **Une Haute Ingénierie Technique Bedrock :** Découplage complet via Proxy Go, 20 TPS inébranlables même à 300 joueurs, auto-pickup direct anti-lag, respawn instantané et sensations de frappe dopaminergiques au millième de seconde près.

Opal Heaven est prêt pour son implémentation technique et son déploiement.
