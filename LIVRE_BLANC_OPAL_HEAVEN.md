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

# 🏁 Conclusion

Ce document fixe les règles d'or et l'architecture concrète d'**Opal Heaven**. Zéro fioriture inutile, un gameplay carré, des blocs et armes utiles, un PvP compétitif en Warzone et une économie saine taillée pour durer 6 mois.
