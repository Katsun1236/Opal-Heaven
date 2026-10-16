# 🌌 OPAL HEAVEN — LE LIVRE BLANC ABSOLU DU GAME DESIGN & D'INGÉNIERIE (MASTER CODEX)
> **Statut :** Document Maître Officiel de Référence Intégrale (Phases 1, 2 & Production)  
> **Plateforme :** Minecraft Bedrock Edition (PocketMine-MP 5.x PHP 8.2 + Proxy Réseau Go Dédié)  
> **Auteurs :** Bastien & Antigravity  
> **Architecture & Philosophie :** Hybridation Paladium (Java) & Histeria (Bedrock), sublimée par une liberté absolue du joueur, le combat de posture martiale et une économie fermée anti-inflation.

---

## 📑 GRAND SOMMAIRE DU MASTER CODEX

* [TOME I : LE MANIFESTE, LE GRAND LORE & L'ONBOARDING](#tome-i--le-manifeste-le-grand-lore--lonboarding)
  * [Chapitre 1 : Philosophie Fondatrice & Liberté Absolue du Joueur](#chapitre-1--philosophie-fondatrice--liberté-absolue-du-joueur)
  * [Chapitre 2 : La Mythologie de la Faille Céleste & Origine des 3 Ordres](#chapitre-2--la-mythologie-de-la-faille-céleste--origine-des-3-ordres)
  * [Chapitre 3 : Le Protocole d'Onboarding en 6 Minutes (« L'Éveil de l'Opale »)](#chapitre-3--le-protocole-donboarding-en-6-minutes--léveil-de-lopale-)
* [TOME II : LE COMBAT MARTIAL & LES 3 ORDRES D'ASCENSION](#tome-ii--le-combat-martial--les-3-ordres-dascension)
  * [Chapitre 4 : La Mécanique de Posture, Parade au Sneak & Finisher](#chapitre-4--la-mécanique-de-posture-parade-au-sneak--finisher)
  * [Chapitre 5 : L'Ordre du Sang (PvP Pur) & Arbres des 3 Spécialisations](#chapitre-5--lordre-du-sang-pvp-pur--arbres-des-3-spécialisations)
  * [Chapitre 6 : L'Ordre des Abysses (Minage & Industrie) & Arbres des 3 Spécialisations](#chapitre-6--lordre-des-abysses-minage--industrie--arbres-des-3-spécialisations)
  * [Chapitre 7 : L'Ordre de la Terre (Agriculture & Génétique) & Arbres des 3 Spécialisations](#chapitre-7--lordre-de-la-terre-agriculture--génétique--arbres-des-3-spécialisations)
  * [Chapitre 8 : Le Répertoire Exhaustif des 54 Méthodes Économiques](#chapitre-8--le-répertoire-exhaustif-des-54-méthodes-économiques)
* [TOME III : LA CARTOGRAPHIE CÉLESTE, LES 40 BIOMES & LES DONJONS](#tome-iii--la-cartographie-céleste-les-40-biomes--les-donjons)
  * [Chapitre 9 : L'Architecture Multi-Mondes Dédiée](#chapitre-9--larchitecture-multi-mondes-dédiée)
  * [Chapitre 10 : Le Registre Intégral des 40 Biomes Moddés à Auras](#chapitre-10--le-registre-intégral-des-40-biomes-moddés-à-auras)
  * [Chapitre 11 : Les Donjons Instanciés PvE & Les 3 Grands Boss](#chapitre-11--les-donjons-instanciés-pve--les-3-grands-boss)
* [TOME IV : L'ÉCONOMIE D'AIRAIN, BOURSE & GRAND LIVRE D'ARTISANAT](#tome-iv--léconomie-dairain-bourse--grand-livre-dartisanat)
  * [Chapitre 12 : L'Écosystème Monétaire à 3 Devises & Barème des Prix PNJ](#chapitre-12--lécosystème-monétaire-à-3-devises--barème-des-prix-pnj)
  * [Chapitre 13 : L'Hôtel des Ventes (`/ah`) & Ordres d'Achat Boursiers](#chapitre-13--lhôtel-des-ventes-ah--ordres-dachat-boursiers)
  * [Chapitre 14 : Le Grand Catalogue des Recettes de Crafting Moddé](#chapitre-14--le-grand-catalogue-des-recettes-de-crafting-moddé)
  * [Chapitre 15 : Le Registre des Tables de Drop & Pourcentages Précis](#chapitre-15--le-registre-des-tables-de-drop--pourcentages-précis)
* [TOME V : LA GUERRE DE FACTION, TERRITOIRES & WARZONE](#tome-v--la-guerre-de-faction-territoires--warzone)
  * [Chapitre 16 : Système de Factions, Claims, DTR & Tech Tree](#chapitre-16--système-de-factions-claims-dtr--tech-tree)
  * [Chapitre 17 : L'Art du Siège, Fortifications & Horaires de Raid](#chapitre-17--lart-du-siège-fortifications--horaires-de-raid)
  * [Chapitre 18 : La Warzone Dynamique & Le Calendrier Hebdomadaire](#chapitre-18--la-warzone-dynamique--le-calendrier-hebdomadaire)
* [TOME VI : LES MÉTIERS AVANCÉS & IMMERSION SOCIALE](#tome-vi--les-métiers-avancés--immersion-sociale)
  * [Chapitre 19 : La Pêche Céleste & Abyssale au Timing](#chapitre-19--la-pêche-céleste--abyssale-au-timing)
  * [Chapitre 20 : L'Élevage Spécial & La Génétique Vivante (ADN)](#chapitre-20--lélevage-spécial--la-génétique-vivante-adn)
  * [Chapitre 21 : Le Système d'Enchantements Moddés (Custom Enchants)](#chapitre-21--le-système-denchantements-moddés-custom-enchants)
  * [Chapitre 22 : Le Panthéon des PNJ Vivants & Arborescences de Dialogue](#chapitre-22--le-panthéon-des-pnj-vivants--arborescences-de-dialogue)
  * [Chapitre 23 : Le Système de Quêtes & Contrats d'Ordre (`/quests`)](#chapitre-23--le-système-de-quêtes--contrats-dordre-quests)
  * [Chapitre 24 : Le Protocole d'Échange Sécurisé (`/trade`)](#chapitre-24--le-protocole-déchange-sécurisé-trade)
  * [Chapitre 25 : Communication, Rangs Joueurs & Leaderboards](#chapitre-25--communication-rangs-joueurs--leaderboards)
* [TOME VII : L'INGÉNIERIE BEDROCK, SÉCURITÉ & RÉPERTOIRE VISUEL](#tome-vii--lingénierie-bedrock-sécurité--répertoire-visuel)
  * [Chapitre 26 : L'Architecture Hybride Go Proxy + PocketMine-MP](#chapitre-26--larchitecture-hybride-go-proxy--pocketmine-mp)
  * [Chapitre 27 : Le Grand Registre des 18 Patchs Anti-Exploits](#chapitre-27--le-grand-registre-des-18-patchs-anti-exploits)
  * [Chapitre 28 : Répertoire Visuel des 35 Blocs Moddés](#chapitre-28--répertoire-visuel-des-35-blocs-moddés)
  * [Chapitre 29 : Répertoire Visuel des 24 Entités Moddées](#chapitre-29--répertoire-visuel-des-24-entités-moddées)
  * [Chapitre 30 : Répertoire des 18 Animations Bedrock & VFX](#chapitre-30--répertoire-des-18-animations-bedrock--vfx)

---

# TOME I : LE MANIFESTE, LE GRAND LORE & L'ONBOARDING

## Chapitre 1 : Philosophie Fondatrice & Liberté Absolue du Joueur

Opal Heaven brise la malédiction des serveurs PvP-Faction traditionnels où les joueurs sont contraints de subir des heures de corvées répétitives pour espérer combattre.

```
                          LE PRINCIPE D'AUTONOMIE TOTALE
   ┌──────────────────────────────────────────────────────────────────────────┐
   │                       LIBERTÉ D'ACTION ABSOLUE                           │
   │                                                                          │
   │   ⚔️ GUERRIER DU SANG      ⛏️ MINEUR DES ABYSSES     🌿 DRUIDE DE LA TERRE│
   │   • Combat pur H24          • Extraction de masse      • Agriculture d'élite│
   │   • Gagne du stuff en PvP   • Génère les gemmes        • Fournit la nourriture│
   │   • Zéro minage forcé       • Zéro PvP forcé           • Zéro combat forcé  │
   │   • Monnaie : Marques Sang  • Monnaie : Éclats Tellur. • Monnaie : Sève     │
   └──────────────────────────────────────────────────────────────────────────┘
```

### Les 4 Piliers Inviolables du Game Design :
1. **L'Éradication du Grind Forcé :** Un joueur ayant choisi l'Ordre du Sang peut équiper, réparer et régénérer son personnage du niveau 1 au niveau 50 exclusivement par ses faits d'armes, arènes et éliminations en Warzone.
2. **Le Cloisonnement Strict des Avantages :** Les bonus d'artisanat, de minage ou d'élevage n'influent **JAMAIS** sur les caractéristiques fondamentales du PvP (dégâts bruts, posture maximale). Le skill de combat reste souverain.
3. **L'Accessibilité Mobile-First (La Règle des 3 Clics) :** 100% des menus, boutiques, ordres et commandes sont accessibles via des formulaires visuels tactiles (`FormUI`), sans jamais forcer un joueur sur smartphone ou manette à saisir de longues commandes dans le chat.
4. **La Pérennité Macroéconomique sur 6 Mois :** Élimination mathématique de l'inflation grâce à des taxes de circulation destructrices (sinks de 5% à l'HDV) et un système d'ordres d'achat boursiers en circuit fermé.

---

## Chapitre 2 : La Mythologie de la Faille Céleste & Origine des 3 Ordres

### Le Récit des Origines : L'Éclatement de l'Opale Primordiale
Au commencement du monde régnait le **Cœur d'Opale**, une relique titanesque suspendue au sommet du firmament qui maintenait la pesanteur et la cohésion des terres. Rongée par les querelles intestines des premiers mortels, la gemme s'est brisée lors du **Jour de la Faille**.
La terre ferme fut pulvérisée en quarante archipels flottants suspendus au-dessus d'un abîme sans fond. De la fragmentation de l'Opale naquirent trois énergies primitives :
* **L'Essence du Sang Céleste :** Incarnation de la volonté martiale, de la bravoure et de la maîtrise du fer.
* **L'Essence Abyssale :** Vibration tellurique reliant le basalte, les filons de titane et les résonances géologiques profondes.
* **L'Essence Sylvestre :** Sève de renaissance capable de faire germer des minéraux vivants et de muter les bêtes sauvages.

Trois factions de survivants prêtèrent serment devant les débris de l'Opale, jurant de rebâtir le royaume céleste selon leur propre voie :
* **L'Ordre du Sang** revendiqua la citadelle centrale et la Warzone pour purger la faiblesse par le combat d'honneur.
* **L'Ordre des Abysses** plongea dans les entrailles des îles pour forger les armes et armures que le monde réclamait.
* **L'Ordre de la Terre** enracina ses serres sur les plateaux fertiles pour nourrir les armées et percer les secrets de l'ADN animal.

---

## Chapitre 3 : Le Protocole d'Onboarding en 6 Minutes (« L'Éveil de l'Opale »)

Pour garantir un taux de rétention supérieur à $90\%$ dès la première session, chaque nouveau joueur traverse un parcours initiatique fluide et sans friction :

```
                  PARCOURS INITIATIQUE D'ACCUEIL (CHRONO : 6 MIN)
    [ CONNEXION ] ──► [ MINUTE 1-2 : CRYPTE CÉLESTE ] ──► [ MINUTE 3-4 : LE MANNEQUIN ]
                             Réveil cinématique                Apprentissage parade
                             Attribution Orbe Céleste          Validation 3 parades
                                                                         │
                                                                         ▼
    [ EXPLORATION LIBRE ] ◄── [ MINUTE 6 : LE TÉLÉPORTEUR ] ◄── [ MINUTE 5 : LE CHOIX ]
      Overworld ou Warzone        Kit de départ selon Ordre         Sire Gabriel (FormUI)
```

### Le Déroulement Étape par Étape :
1. **Minute 0:00 - Le Réveil dans la Crypte :** Le joueur s'éveille dans une antichambre d'albâtre. Il reçoit instantanément dans son 9e slot d'inventaire l'**Orbe Céleste** (objet d'interface indestructible lié au joueur). Un message ActionBar s'affiche : `§b✦ Bienvenue en Éveil. Suivez la traînée de lumière céleste.`
2. **Minute 1:30 - L'Épreuve Martiale du Mannequin :** Le joueur rencontre le premier PNJ automate (`opal:combat_dummy`). Pour ouvrir les grilles du sanctuaire, il doit exécuter avec succès **3 parades parfaites consécutives au Sneak** lors de frappes télégraphiées. L'ActionBar confirme chaque réussite en vert éclatant.
3. **Minute 3:30 - L'Audience de Sire Gabriel :** Le joueur pénètre dans la Rotonde Céleste. Un clic droit sur Sire Gabriel ouvre l'interface de choix d'Ordre avec résumé des passifs, styles de jeu et gains économiques.
4. **Minute 5:00 - Attribution du Kit d'Ordre Débutant :**
   * *Guerrier du Sang :* Armure en cuir renforcé T1, Épée en fer trempé, 16 Viandes Séchées, 10 Marques de Sang.
   * *Mineur des Abysses :* Pioche en Acier T2 (Efficacité I), Casque de mineur luminescent, 32 Torches, 5 Éclats Telluriques.
   * *Druide de la Terre :* Arrosoir en Bois T1, 16 Graines Fertilisées, 4 Poussières de Compost, Bottes de Voyageur.
5. **Minute 6:00 - Déploiement :** Le joueur est téléporté sur l'esplanade des Portails Stellaires, prêt à conquérir le monde de faction ou à entrer dans la fosse PvP.

---

# TOME II : LE COMBAT MARTIAL & LES 3 ORDRES D'ASCENSION

## Chapitre 4 : La Mécanique de Posture, Parade au Sneak & Finisher

Le système de combat rompt avec le spam-click frénétique habituel pour instaurer une dynamique tactique inspirée des jeux de combat de précision (Sekiro/Dark Souls adapté au moteur réseau Bedrock).

```
                     CYCLE D'ÉCHANGE DE POSTURE MARTIALE
     [ ATTAQUE ENNEMIE ] ──────────────────────────────────────────┐
              │                                                     │
              ▼                                                     ▼
     ┌──────────────────┐                                  ┌──────────────────┐
     │  PARADE PARFAITE │ (Timing : 1-9 ticks)             │   COUP ENCAISSÉ  │
     │  • 0 dégât subi  │                                  │ • Dégâts pleins  │
     │  • Recul attaquant│                                 │ • Posture +20%   │
     │  • Posture adv +15%                                 └────────┬─────────┘
     └──────────────────┘                                           │
                                                                    ▼
                                                           [ JAUGE À 100% ]
                                                           RUPTURE / STAGGER !
                                                           (Étourdissement 2.0s)
                                                                    │
                                                                    ▼
                                                           [ FINISHER CRITIQUE ]
                                                           (Dégâts purs doublés)
```

### A. Les Spécifications Techniques du Système de Posture :
* **Jauge de Posture :** Chaque joueur possède une barre invisible (affichée en ActionBar lors des combats) calibrée de **0 à 100 points**.
* **Régénération Naturelle :** La posture se dissipe au rythme de **15 points par seconde** si le joueur n'a pas subi de dégâts depuis 3.0 secondes. La dissipation est doublée à l'arrêt complet.
* **Fenêtre de Parade au Sneak :**
  * S'accroupir (`Sneak`) avec une épée ou un bouclier arme la parade instantanément.
  * Si le coup ennemi impacte entre le **1er et le 9e tick (0.05s à 0.45s)** suivant le sneak : **Parade Parfaite**.
  * Si le sneak est maintenu au-delà de 9 ticks : la garde devient passive (réduit les dégâts de $50\%$, mais fait monter la posture du défenseur de $+25\%$).
* **Rupture de Posture (Stagger) :**
  * Dès que la jauge atteint 100 points, le joueur entre en état de Stagger pendant **40 ticks (2.0 secondes)**.
  * Le joueur étourdi subit un tremblement caméra (`CameraShakePacket`), ne peut plus bouger, frapper ou utiliser d'items.
  * Tout coup porté à un joueur en Stagger est un **Finisher Critique** : dégâts bruts multipliés par **$2.0$**, ignorant l'armure à hauteur de $30\%$.

---

## Chapitre 5 : L'Ordre du Sang (PvP Pur) & Arbres des 3 Spécialisations

> **Devise :** *"Le sang versé sanctifie l'acier."*  
> **Gain d'XP d'Ordre :** Éliminations en Warzone (100 XP), Parades parfaites (15 XP), Victoires KOTH (500 XP), Contrats de prime réussis (200 XP).

### A. La Boutique du Sang (Autonomie PvP Complète)
Le Guerrier du Sang dépense ses **Marques de Sang** auprès du Marchand Rouge au spawn :
* *Rations de Guerre (1 Marque) :* Pain de combat restaurant instantanément 5 cœurs et 6 points de faim.
* *Poussière de Réparation Martiale (5 Marques) :* Restaure 250 points de durabilité sur l'armure équipée sans aucune enclume.
* *Potion de Furie Céleste (8 Marques) :* Octroie Force I et Vitesse II pendant 45 secondes.
* *Coffre de Champion Sanglant (25 Marques) :* Contient des armes en titane, parchemins d'enchantement et fragments d'opale.

### B. Arbres des 3 Spécialisations de l'Ordre du Sang (Niveaux 1 à 50)

```
                       LES 3 VOIES DU GUERRIER DU SANG
          ┌───────────────────────┬───────────────────────┐
          ▼                       ▼                       ▼
    [ LE BRETTEUR ]         [ LE BERSERKER ]        [ LE TRAQUEUR ]
    Maître de la parade     Puissance de crise      Vitesse & Assassinat
    et du duel 1v1          et mêlée de masse       Chasse aux primes
```

#### 1. Le Bretteur (Duelliste Imprenable)
* **Niveau 10 (Passif 1 - Réflecteur Cinétique) :** Chaque parade parfaite inflige $+20\%$ de dégâts de posture supplémentaires à l'attaquant et lui applique un micro-recul de 1 bloc.
* **Niveau 20 (Passif 2 - Riposte Éclair) :** La première attaque portée dans la seconde suivant une parade parfaite a $100\%$ de chances d'être un coup critique.
* **Niveau 35 (Actif - Garde d'Acier) :** Cooldown 45s. Élargit la fenêtre de parade parfaite de 9 à 14 ticks pendant 6 secondes.
* **Niveau 50 (Ultime - Duel Sacré) :** Cooldown 120s. Verrouille la cible dans une arène d'énergie de 10 blocs de rayon pendant 8 secondes. Tout tiers extérieur ne peut intervenir.

#### 2. Le Berserker (Fureur Sanguinaire)
* **Niveau 10 (Passif 1 - Rage Montante) :** Augmente les dégâts de mêlée de $+1\%$ pour chaque tranche de $4\%$ de points de vie manquants.
* **Niveau 20 (Passif 2 - Festin Hématique) :** Chaque élimination restaure immédiatement 4 cœurs de vie et retire tous les malus de potion en cours.
* **Niveau 35 (Actif - Cri de Guerre) :** Cooldown 40s. Émet une onde rugissante repoussant tous les assaillants à 5 blocs et appliquant Faiblesse I aux ennemis pendant 5s.
* **Niveau 50 (Ultime - Invulnérabilité Martiale) :** Si le Berserker subit un coup fatal, sa mort est annulée, il est gelé à 1 demi-cœur et devient insensible aux dégâts pendant 3.5 secondes pour tenter une exécution de la dernière chance.

#### 3. Le Traqueur (Ombre de la Warzone)
* **Niveau 10 (Passif 1 - Frappe Sournoise) :** Les attaques assénées dans le dos de la victime infligent $+30\%$ de dégâts critiques.
* **Niveau 20 (Passif 2 - Foulée Spectrale) :** Vitesse de déplacement augmentée de $+15\%$ en permanence tant que le joueur est accroupi ou sous l'effet de discrétion.
* **Niveau 35 (Actif - Pas de l'Ombre) :** Cooldown 30s. Téléporte le joueur 6 blocs en avant à travers les lignes ennemies avec invisibilité totale pendant 2.5 secondes.
* **Niveau 50 (Ultime - Marque de Mort) :** Cooldown 90s. Pose un sceau d'exécution sur la cible. Après 5 secondes, $35\%$ de tous les dégâts infligés pendant cette fenêtre sont réinfligés instantanément en dégâts purs ignorant l'armure.

---

## Chapitre 6 : L'Ordre des Abysses (Minage & Industrie) & Arbres des 3 Spécialisations

> **Devise :** *"La roche révèle la vérité du monde."*  
> **Gain d'XP d'Ordre :** Minage de cobalt (15 XP), titane (35 XP), aetherite (75 XP), opale (250 XP), déclenchement de surcharges harmoniques (50 XP).

### A. Les Éclats Telluriques (Autonomie Minière Complète)
Dépensables au *Comptoir de la Guilde Souterraine* :
* *Nectar d'Endurance (2 Éclats) :* Supprime la faim et confère Célérité II (Haste) pendant 10 minutes.
* *Bombe de Faille Géologique (6 Éclats) :* Pulvérise instantanément un cube de $3 \times 3 \times 3$ de roche stérile sans détruire les minerais précieux.
* *Parchemin de Liaison Minérale (15 Éclats) :* Téléporte les minerais extraits directement dans le coffre de faction sécurisé.

### B. Arbres des 3 Spécialisations de l'Ordre des Abysses (Niveaux 1 à 50)

#### 1. Le Sismologue (Destructeur de Roche)
* **Niveau 10 (Passif 1 - Rythme Harmonique) :** Frapper les blocs de roche en rythme toutes les 0.4s fait monter une jauge de résonance qui augmente la vitesse de minage de $+40\%$.
* **Niveau 20 (Passif 2 - Onde Tellurique) :** Miner un minerai précieux libère une onde brisant automatiquement les 8 blocs de roche environnants.
* **Niveau 35 (Actif - Fissure Sismique) :** Cooldown 60s. Envoie une onde de choc au sol sur 12 blocs qui détruit la roche tendre et étourdit les monstres souterrains.
* **Niveau 50 (Ultime - Cataclysme Minier) :** Cooldown 180s. Déclenche une extraction séismique de zone ($7 \times 7 \times 7$) filtrant automatiquement les blocs sans valeur pour ne déposer dans l'inventaire que les minerais nobles.

#### 2. Le Prospecteur (Maître de la Rareté)
* **Niveau 10 (Passif 1 - Sens Minéralogique) :** $15\%$ de chances de doubler la quantité de minerais bruts extraits lors du minage de cobalt et titane.
* **Niveau 20 (Passif 2 - Détecteur d'Aether) :** Une particule dorée guide le prospecteur vers le filon précieux le plus proche dans un rayon de 16 blocs.
* **Niveau 35 (Actif - Vision Spectrale) :** Cooldown 90s. Révèle en surbrillance (Glowing) tous les filons d'améthyste et d'opale à travers la roche pendant 8 secondes.
* **Niveau 50 (Ultime - Faveur de l'Opale) :** Cooldown 300s. Pendant 30 secondes, chaque minerai extrait a $5\%$ de chances supplémentaires de dropper une Gemme d'Opale Pure intacte.

#### 3. Le Terrasculpteur (Vitesse & Industrie)
* **Niveau 10 (Passif 1 - Auto-Fusion Thermique) :** Les minerais de cobalt et de titane brisés sont instantanément fondus en lingots directement dans l'inventaire.
* **Niveau 20 (Passif 2 - Compaction Minérale) :** Les blocs de roche et minerais ordinaires sont automatiquement compactés en blocs de 9 pour économiser l'espace d'inventaire.
* **Niveau 35 (Actif - Foreuse Vivante) :** Cooldown 60s. Confère Célérité IV (Haste IV) et Vitesse II pendant 20 secondes sans aucune perte de durabilité sur la pioche.
* **Niveau 50 (Ultime - Excavation Dimensionnelle) :** Cooldown 240s. Creuse une galerie horizontale parfaite de $3 \times 3$ sur une longueur de 32 blocs en une seule seconde.

---

## Chapitre 7 : L'Ordre de la Terre (Agriculture & Génétique) & Arbres des 3 Spécialisations

> **Devise :** *"Ce que la graine reçoit, l'empire le récolte."*  
> **Gain d'XP d'Ordre :** Récolte de plantes moddées (10 XP), fertilisation au composteur (25 XP), mutation génétique réussie (150 XP), domestication de bête de guerre (200 XP).

### A. La Sève Primordiale (Autonomie Agricole Complète)
Dépensable auprès de *Dame Sylvana* :
* *Sérum de Croissance Accélérée (2 Sèves) :* Fait mûrir instantanément un carré de $3 \times 3$ cultures.
* *Élixir de Pureté Génétique (8 Sèves) :* Augmente de $+20\%$ le potentiel d'ADN d'un animal lors de l'insémination.
* *Graine de Mana Protectrice (12 Sèves) :* Plante un arbuste soignant les alliés dans un rayon de 8 blocs au sein de la base de faction.

### B. Arbres des 3 Spécialisations de l'Ordre de la Terre (Niveaux 1 à 50)

#### 1. Le Moissonneur (Agriculture Industrielle & Alchimie)
* **Niveau 10 (Passif 1 - Germination Spontanée) :** Les cultures autour du joueur poussent $25\%$ plus vite de manière passive.
* **Niveau 20 (Passif 2 - Double Moisson) :** $30\%$ de chances d'obtenir une récolte doublée avec réensemencement automatique du sol.
* **Niveau 35 (Actif - Pluie Céleste) :** Cooldown 60s. Invoque un nuage fertilisant de $9 \times 9$ blocs qui fait progresser toutes les cultures de 2 stades immédiatement.
* **Niveau 50 (Ultime - Moisson Primordiale) :** Cooldown 300s. Déclenche une récolte instantanée de toutes les cultures du chunk avec bonus de $50\%$ de rendement et $10\%$ de chances de drop de graines de diamant.

#### 2. Le Sylviculteur (Botanique & Biomes)
* **Niveau 10 (Passif 1 - Abattage en Chaîne) :** Couper le bloc de base d'un arbre abat l'intégralité du tronc d'un seul coup.
* **Niveau 20 (Passif 2 - Écorce Vivante) :** Le bois récolté a $20\%$ de chances de fournir de la Résine d'Aether, ingrédient de base des parchemins d'enchantement.
* **Niveau 35 (Actif - Racines Entravantes) :** Cooldown 45s. Fait jaillir des racines sous les pieds d'un assaillant pour l'immobiliser pendant 3 secondes.
* **Niveau 50 (Ultime - Arbre de Vie) :** Cooldown 360s. Fait pousser un Arbre Ancestral au sein du claim qui régénère l'endurance et la santé de tous les membres de faction à 16 blocs pendant 60 secondes.

#### 3. L'Éleveur-Généticien (Bio-Ingénierie & Bêtes de Guerre)
* **Niveau 10 (Passif 1 - Prélèvement Pur) :** Les seringues d'extraction génétique prélèvent de l'ADN avec un bonus fixe de $+15\%$ de pureté.
* **Niveau 20 (Passif 2 - Élevage Accéléré) :** Le délai de reproduction des bêtes de faction est réduit de $50\%$.
* **Niveau 35 (Actif - Appel de la Meute) :** Cooldown 60s. Téléporte instantanément vos familiers de guerre (loups, sangliers) à vos côtés avec un boost de Vitesse II et Résistance I.
* **Niveau 50 (Ultime - Mutation Parfaite) :** Cooldown 600s. Garantit que la prochaine descendance animale obtiendra un ADN parfait à $100\%$ avec déblocage du gène légendaire de votre choix.

---

## Chapitre 8 : Le Répertoire Exhaustif des 54 Méthodes Économiques

Pour qu'aucun joueur ne se sente restreint, Opal Heaven intègre **54 méthodes économiques uniques**, calibrées et réparties équitablement sur les 3 Ordres (18 par Ordre).

```
                      TABLEAU DE BORD DES 54 MÉTHODES ÉCONOMIQUES
   ORDRE DU SANG (PvP)          ORDRE DES ABYSSES (Mineur)    ORDRE DE LA TERRE (Agri)
   1. Contrats de Prime Bounties 19. Extraction Cobalt Brut    37. Vente Blé Céleste de Gros
   2. Duels d'Arène Classée      20. Raffinage Titane Fonderie 38. Élevage Loups de Guerre
   3. Primes de Killstreaks      21. Quête Failles d'Aetherite 39. Culture Graines de Fer
   4. Rançons de Tête KOTH       22. Vente Poussière Basalte   40. Culture Graines d'Or
   5. Pillage de Convois Bât     23. Forage de Roches Nobles   41. Culture Graines Diamant
   6. Défense de Totem Warzone   24. Géodes d'Améthystes       42. Culture Graines Opale
   7. Coffres d'Airdrop Céleste  25. Confection Enclumes Acier 43. Production Composteur
   8. Vente Marques du Sang      26. Prospection Pépites d'Or  44. Pêche au Timing (Poissons)
   9. Vente Rations de Guerre    27. Cristaux Purs d'Opale     45. Pêche Harpon Léviathan
   10. Mercenariat de Faction    28. Sables de Quartz d'Aether 46. Extraction ADN Purifié
   11. Tournois d'Arène Hebdo    29. Vente Pierres Fortifiées  47. Vente Bœufs Caparaçonnés
   12. Escorte Marchand Zephyr   30. Dalles d'Obsidienne Dense 48. Sève d'Arbre Primordial
   13. Paris Légaux de Duels     31. Orbes de Désenchantement  49. Vente Ronces Défensives
   14. Éliminations Boss Donjon  32. Contrats Minage PNJ       50. Pépinière Arbres Rares
   15. Vente Armures T4 Pillées  33. Fourniture Chantiers Guilde51. Confection Festins Alchi
   16. Sécurisation Faille Sang  34. Recyclage Armes Brisées   52. Huiles Fertiles Rares
   17. Garde du Corps Minier     35. Location Foreuse Runique  53. Lait Purificateur Doré
   18. Chasse Champions Ennemis  36. Bourse aux Lingots HDV   54. Vente Fleurs Bioluminesc.
```

### Rendements Moyens Équilibrés :
* **Joueur Débutant (Niveau 1-15) :** $2\,500$ à $4\,000$ pièces d'or / heure.
* **Joueur Intermédiaire (Niveau 16-35) :** $8\,000$ à $14\,000$ pièces d'or / heure.
* **Joueur Expert / Maître d'Ordre (Niveau 36-50) :** $25\,000$ à $45\,000$ pièces d'or / heure.
* **Principe d'Équité :** Aucune méthode minière ou agricole ne dépasse de plus de $10\%$ le rendement d'un joueur PvP de même niveau enchaînant arènes et primes de guerre.

---

# TOME III : LA CARTOGRAPHIE CÉLESTE, LES 40 BIOMES & LES DONJONS

## Chapitre 9 : L'Architecture Multi-Mondes Dédiée

Pour assurer un débit constant de 20 TPS même avec 300 joueurs actifs et empêcher la dégradation des territoires de factions par des carrières géantes, Opal Heaven opère sur un réseau de **5 mondes compartimentés**.

```
                   CARTE DU RÉSEAU DES 5 MONDES CÉLESTES
                                [ HUB CÉLESTE ]
                            (Sanctuaire Paisible 0 PvP)
                                       │
      ┌──────────────────┬─────────────┴─────────────┬──────────────────┐
      ▼                  ▼                           ▼                  ▼
[ OVERWORLD FACTION ] [ WARZONE ACTIVE ]      [ MONDE MINAGE ]   [ ROYAUME DONJONS ]
10 000 x 10 000       1 500 x 1 500           5 000 x 5 000      Instances PvE
40 Biomes Moddés      PvP Hardcore Non-Stop   Reset Mensuel Net  Cryptes Privatisées
Bases Permanentes     KOTH, Totem & Airdrops  Ressources Brutes  Groupes de 1 à 4
```

### Spécifications & Rôles des 5 Mondes :
1. **Le Hub Céleste (`hub`) :** Sanctuaire flottant dans le vide astral. Dégâts désactivés, faim gelée, inventaires protégés. Regroupe l'ensemble des PNJ vivants, la Banque de Cassandra, la Grande Bourse, les mannequins d'entraînement de parade et les Portails Stellaires.
2. **L'Overworld de Faction (`opal_main`) :** Carte pérenne de $10\,000 \times 10\,000$ blocs générée avec les 40 biomes moddés exclusifs. C'est le monde sacré des bases, des claims, des fermes industrielles et de la guerre de territoire. Aucun reset de terrain n'y intervient pendant toute la saison de 6 mois.
3. **La Warzone Sanglante (`warzone`) :** Arène circulaire de $1\,500 \times 1\,500$ blocs ceinturant le Cratère de l'Opale. Perte de stuff intégrale, claims impossibles, théâtre quotidien du KOTH, du Totem de Faction et des Airdrops célestes.
4. **Le Monde Minage & Ressources (`mining_world`) :** Dimension de $5\,000 \times 5\,000$ blocs dédiée à l'extraction brute de masse. **Ce monde est intégralement régénéré le 1er de chaque mois à 04h00 du matin.** Cela garantit des filons neufs permanents et évite que l'Overworld de faction ne ressemble à un champ de cratères gruyère.
5. **Le Sanctuaire des Donjons (`dungeon_realm`) :** Dimension instanciée hébergeant les donjons PvE scellés. Chaque groupe de 1 à 4 joueurs dispose de sa propre salle isolée sans risque d'intrusion extérieure.

---

## Chapitre 10 : Le Registre Intégral des 40 Biomes Moddés à Auras

Chaque biome d'Opal Heaven possède une identité visuelle saisissante et diffuse une **Aura Environnementale passive** qui influence les joueurs présents.

```
                      LE PANTHÉON DES 40 BIOMES MODDÉS
┌────┬─────────────────────────────┬──────────────────────────┬─────────────────────────────┐
│ N° │ Nom du Biome Moddé          │ Thématique Visuelle      │ Aura & Effet Environnemental│
├────┼─────────────────────────────┼──────────────────────────┼─────────────────────────────┤
│ 01 │ **Forêt d'Opale Éternelle** │ Arbres d'albâtre, herbe rose│ Régénération posture +15%   │
│ 02 │ **Failles de Basalte Ardent**│ Roches noires, lave violette│ Résistance au feu naturelle │
│ 03 │ **Pics Auroraux d'Aether**  │ Montagnes flottantes cyan│ Hauteur de saut +1.5 bloc   │
│ 04 │ **Canopée Primordiale**     │ Arbres géants de 60 blocs│ Vitesse de pousse plantes +30%│
│ 05 │ **Désert de Silice Stellaire**│ Sable blanc, cristaux d'or │ Vitesse de sprint +10%      │
│ 06 │ **Abysses Luminescents**    │ Champignons géants azur  │ Vision nocturne permanente  │
│ 07 │ **Toundra d'Acier Glacial** │ Glace compacte et cobalt │ Ralentit les assaillants -10%│
│ 08 │ **Vallée du Sang Céleste**  │ Terre pourpre, rivières rouges│ Dégâts critiques mêlée +10% │
│ 09 │ **Jungle des Ronces Vives** │ Vignes épineuses et brume│ Poison adverse prolongé +2s │
│ 10 │ **Plateau Tectonique**      │ Dalles de pierre craquelée│ Surcharges minage +25% zone │
│ 11 │ **Marais d'Améthyste**      │ Nénuphars violets géants │ Coût endurance sorts -20%   │
│ 12 │ **Gorge des Tempêtes**      │ Éclairs célestes au zénith│ Vitesse d'attaque +10%      │
│ 13 │ **Oasis Astrale**           │ Palmiers d'or, eau cristalline│ Restauration faim x2.0      │
│ 14 │ **Cavernes de Titane Pur**  │ Parois métalliques nues  │ Résistance physique +10%    │
│ 15 │ **Champs de Blé Divin**     │ Épis géants dorés de 3m  │ Rendement récolte x2.0      │
│ 16 │ **Forêt de Brume Spectrale**│ Brouillard dense argenté │ Portée détection mobs -50%  │
│ 17 │ **Cratère d'Impact Stellaire**│ Vitrifications prismatiques│ Chance drop opale doublée   │
│ 18 │ **Mangrove Tellurique**     │ Racines immergées massives│ Vitesse de nage +40%        │
│ 19 │ **Monts de Cobalt Souverain**│ Crêtes rocheuses bleutées│ Durabilité outils x1.5      │
│ 20 │ **Savane d'Émeraude**       │ Hautes herbes vert pomme │ Vitesse montures +25%       │
│ 21 │ **Archipel Flottant Boréal**│ Îles suspendues dans le vide│ Annule chutes jusqu'à 20m │
│ 22 │ **Lagon des Léviathans**    │ Océan profond bleu outremer│ Rareté poissons pêchés +30% │
│ 23 │ **Steppes Fongiques Rouges**│ Mycènes incandescentes   │ Poudre de soin spontanée 5% │
│ 24 │ **Fissure du Néant**        │ Failles d'obsidienne noire│ Dégâts du vide annulés      │
│ 25 │ **Bosquet des Murmures**    │ Saules pleureurs dorés   │ Gain XP d'Ordre +20%        │
│ 26 │ **Terres Brûlées d'Ignis**  │ Cendres chaudes et fumerolles│ Coups de feu embrasent 3s   │
│ 27 │ **Hauts-Plateaux Cristallins**│ Dômes de quartz translucide│ Résistance aux flèches +20% │
│ 28 │ **Clairière des Fées d'Aether**│ Poussières stellaires en l'air│ Régénération cœurs passive  │
│ 29 │ **Canyon de Cuivre Ancien** │ Parois orangées oxydées  │ Vitesse de fonte four x2.0  │
│ 30 │ **Désert d'Ossements Géants**│ Squelettes fossilisés titanesques│ Dégâts contre boss +15% │
│ 31 │ **Mer de Corail Lumineux**  │ Coraux phosphorescents   │ Respiration aquatique infinie│
│ 32 │ **Plaine des Géants de Pierre**│ Mégalithes de basalte 3D │ Résistance au recul +40%    │
│ 33 │ **Vallon des Ruches Célestes**│ Nids d'abeilles d'or géants│ Production de cire alchimique│
│ 34 │ **Pics Sanglants d'Aramor** │ Aiguilles rocheuses rouge carmin│ Vol de vie augmenté de +5% │
│ 35 │ **Forêt Pétrifiée**         │ Troncs minéralisés incassables│ Bois d'écorce ignifugée     │
│ 36 │ **Banquise Scintillante**   │ Icebergs taillés en prismes│ Glissade augmentée, drift   │
│ 37 │ **Crevasse Magmatique Profonde**│ Failles de lave sous y=0 │ Titane brut enrichi (+1 drop)│
│ 38 │ **Sanctuaire des Tempêtes** │ Vents tourbillonnants    │ Flèches volent 30% plus loin│
│ 39 │ **Jardins Suspendus d'Éden**│ Cascades d'eau pure céleste│ Pureté génétique ADN +20%   │
│ 40 │ **Le Cœur Céleste Déchu**   │ Ruines de l'Opale Originelle│ Énergie arcanique absolue   │
└────┴─────────────────────────────┴──────────────────────────┴─────────────────────────────┘
```

---

## Chapitre 11 : Les Donjons Instanciés PvE & Les 3 Grands Boss

Accessibles via des **Clés de Donjon** forgées à base de Fragments Célestes, les donjons accueillent des escouades de 1 à 4 joueurs dans une course contre la montre de 20 minutes chrono.

```
                   STRUCTURE SÉQUENTIELLE D'UN RUN DE DONJON
    [ INSERTION DE CLÉ ] ──► [ SALLE 1 : L'ARÈNE D'ÉLITE ]
                                    3 Vagues de monstres renforcés
                                    Rupture de posture requise
                                             │
                                             ▼
    [ ARÈNE DU BOSS ] ◄── [ SALLE 2 : L'ÉPREUVE DES AGILITÉS ]
    Patterns complexes          Parcours de dalles mobiles
    Parades synchronisées       Pièges de flammes & précipice
```

### Les 3 Grands Donjons de Saison :

#### 1. La Crypte du Seigneur Sanglant (Donjon Martiale - Tier 1)
* **Boss :** `opal:boss_vampire_lord` (**500 HP**).
* **Patterns de Combat :**
  * *Balayage Mortel ($360^\circ$) :* Toutes les 6 secondes, le boss arme sa faux pendant 15 ticks. **Doit impérativement être paré au sneak.** En cas d'échec, le joueur perd 8 cœurs et le boss régénère $10\%$ de sa santé.
  * *Dash d'Ombre :* Se téléporte instantanément dans le dos du joueur le plus éloigné.
* **Stratégie Recommandée :** Former un cercle, un Bretteur attire l'aggro en parade parfaite pendant que les alliés frappent dans le dos.

#### 2. Le Cœur de la Forge Tectonique (Donjon Industriel - Tier 2)
* **Boss :** `opal:boss_basalt_golem` (**800 HP**).
* **Patterns de Combat :**
  * *Écrasement Tectonique (Ground Slam) :* Lève ses poings massifs pendant 1.5s (télégraphie visuelle de lave incandescente), puis frappe le sol : onde sismique brisant la posture de quiconque ne saute pas au moment précis de l'impact.
  * *Éruption Magmatique :* Des geysers de lave jaillissent sous les joueurs immobiles depuis plus de 2 secondes.
* **Stratégie Recommandée :** Rester constamment en mouvement de saut synchronisé (Jump Timing) et détruire ses jointures de titane.

#### 3. La Canopée de l'Arbre Primordial (Donjon Sylvestre - Tier 3)
* **Boss :** `opal:boss_bramble_queen` (**650 HP**).
* **Patterns de Combat :**
  * *Prisons Végétales :* Enracine 2 joueurs sur place dans des buissons d'épines. Les autres joueurs doivent briser les ronces à la hache en moins de 4 secondes avant qu'elles n'explosent.
  * *Barrage d'Épines Perforantes :* Tire 16 épines téléguidées esquivables uniquement derrière les piliers d'écorce de l'arène.
* **Récompenses Uniques :** Plans de craft d'Armures T5 d'Opale Pure, Graines de Gemmes Légendaires et Titre de Héros de Donjon.

---

# TOME IV : L'ÉCONOMIE D'AIRAIN, BOURSE & GRAND LIVRE D'ARTISANAT

## Chapitre 12 : L'Écosystème Monétaire à 3 Devises & Barème des Prix PNJ

Pour garantir une valeur stable à la monnaie sur 6 mois, l'économie sépare les rôles par **trois devises cloisonnées** et un circuit strict de régulation.

```
                      LE TRIANGLE DES MONNAIES D'OPAL HEAVEN
                          [ PIÈCES D'OR CÉLESTES ]
                         (Monnaie d'Échange Globale)
                                     │
           ┌─────────────────────────┴─────────────────────────┐
           ▼                                                   ▼
   [ MARQUES DU SANG ]                                 [ OPALE PURE D'AETHER ]
   Monnaie Pure PvP                                    Étalon-Or de Prestige
   Non échangeable, liée à l'âme                       Rareté absolue, Endgame
   Boutique de rations & guerre                        Parangon & Claims Capitaux
```

### A. Les 3 Monnaies et Leurs Droits d'Usage :
1. **Les Pièces d'Or Célestes :** Monnaie universelle du serveur. Utilisée pour l'Hôtel des Ventes, la création de faction, les téléportations et le commerce de joueur à joueur.
2. **Les Marques du Sang :** Monnaie exclusive PvP, non transférable. Obtenue uniquement par les kills en Warzone et arènes. Sert à acheter les rations, parchemins et réparations de guerre pour garantir l'autonomie des guerriers.
3. **L'Opale Pure d'Aether :** Matériau de réserve précieux (9 gemmes d'Opale = 1 Bloc d'Opale). Sert d'étalon de richesse et de carburant pour les technologies de faction de palier V.

### B. Le Barème Officiel des Prix Garantis PNJ (AdminShop / Puits de Secours)
Le marchand royal du Hub rachète des volumes quotidiens garantis pour assurer qu'aucune ressource ne tombe à 0 pièce :
* **Minerais & Matériaux Bruts :**
  * 1× Minerai de Fer brut : $5\text{ Or}$ | 1× Lingot de Fer : $7\text{ Or}$
  * 1× Cobalt brut : $12\text{ Or}$ | 1× Lingot de Cobalt : $18\text{ Or}$
  * 1× Titane brut : $30\text{ Or}$ | 1× Lingot de Titane : $45\text{ Or}$
  * 1× Cristal d'Aetherite : $110\text{ Or}$
  * 1× Gemme d'Opale Pure : $500\text{ Or}$ (prix plancher garanti)
* **Produits Agricoles & Élevage :**
  * 64× Blé Céleste : $80\text{ Or}$
  * 16× Baies de Mana : $120\text{ Or}$
  * 1× Poussière Fertile de Composteur : $40\text{ Or}$
  * 1× Fiole d'ADN Animal $80\%+$ : $350\text{ Or}$
* **Taxes & Sinks de Destruction Monétaire :**
  * Taxe sur chaque vente réussie à l'Hôtel des Ventes : **$5.0\%$ détruit définitivement**.
  * Taxe sur les primes de chasse (Bounties) : **$15.0\%$ de frais de guilde**.
  * Taxe de revente rapide (Anti-Spéculation) : **$25.0\%$ de surtaxe** si un lot acheté à l'HDV est remis en vente dans les 24h.

---

## Chapitre 13 : L'Hôtel des Ventes (`/ah`) & Ordres d'Achat Boursiers

L'Hôtel des Ventes repose sur une interface tactile Bedrock (`FormUI`) permettant des transactions asynchrones sécurisées.

```
                      INTERFACE FORMUI DE L'HÔTEL DES VENTES
   ┌────────────────────────────────────────────────────────────────────────┐
   │ 💎 HÔTEL DES VENTES CÉLESTE (`/ah`)                                    │
   ├────────────────────────────────────────────────────────────────────────┤
   │ [ 🗡️ Armes & Armures ]  [ ⛏️ Minerais & Lingots ]  [ 🌾 Agriculture ]   │
   │ [ 📜 Parchemins & Livres ] [ 📦 Blocs Fortifiés ]  [ 🔍 Rechercher ]   │
   ├────────────────────────────────────────────────────────────────────────┤
   │ [ ➕ Déposer une Vente ]  [ 📊 Bourse : Placer un Ordre d'Achat ]       │
   │ [ 📦 Mes Ventes en Cours ] [ 💰 Récupérer mes Gains ]                  │
   └────────────────────────────────────────────────────────────────────────┘
```

### Le Système Boursier d'Ordres d'Achat (Buy Orders) :
1. Un acheteur a besoin de 500 lingots de Titane sans vouloir camper devant l'écran.
2. Il tape `/ah buyorder`, sélectionne *Lingot de Titane*, saisit la quantité (500) et fixe son prix unitaire ($50\text{ Or}$).
3. Le montant total ($25\,000\text{ Or}$) est instantanément prélevé de son compte et séquestré dans la base de données SQLite.
4. Dès qu'un mineur des Abysses extrait du titane, il ouvre l'HDV, consulte les ordres d'achat actifs, clique sur *« Remplir l'Offre »* et encaisse ses pièces immédiatement, tandis que les lingots sont livrés dans le casier de stockage de l'acheteur.

---

## Chapitre 14 : Le Grand Catalogue des Recettes de Crafting Moddé

Chaque recette moddée d'Opal Heaven est confectionnée dans la table d'artisanat classique ou l'Enclume d'Acier de Thorgar.

```
                          PATTERNS DES RECETTES MAJEURES
   ARROSOIR EN FER (T2)        COMPOSTEUR ACCÉLÉRATION     BÂTON DE GUÉRISON (HEAL)
     [ F ] [ . ] [ F ]           [ C ] [ T ] [ C ]             [ . ] [ O ] [ . ]
     [ F ] [ S ] [ F ]           [ B ] [ V ] [ B ]             [ . ] [ T ] [ . ]
     [ . ] [ F ] [ . ]           [ F ] [ F ] [ F ]             [ . ] [ T ] [ . ]
   F = Lingot de Fer           C = Cuivre / T = Trémie       O = Gemme d'Opale
   S = Seau d'Eau              B = Basalte / V = Verre       T = Bâton de Titane
```

### A. Les 5 Tiers d'Armures Moddées :
1. **Tier 1 — Set de Cuir Céleste & Fer :** Cuir traité + lingots de fer sur l'établi. Armure d'onboarding, 100% de vitesse.
2. **Tier 2 — Set de Quartz & Cobalt :** 24 lingots de Cobalt + blocs de quartz taillés. Résistance au recul $+20\%$, vitesse $-3\%$.
3. **Tier 3 — Set de Titane Tellurique :** 24 lingots de Titane affinés à la Fonderie. Réduit la montée de posture lors d'une parade de $-15\%$, vitesse $-5\%$.
4. **Tier 4 — Set de Rubis d'Aetherite :** 24 cristaux d'Aetherite + 4 cœurs de feu de donjon. Confère $+10\%$ de vol de vie en mêlée, vitesse $-2\%$.
5. **Tier 5 — Armure Divine d'Opale Pure :** 24 Gemmes d'Opale Céleste forgées sur l'Enclume de Thorgar avec un Sceau Céleste. Réduit les cooldowns de toutes les compétences de $-15\%$ et confère Vitesse $+2\%$.

### B. Les 4 Bâtons d'Action Tactiques :
* **Bâton de Guérison (Heal Stick) :** 1 Gemme d'Opale + 2 Bâtons de Titane + 2 Larmes de Ghast. *(Restaure 4 cœurs, 3 charges, cooldown 45s)*.
* **Bâton de Vitesse (Speed Stick) :** 1 Cristal d'Aetherite + 2 Bâtons de Cobalt + 2 Poudres de Sucre Astral. *(Speed II pendant 15s, 5 charges)*.
* **Bâton de Répulsion (Force Stick) :** 1 Bloc de Basalte dense + 2 Bâtons de Titane + 1 Piston. *(Onde de choc repoussant à 6 blocs, 3 charges)*.
* **Bâton de Brèche (Breach Stick) :** 1 TNT de Brèche + 2 Bâtons de Titane + 1 Gemme d'Améthyste. *(Désactive une tourelle ou herse de faction pendant 20s, utilisable uniquement 18h-23h)*.

### C. La Machinerie & Blocs Utilitaires :
* **Composteur d'Accélération :** 4 Lingots de Cuivre + 1 Trémie + 2 Blocs de Basalte + 1 Verre Blindé + 2 Lingots de Fer.
* **Fonderie Céleste :** 4 Blocs de Titane + 4 Briques de Basalte + 1 Cœur d'Améthyste Stellaire au centre.
* **Balise Anti-Perles (Ender Disruptor) :** 1 Œil d'Ender + 4 Obsidiennes Denses + 4 Cristaux d'Aetherite.
* **Piques d'Acier au Sol (x4) :** 3 Épées en Fer usagées + 3 Lingots de Fer en barre horizontale.
* **Pierre Fortifiée (x4) :** 4 Blocs de Pierre Taillée + 4 Lingots de Fer + 1 Poudre de Basalte.
* **Obsidienne Dense de Faille (x2) :** 4 Obsidiennes normales + 4 Lingots de Titane + 1 Éclat Stellaire.

---

## Chapitre 15 : Le Registre des Tables de Drop & Pourcentages Précis

### A. Minage des Roches & Minerais Moddés :
* **Filon de Cobalt :** 1 Cobalt brut ($100\%$) | Avec Fortune III : 1 à 3 ($120\%$ espérance).
* **Filon de Titane :** 1 Titane brut ($100\%$) | Avec Fortune III : 1 à 2 ($140\%$ espérance).
* **Filon d'Aetherite :** 1 Cristal ($100\%$) | Avec Fortune III : 1 à 2 ($125\%$ espérance).
* **Filon d'Opale Céleste :** 1 Gemme d'Opale ($100\%$). **Insensible à Fortune** (anti-duplication de richesse).
* **Pierre ordinaire en Faille :** $1.5\%$ de chance de faire jaillir un Fragment d'Améthyste spontaneously.

### B. Table de Pêche Active au Mini-Jeu :
* **Ferrage Manqué :** 0 butin, $100\%$ de perte d'appât.
* **Ferrage Standard (Zone Verte) :**
  * $70\%$ : Poissons ordinaires comestibles (Cabillaud, Saumon d'argent).
  * $20\%$ : Poissons consommables rares (Anguille boréale Speed III, Carpe posture).
  * $8\%$ : Trésor englouti (Lingot de titane, livre d'enchantement commun).
  * $2\%$ : Déclenchement de la fureur du Léviathan marin.
* **Ferrage Parfait (Zone Dorée ★) :**
  * $50\%$ : Butin standard doublé (x2).
  * $35\%$ : Coffre d'Aether scellé (contenant parchemins d'enchantement épiques et cristaux).
  * $15\%$ : Fragment d'Opale Céleste ou Écaille d'Abysse légendaire.

### C. Coffres de Largage d'Airdrops Célestes en Warzone :
* **Airdrop Commun (Plafond 60% des largages) :** 32 Lingots de fer, 16 Cobalt, 2 Pommes d'or, 1 Bâton de vitesse (2 charges), 2 500 Or.
* **Airdrop Épique (30% des largages) :** 16 Lingots de Titane, 4 Cristaux d'Aetherite, 1 Pièce d'armure T3, 1 Bâton de Guérison, 1 Sceau de Sauvegarde, 8 000 Or.
* **Airdrop Légendaire (10% des largages) :** 1 Arme T4 en Aetherite enchantée, 2 Gemmes d'Opale Céleste, 1 Parchemin de Garde d'Airain (100%), 1 Clé de Donjon Primordiale, 25 000 Or.

---

# TOME V : LA GUERRE DE FACTION, TERRITOIRES & WARZONE

## Chapitre 16 : Système de Factions, Claims, DTR & Tech Tree

Les factions d'Opal Heaven représentent l'aboutissement politique et militaire du jeu. Elles sont régulées par un système de puissance numérique intransigeant : le **DTR (Death-To-Regen)**.

```
                    MÉCANIQUE DE DTR & STATUT RAIDABLE
   FACTION SAINE (DTR > 0.0)             FACTION RAIDABLE (DTR <= 0.0)
   ┌───────────────────────────────┐     ┌───────────────────────────────┐
   │ • Claims protégés             │     │ ⚠️ DÉFENSES EFFONDRÉES !       │
   │ • Portes & coffres verrouillés│ ──► │ • Portes ouvrables par tous   │
   │ • Régénération Nexus active   │     │ • Coffres pillables           │
   │ • Cœur de Faction défendu     │     │ • Claims sur-claimables       │
   └───────────────────────────────┘     └───────────────────────────────┘
```

### A. Les Règles Fondamentales du DTR :
* **Plafond de Membres :** Maximum **10 joueurs** par faction.
* **Calcul du DTR Maximal :** Chaque membre connecté ou déconnecté octroie **$3.0$ points de DTR**. (Exemple : faction pleine de 10 joueurs = **$30.0$ DTR max**).
* **Pénalité de Décès :** Chaque mort d'un membre (en Warzone ou Overworld) retire immédiatement **$1.0$ point de DTR**.
* **Statut Raidable (Sous-Pillage) :** Si le DTR descend à **$0.0$ ou moins** :
  * Les coffres et portes de la faction deviennent immédiatement ouvrables par n'importe quel joueur étranger.
  * Les factions ennemies peuvent sur-claimer le territoire chunk par chunk.
* **Régénération du DTR :**
  * Le DTR remonte au rythme de **$+1.0$ point toutes les 45 minutes**, à condition qu'au moins 1 membre soit connecté.
  * **Freeze de Mort :** Tout décès bloque la régénération du DTR pendant un délai de gel strict de **15 minutes**.

### B. L'Arbre Technologique de Faction (5 Paliers de Tech Tree)
L'évolution d'une faction s'effectue via l'interface `/f tech` au Cœur de Faction :

| Palier | Nom du Niveau | Coût en Ressources | Déblocages & Avantages Majeurs |
| :---: | :--- | :--- | :--- |
| **I** | **Campement Débutant** | $10\,000\text{ Or}$ + 64 Lingots Cobalt | 5 Claims maximum, Coffre de faction 27 slots, Cœur à 1 000 HP. |
| **II** | **Bastion d'Acier** | $35\,000\text{ Or}$ + 64 Lingots Titane | 12 Claims max, Coffre 54 slots (double coffre), Vitesse régen Cœur +15%. |
| **III** | **Citadelle d'Aether** | $90\,000\text{ Or}$ + 32 Cristaux Aetherite | 22 Claims max, Déblocage des Piques d'acier et Dalles d'électrocution. |
| **IV** | **Forteresse Céleste** | $220\,000\text{ Or}$ + 16 Gemmes d'Opale | 32 Claims max, Coffre géant 108 slots, Radar d'alerte intrusion à 32 blocs. |
| **V** | **Empire Impérial** | $500\,000\text{ Or}$ + 32 Blocs d'Opale Pure | 45 Claims max, Invulnérabilité passive hors raid, Balise céleste de régénération. |

---

## Chapitre 17 : L'Art du Siège, Fortifications & Horaires de Raid

Pour permettre aux joueurs de vivre une vie scolaire ou professionnelle normale sans craindre d'être détruits la nuit, les mécaniques de raid sont strictement encadrées par une horloge serveur inviolable.

```
                      FENÊTRE HORAIRE STRICTE DE RAID
    00:00               18:00                             23:00         24:00
      [══ PAIX ABSOLUE ══][══════ FENÊTRE DE SIÈGE ACTIVE ══════][══ PAIX ABSOLUE ══]
      TNT inoffensive      TNT de Brèche active (x4 dégâts)      TNT inoffensive
      Claims invulnérables Bâtons de brèche & Marteaux de siège Claims invulnérables
```

### A. Les Règles d'Airain du Siège :
1. **La Plage de Raid (18h00 - 23h00 UTC+2) :** Seul cet intervalle autorise les explosions à endommager les blocs de claims et les Cœurs de Guilde. Hors de cette plage, la TNT explose sans détruire aucun bloc de faction.
2. **L'Artillerie de Siège :**
   * **TNT de Brèche :** Inflige des dégâts quadruplés ($x4.0$) aux blocs fortifiés.
   * **Marteau de Béliers :** Permet d'enfoncer les Portes Blindées de faction au corps-à-corps (50 coups pour forcer une porte).
3. **Le Bouclier de Trêve de 24h (Truce Shield) :**
   * Achetable une fois toutes les 2 semaines auprès de la Banque pour $120\,000\text{ Or}$.
   * Permet à une faction en déplacement ou en sous-effectif d'activer 24 heures consécutives de protection totale, désactivant les raids sur ses claims pendant toute une soirée.

---

## Chapitre 18 : La Warzone Dynamique & Le Calendrier Hebdomadaire

### A. Topographie de l'Arène Centrale
La Warzone ceinturant le Hub est conçue pour empêcher le camp de sortie et stimuler la manœuvre tactique :
* **Les 4 Ponts de Saut Célestes :** Quatre promontoires de projection empêchent les attaquants d'attendre les joueurs à la sortie du spawn.
* **Les Ruines Basses :** Tranchées et galeries semi-souterraines permettant de briser la ligne de vue des archers et de fuir les surnombres.
* **Le Cratère Central :** Lieu de capture ouvert à ciel ouvert où convergent les événements majeurs.

### B. Le Calendrier Hebdomadaire des Événements d'Opal Heaven

```
                   PLANNING OFFICIEL DES GRANDS ÉVÉNEMENTS
┌───────────┬──────────┬────────────────────────────────────────────────────────────┐
│ Jour      │ Heure    │ Événement & Lieu                                           │
├───────────┼──────────┼────────────────────────────────────────────────────────────┤
│ **Lundi** │ 19h00    │ **KOTH d'Ouverture** (Warzone - 4 min de capture)          │
│ **Mardi** │ 20h30    │ **Apparition de Zephyr le Nomade** (Biome Céleste Aléatoire)│
│ **Mercredi**│ 16h00  │ **Totem de Conquête de Guilde** (Flanc Est Warzone)        │
│ **Jeudi** │ 19h00    │ **KOTH des Arcanes** (Warzone - Butin parchemins 100%)     │
│ **Vendredi**│ 21h00  │ **Nuit Sanglante** (Warzone - XP & Marques de Sang x1.5)   │
│ **Samedi**│ 15h00    │ **Grand Totem de Guerre** (Warzone)                        │
│ **Samedi**│ 19h00    │ **KOTH Majeur du Samedi** (Clé de Box Mythique + 25k Or)  │
│ **Dimanche**│ 17h00  │ **Tournoi d'Arène Classée 1v1** (Hub Céleste)             │
│ **Dimanche**│ 19h00  │ **KOTH de Clôture Hebdomadaire**                           │
│ **Tous les jours**│ Toutes les 2h │ **Largage d'un Airdrop Céleste** (01h, 03h, 05h, etc.) │
└───────────┴──────────┴────────────────────────────────────────────────────────────┘
```

---

# TOME VI : LES MÉTIERS AVANCÉS & IMMERSION SOCIALE

## Chapitre 19 : La Pêche Céleste & Abyssale au Timing

La pêche d'Opal Heaven transforme une activité passive en un mini-jeu d'adresse visuel et réflexe géré sur l'ActionBar.

```
                    MINI-JEU D'ACTIONBAR AU FERRAGE (2.5s)
          [ ░░░░░░░░░░░░░░░░░░░░░[  VERT  |★|  VERT  ]░░░░░░░░░░░░░░░░░░░░ ]
                                         ▲
                           Curseur mobile oscillant
```

### A. Les 4 Cannes à Pêche Spécialisées :
1. **Canne en Bambou Tressé (T1) :** Zone verte large ($30\%$ de la barre). Attrape des poissons comestibles ordinaires.
2. **Canne Tellurique à Fil d'Acier (T2) :** Lestée de plomb en basalte. **Permet de pêcher dans la lave** des biomes volcaniques (poissons de feu, fragments de magma).
3. **Canne de Sève Céleste (T3) :** Imprégnée d'essences végétales. Zone verte resserrée ($15\%$). Attire les poissons d'Aether aux effets de potions immédiats.
4. **Harpon Abyssal Primordial (T4) :** Zone dorée centrale ultra-étroite ($5\%$). Permet de harponner des Trésors Scellés et de réveiller le Léviathan des Profondeurs.

### B. Les Poissons-Consommables d'Urgence :
* **Anguille Boréale :** Se mange en 0.2s ➔ Octroie `Speed III` pendant 15 secondes.
* **Carpe d'Améthyste :** Restaure immédiatement $40\%$ de la jauge de Posture du joueur en plein duel.
* **Piranha Magmatique :** Cuisiné en chaudron ➔ Immunité totale au feu et à la lave pendant 10 minutes.

---

## Chapitre 20 : L'Élevage Spécial & La Génétique Vivante (ADN)

L'élevage traditionnel est sublimé en une filière génétique de précision.

```
                    PROCÉDÉ D'INNOVATION BIOGÉNÉTIQUE
      [ ANIMAL SAUVAGE ] ──► (Seringue d'ADN) ──► [ FIOLE D'ADN BRUT (40-75%) ]
                                                           │
                                                           ▼
      [ EMBRYON D'ÉLITE 100% ] ◄── (Recombinaison) ◄── [ CENTRIFUGEUSE POSTE ]
```

### Les 4 Bêtes de Guerre de Saison :
1. **Le Loup de Guerre Génétique :** Équipé d'un caparaçon de titane. Inflige des morsures causant $+15\%$ de dégâts de posture et applique l'effet Saignement I.
2. **Le Sanglier Tectonique :** Monture lourde capable de charger en ligne droite (vitesse doublée) et de défoncer les herses en bois de faction.
3. **Le Faucon d'Aether :** Familier de reconnaissance aérienne volant au-dessus de son maître, appliquant l'effet Glowing à tous les ennemis invisibles dans un rayon de 25 blocs.
4. **Le Destrier Céleste :** Monture d'élite en armure d'opale (vitesse de sprint $0.45$, saut de 4.5 blocs, annulation des chutes jusqu'à 20 blocs).

---

## Chapitre 21 : Le Système d'Enchantements Moddés (Custom Enchants)

Un catalogue d'arcanes de combat appliqués par parchemins scellés sur l'Enclume d'Acier de Thorgar.

```
                 APPLICATION DES PARCHEMINS SUR L'ENCLUME
       [ PARCHEMIN D'ARCANES ] ──► [ ENCLUME D'ACIER DE THORGAR ]
                                             │
               ┌─────────────────────────────┴─────────────────────────────┐
               ▼                                                           ▼
      [ SUCCÈS (ex: 70%) ]                                        [ ÉCHEC (ex: 30%) ]
      L'enchantement est scellé                                   • Si Protégé : Livre perdu
      définitivement sur l'arme                                   • Si Non-Protégé : Arme BRISÉE
```

### Objets de Sécurisation Arcanique :
* **Poussière de Clarté d'Opale :** Augmente de $+5\%$ à $+20\%$ le taux de succès du parchemin.
* **Sceau de Sauvegarde Céleste :** Protège l'arme contre la destruction en cas d'échec de l'enchantement.

### Les 12 Enchantements Majeurs d'Opal Heaven :
1. **Lame d'Impact (Épée - Légendaire) :** Chaque coup critique inflige $+15\%$ de posture adverse en plus.
2. **Saignement Profond (Hache - Épique) :** Applique une hémorragie de 1 demi-cœur/s pendant 4 secondes.
3. **Vampirisme Sanguin (Dague - Épique) :** Convertit $15\%$ des dégâts causés en régénération directe.
4. **Garde d'Airain (Plastron - Légendaire) :** Réduit la durée du Stagger de 2.0s à 1.2s.
5. **Esquive Boréale (Bottes - Rare) :** $10\%$ de chances d'ignorer totalement un coup de flèche ennemi.
6. **Plumes d'Aether (Bottes - Commun) :** Annule les dégâts de chute jusqu'à 25 blocs.
7. **Foreuse Tellurique (Pioche - Épique) :** Mine en $3 \times 3$ en continu sans surcharge.
8. **Télékinésie Arcanique (Outils - Rare) :** Les blocs brisés vont directement dans l'inventaire.
9. **Moisson Doublée (Houe - Rare) :** $25\%$ de chances de doubler la récolte de semences d'élite.
10. **Frappe Foudroyante (Arc - Épique) :** Fait tomber un éclair céleste sur la cible à l'impact.
11. **Brise-Armure (Hache - Légendaire) :** Inflige une usure de durabilité x3 à l'armure de la victime.
12. **Résilience Sismique (Bouclier - Rare) :** Absorbe $50\%$ de la répulsion des explosions.

---

## Chapitre 22 : Le Panthéon des PNJ Vivants & Arborescences de Dialogue

Chaque PNJ du Hub propose une interaction vivante en FormUI avec choix narratifs et raccourcis d'action :

```
                  ARBORESCENCE D'ACCUEIL DE SIRE GABRIEL
               ┌──────────────────────────────────────────────┐
               │ 🧙 SIRE GABRIEL — LE PALADIN DÉCHU           │
               │ « Que la lumière de l'Opale guide tes pas,  │
               │ voyageur. Quel serment ton âme prête-t-elle ?»│
               ├──────────────────────────────────────────────┤
               │ [ ⚔️ Rejoindre l'Ordre du Sang (PvP) ]        │
               │ [ ⛏️ Rejoindre l'Ordre des Abysses (Minage) ] │
               │ [ 🌿 Rejoindre l'Ordre de la Terre (Agri) ]   │
               │ [ 🔄 Réinitialiser ma Spécialisation (50k Or)]│
               │ [ ❓ Découvrir l'Histoire de la Faille ]     │
               └──────────────────────────────────────────────┘
```

* **Thorgar le Forgeron :** *"Approche ton acier de ma forge céleste. Prends garde, le feu de l'abysse ne pardonne pas la maladresse."* ➔ Ouvre l'Enclume d'Acier, la Réparation d'Urgence et le Recyclage.
* **Dame Sylvana :** *"La terre chante sous les sabots des bêtes. Apporte-moi tes moissons géantes et reçois la bénédiction végétale."* ➔ Vente de semences et rachat des récoltes géantes.
* **Cassandra la Banquière :** *"Ici, votre fortune est plus en sécurité que dans le donjon le plus imprenable. Déposez, empruntez, prospérez."* ➔ Coffres Inviolables (Safe Boxes) et Bourse.
* **Zephyr le Marchand Nomade :** *"Je viens des confins des îles brisées. Mes reliques ont un prix, mais elles font les légendes."* ➔ Offre événementielle de 3 items mythiques toutes les 4h pendant 45 min.
* **Eldrin l'Archiviste :** *"Chaque coup d'épée et chaque récolte s'inscrit dans les annales d'Opal Heaven."* ➔ Menu complet des Quêtes et Déblocages de Titres.

---

## Chapitre 23 : Le Système de Quêtes & Contrats d'Ordre (`/quests`)

* **La Voie Initiatique (10 Chapitres Narrés) :** Du serment initial jusqu'à la conquête de la Warzone et l'achèvement d'un Donjon Instancié de Tier 1.
* **Contrats Quotidiens d'Ordre (Daily Bounties - 3 par jour) :**
  * *Sang :* 5 kills en Warzone, 5 parades parfaites, capture d'un KOTH.
  * *Abysses :* 128 blocs de minerais moddés, fusion de 16 lingots de titane, vente de 10k Or à l'HDV.
  * *Terre :* 300 cultures arrosées, 3 prélèvements d'ADN animal, récolte de 64 graines hybrides.
* **Sécurité Anti-Abus :** Les éliminations entre membres de la même faction ou même adresse IP sont automatiquement invalidées. Tout bloc marqué `PlayerPlaced` est ignoré par les quêtes de minage.

---

## Chapitre 24 : Le Protocole d'Échange Sécurisé (`/trade`)

Pour supprimer définitivement les arnaques au drop et les vols par perles d'ender :

```
                     CYCLE D'ÉCHANGE SÉCURISÉ JOUEUR-JOUEUR
     [ JOUEUR A ] ─── (/trade JoueurB) ───► [ NOTIFICATION / ACCEPTATION ]
          │                                             │
          ▼                                             ▼
     [ SÉLECTION 6 ITEMS MAX ]                     [ SÉLECTION 6 ITEMS MAX ]
     [ OFFRE EN PIÈCES D'OR  ]                     [ OFFRE EN PIÈCES D'OR  ]
          │                                             │
          ▼                                             ▼
     [ VALIDATION « PRÊT » ]                       [ VALIDATION « PRÊT » ]
          │                                             │
          └───────────────────────┬─────────────────────┘
                                  ▼
          [ BOUCLIER ANTI-SWITCH : 3 SECONDES DE VÉRIFICATION ]
          (Toute modification réinitialise le statut et impose 3s)
                                  │
                                  ▼
            [ SWAP ATOMIQUE SIMULTANÉ SQLITE & MÉMOIRE VIVE ]
```

* **Distance maximale :** 6 blocs, statut hors combat obligatoire.
* **Sécurité Anti-Switch :** Si l'un des joueurs modifie un item ou retire une pièce après avoir cliqué sur Prêt, **l'accord saute instantanément**, l'écran clignote en rouge et un **verrouillage obligatoire de 3 secondes** est imposé avant de pouvoir re-cliquer sur Prêt.
* **Transaction Atomique :** Swap d'inventaires simultané en base de données. Si une micro-déconnexion survient au tick exact de l'échange, **la transaction est annulée et chaque joueur conserve strictement ses items d'origine**. Zéro perte, zéro duplication.

---

## Chapitre 25 : Communication, Rangs Joueurs & Leaderboards

### A. Canaux de Communication Structurés
* `!` ou ordinaire : **Canal Global** (anti-spam 2s, préfixe `[Ordre][Faction]`).
* `$` : **Canal Local** (portée de 30 blocs pour commerce de proximité et RP discret).
* `@` : **Canal Faction** (visible exclusivement par les membres de guilde).
* `#` : **Canal Alliance** (visible par les factions alliées).

### B. Leaderboards Holographiques Flottants au Hub
Mis à jour automatiquement toutes les 60 secondes via requêtes SQLite :
1. *Top Factions :* Classement par points de Tech et KOTHs capturés.
2. *Top Gladiateurs :* Classement par ratio K/D et nombre de parades parfaites réussies.
3. *Top Titans Industriels :* Classement par quantité de minerais rares extraits.

### C. Hiérarchie des Rôles & Rangs (`RoleManager`)
* **Joueur :** 3 ventes max à l'HDV, accès aux 3 Ordres, 1 faction.
* **VIP Céleste :** 6 ventes max à l'HDV, kit cosmétique particule d'aura bleue, accès prioritaire en cas de serveur plein.
* **Héros Céleste :** 10 ventes max à l'HDV, accès à la commande `/workbench` mobile, particule stellaire dorée.
* **Modérateur / Administrateur :** Outils de freeze, vanish, mute, inspection d'inventaire et journal des logs SQLite.

---

# TOME VII : L'INGÉNIERIE BEDROCK, SÉCURITÉ & RÉPERTOIRE VISUEL

## Chapitre 26 : L'Architecture Hybride Go Proxy + PocketMine-MP

Pour accueillir simultanément plus de 300 joueurs sur Minecraft Bedrock sans écroulement des TPS (Ticks Per Second), Opal Heaven déploie une architecture réseau découplée de pointe.

```
                      ARCHITECTURE RÉSEAU HYBRIDE D'OPAL HEAVEN
    [ CLIENTS BEDROCK ] (300+ Joueurs : Mobiles, Consoles, PC Win10)
             │  Protocole UDP / RakNet natif (Port 19132)
             ▼
    ┌────────────────────────────────────────────────────────────────────────┐
    │                       PROXY GO ULTRA-PERFORMANT                        │
    │  • Multi-threading RakNet en Goroutines légères                        │
    │  • Filtrage pare-feu Anti-DDoS UDP instantané                          │
    │  • Compression dynamique des Chunks (Snappy / Zlib)                    │
    │  • Cache mémoire des Skins HD 128x128 (Évite la saturation réseau)     │
    └───────────────────────────────────┬────────────────────────────────────┘
                                        │ Liaison loopback ultra-rapide
                                        ▼
    ┌────────────────────────────────────────────────────────────────────────┐
    │                      SERVEUR POCKETMINE-MP 5.x                         │
    │  • Moteur logique de jeu, entités et calculs physiques                │
    │  • Gestion des 40 Biomes & Générateur procédural d'îles                │
    │  • Traitement du Combat de Posture & Parades (100% Main Thread)       │
    │  • Workers asynchrones dédiés (`AsyncTask`) pour SQLite & Bourse       │
    └────────────────────────────────────────────────────────────────────────┘
```

### Les Avantages Majeurs du Proxy Go :
1. **Délestage Réseau du Serveur Principal :** Le traitement des poignées de main (handshakes) RakNet, le chiffrement et la décompression des gros paquets de textures sont pris en charge par le proxy Go en multi-cœurs. Le thread principal de PocketMine-MP ne traite que la pure logique de jeu (20 TPS stables garantis).
2. **Gestionnaire de Cache des Skins HD :** Les skins personnalisés 128x128 des PNJ et des armures moddées sont mis en cache en mémoire vive par le proxy, réduisant de $70\%$ la bande passante descendante lors des rassemblements massifs en Warzone.
3. **Protection Anti-DDoS :** Toute tentative de saturation par paquets malveillants ou floods UDP est absorbée et neutralisée par le proxy Go avant même d'atteindre le moteur PHP.

---

## Chapitre 27 : Le Grand Registre des 18 Patchs Anti-Exploits

Le plugin `AntiExploitManager.php` neutralise proactivement les 18 failles historiques de Minecraft Bedrock et des modes faction :

```
                  LE GRAND REGISTRE DES 18 SOLUTIONS DE SÉCURITÉ
┌────┬─────────────────────────────┬────────────────────────────────────────────────────────────┐
│ N° │ Nom du Patch Technique      │ Mécanisme de Protection Implémenté                         │
├────┼─────────────────────────────┼────────────────────────────────────────────────────────────┤
│ 01 │ **Tag `PlayerPlaced`**      │ Tout bloc posé par un joueur est marqué en SQLite. Ne drop │
│    │                             │ aucun minerai rare, aucun or et ne donne aucun XP minage.  │
│ 02 │ **Anti-Kill Farm XUID/IP**  │ Cooldown de 30 min sur un même joueur/IP : 0 marque de     │
│    │                             │ sang et 0 XP en cas de kills répétés d'un complice.        │
│ 03 │ **Anti-Combat Logging**     │ Déconnexion sous Combat-Tag (15s) ➔ Spawn d'un NPC fantôme │
│    │                             │ vulnérable pendant 15s. S'il est tué, le joueur drop tout. │
│ 04 │ **Anti-Pearl Glitch**       │ Raycasting strict des perles : annulation immédiate de la  │
│    │                             │ téléportation si la trajectoire traverse un mur ou bloc 3D.│
│ 05 │ **Anti-Macro Pêche**        │ Variations aléatoires de l'ActionBar à chaque lancer et    │
│    │                             │ détection des clics à intervalle fixe artificiel (< 50ms). │
│ 06 │ **Transaction Atomique UI** │ Verrouillage mémoire des inventaires lors des formulaires  │
│    │                             │ FormUI : impossible de dupliquer un objet par déconnexion. │
│ 07 │ **Anti-Chest Stealing**     │ Contrôle des permissions de rôle au sein du claim : un     │
│    │                             │ membre Recruit ne peut ouvrir les coffres du Master.       │
│ 08 │ **Anti-Speedmine / Noclip** │ Calcul du temps de casse par bloc : rejet côté serveur de  │
│    │                             │ tout paquet de destruction de bloc reçu prématurément.     │
│ 09 │ **Anti-FastPlace**          │ Plafond strict de 8 blocs posés par seconde par joueur.    │
│ 10 │ **Anti-Fly / Glide**        │ Calcul de trajectoire balistique et de gravité : rollback  │
│    │                             │ immédiat si un joueur flotte sans effet de potion valide.  │
│ 11 │ **Anti-Killaura / Reach**   │ Portée maximale de frappe bridée à 3.2 blocs et angle FOV  │
│    │                             │ obligatoire orienté vers la hitbox de la cible.            │
│ 12 │ **Anti-Spam Chat & Command**│ Délai minimum de 2.0s entre les messages et filtre regex.  │
│ 13 │ **Anti-Ghost Block Sync**   │ Renvoi immédiat d'un `UpdateBlockPacket` forcé si le client│
│    │                             │ croit avoir brisé un bloc protégé ou hors de portée.       │
│ 14 │ **Anti-Inventory Desync**   │ Contrôle de somme de contrôle (Checksum) des slots d'items │
│    │                             │ avant chaque achat, vente ou échange `/trade`.             │
│ 15 │ **Anti-Border Bypass**      │ Barrière infranchissable aux coordonnées limites du monde   │
│    │                             │ avec refoulement cinétique vers le centre de la carte.     │
│ 16 │ **Anti-XRay Procédural**    │ Masquage sélectif des minerais souterrains non exposés à   │
│    │                             │ l'air libre dans les chunks réseau envoyés au client.      │
│ 17 │ **Anti-Dupe Shulker/Sacs**  │ Interdiction absolue d'imbriquer un conteneur portable     │
│    │                             │ dans un autre conteneur portable.                          │
│ 18 │ **Anti-Macro de Posture**   │ Chronométrage des transitions de Sneak : un sneak maintenu │
│    │                             │ en continu annule la parade parfaite au profit de la garde.│
└────┴─────────────────────────────┴────────────────────────────────────────────────────────────┘
```

---

## Chapitre 28 : Répertoire Visuel des 35 Blocs Moddés

Chaque bloc dispose de textures personnalisées et d'effets visuels intégrés :

### A. Minerais & Cristaux (9 Blocs) :
1. `opal:cobalt_ore` : Roche ardoisée anthracite veinée de cristaux bleu électrique (`#1D4ED8`).
2. `opal:titanium_ore` : Basalte noir profond incrusté de plaques plates de titane gris acier (`#475569`).
3. `opal:aetherite_ore` : Pierre d'Aether crème marbrée de cristaux cyan et améthyste luminescents (lueur 4).
4. `opal:celestial_opal_ore` : Granit blanc nacré constellé d'étoiles avec gemmes irisées prismatiques animées (lueur 7).
5. `opal:stellar_amethyst` : Prismes de cristal violet royal 3D polygonaux en saillie hors de la roche (lueur 5).
6. `opal:block_cobalt` : Bloc massif de cobalt brossé à joints métalliques étanches.
7. `opal:block_titanium` : Bloc de blindage lourd riveté en titane mat.
8. `opal:block_aetherite` : Bloc résonnant cyan réduisant de $25\%$ le recul des explosions alentour.
9. `opal:block_pure_opal` : Bloc de prestige ultime rayonnant d'une aura féerique continue (lueur 12).

### B. Machinerie & Blocs Utilitaires (8 Blocs) :
10. `opal:acceleration_composter` : Trémie de laiton conique, engrenages apparents en rotation 3D et vapeur verte.
11. `opal:celestial_foundry` : Fourneau octogonal en basalte avec foyer de lave ardente et dôme de condensation en améthyste.
12. `opal:thorgar_anvil` : Enclume d'acier céleste incassable gravée de runes de protection.
13. `opal:faction_heart` : Dodécaèdre d'opale flottant au-dessus d'un piédestal d'obsidienne, pulsant en bleu (paix) ou rouge (siège).
14. `opal:dna_extractor_station` : Table alchimique avec fioles d'incubation sous verre et centrifugeuse à manivelle.
15. `opal:safe_deposit_box` : Coffre personnel lié au XUID, cerclé d'acier trempé inviolable.
16. `opal:arcanum_table` : Table de désenchantement arcanique pour recycler les armes en orbes d'essence.
17. `opal:ender_disruptor` : Monolithe d'ardoise surmonté d'un œil d'Ender aux anneaux magiques pourpres orbitaux.

### C. Fortifications & Blocs Défensifs (6 Blocs) :
18. `opal:fortified_stone` : Pierre taillée rivetée d'acier, résistant à 3 explosions directes de TNT.
19. `opal:dense_void_obsidian` : Obsidienne dense comprimée au vide céleste, résistant à 5 détonations de TNT.
20. `opal:aether_armored_glass` : Verre feuilleté transparent résistant aux blasts et insensible aux flèches.
21. `opal:ground_spikes` : Plaque de 9 pointes d'acier triangulaires acérées infligeant 6 HP de dégâts directs et Saignement.
22. `opal:shock_plate` : Dalle piégée quadrillée de bobines Tesla infligeant $+40\%$ de posture et Lenteur II.
23. `opal:reinforced_door` : Porte blindée en titane de 100 HP, forçable uniquement au bélier ou TNT de brèche.

### D. Végétaux & Agriculture Moddée (7 Blocs) :
24. `opal:fertile_celestial_soil` : Terre labourée luminescente auto-hydratée, incassable au saut, vitesse de pousse $+100\%$.
25. `opal:iron_crop` : Tige métallique argentée produisant des pépites de fer en 4 stades.
26. `opal:gold_crop` : Panicules dorées scintillantes produisant des pépites d'or en 4 stades.
27. `opal:diamond_crop` : Bourgeons translucides cyan produisant des éclats de diamant en 5 stades.
28. `opal:celestial_opal_crop` : Plante de prestige à 7 stades nécessitant un bloc d'Améthyste Stellaire sous la terre.
29. `opal:primordial_log` & `opal:primordial_leaves` : Tronc géant et feuillage ancestral résistant aux flammes.
30. `opal:venomous_brambles` : Ronces vénéneuses ralentissant de $60\%$ et infligeant Poison II aux ennemis.

### E. Blocs Événementiels & Warzone (5 Blocs) :
31. `opal:koth_beacon_point` : Balise centrale de capture KOTH à faisceau lumineux vertical bicolore.
32. `opal:faction_totem_core` : Pilier totémique à 3 couches de blocs à extraire en Warzone.
33. `opal:celestial_airdrop_chest` : Coffre parachuté peint en bandes jaunes et noires avec gyrophare stroboscopique rouge.
34. `opal:stargate_portal_frame` : Cadre d'obsidienne stellaire inaltérable formant les anneaux inter-mondes.
35. `opal:stargate_vortex` : Miroir d'énergie translucide liquide téléportant le joueur à travers le réseau des dimensions.

---

## Chapitre 29 : Répertoire Visuel des 24 Entités Moddées

Toutes les entités exploitent des skins HD 128x128 et des géométries Blockbench en couches superposées 3D :

### A. PNJ Vivants du Hub (7 Entités) :
1. `opal:npc_sire_gabriel` : Paladin en armure d'or blanc et opale, cape brodée flottante et regard azur perçant.
2. `opal:npc_thorgar` : Forgeron colosse à la peau cendrée, tablier de cuir tanné, barbe tressée et marteau de cobalt.
3. `opal:npc_dame_sylvana` : Druidesse en robe d'écorce souple, couronne de brindilles luminescentes et nuage de spores dorés.
4. `opal:npc_cassandra` : Aristocrate en robe de velours violet nuit, monocle arcanique d'améthyste et balance dorée oscillante.
5. `opal:npc_zephyr` : Explorateur céleste au chapeau à large bord, sac à dos 3D débordant de parchemins et fioles.
6. `opal:npc_eldrin` : Érudit en toge d'indigo avec grimoire relié d'or en lévitation autonome devant son torse.
7. `opal:npc_celestial_guard` : Sentinelle en armure de plates d'Aetherite assurant la solennité des sanctuaires.

### B. Entités Techniques & Entraînement (4 Entités) :
8. `opal:combat_dummy` : Mannequin de chêne et d'acier avec cible peinte, bras articulé et signal visuel de parade (icône orange ➔ bleue).
9. `opal:floating_leaderboard` : Hologramme 3D invisible avec texte multiligne persistant actualisé toutes les 60 secondes.
10. `opal:claim_beacon_hologram` : Marqueur d'en-tête de chunk affichant le nom de la guilde et la santé du Cœur.
11. `opal:loot_beacon_vfx` : Colonne de lumière colorée verticale guidant les joueurs vers les butins de haute rareté au sol.

### C. Bêtes de Guerre & Faune Génétique (4 Entités) :
12. `opal:war_wolf` : Loup caparaçonné de titane aux yeux rouges braisés, attaquant la posture ennemie.
13. `opal:tectonic_boar` : Sanglier massif à défenses de roche volcanique capable de charger et d'enfoncer les herses.
14. `opal:aether_falcon` : Rapace céleste volant en cercle au-dessus de son maître et révélant les ennemis cachés à 24 blocs.
15. `opal:celestial_steed` : Destrier ailé en armure d'opale, vitesse $0.45$, saut de 4.5 blocs, immunité aux chutes.

### D. Boss de Donjons & Monstres d'Élite (5 Entités) :
16. `opal:boss_vampire_lord` : Humanoïde émacié de $2.4\text{m}$ en armure pourpre brandissant une grande faux en cristal de sang (500 HP).
17. `opal:boss_basalt_golem` : Mastodonte de $3.8\text{m}$ fait de blocs de basalte zébrés de failles de lave incandescente (800 HP).
18. `opal:boss_bramble_queen` : Entité sylvestre éthérée entourée de sarments vivants invoquant des prisons d'épines (650 HP).
19. `opal:abyssal_leviathan` : Serpent de mer géant de 15 blocs cuirassé d'écailles de cobalt bleu nuit (350 HP).
20. `opal:elite_corrupted_knight` : Garde d'élite avec pavois d'acier nécessitant une rupture de posture de 100 points.

### E. Projectiles & Entités Dynamiques (4 Entités) :
21. `opal:breach_tnt_primed` : TNT amorcée clignotante à mèche de 3.0s infligeant des dégâts quadruplés aux fortifications.
22. `opal:tactical_ice_shard` : Projectile de givre créant une nappe de glace $3 \times 3$ au sol et appliquant Lenteur III.
23. `opal:seismic_shockwave` : Onde de sol avançant sur 10 blocs en soulevant des poussières et brisant la posture.
24. `opal:airdrop_parachute` : Toile de parachute en soie nacrée descendant à vitesse régulée de $0.25\text{ m/s}$.

---

## Chapitre 30 : Répertoire des 18 Animations Bedrock & VFX

### A. Animations Joueur (Player Model - 6 Animations) :
1. `animation.player.parry_stance` : L'épée se relève horizontalement devant le sternum à deux mains en garde défensive au Sneak.
2. `animation.player.stagger` : Secousse caméra (`CameraShakePacket`), recul de $20^\circ$, tête dodelinante, bras ballants et couronne d'étoiles dorées orbitale (40 ticks).
3. `animation.player.finisher_strike` : Rotation du buste à $60^\circ$ et abaissement foudroyant de la lame avec traînée de lumière rouge rubis.
4. `animation.player.staff_channeling` : Bras levés à la verticale tenant le bâton vers le zénith avec halo de particules spiralées ascendante.
5. `animation.player.heavy_reeling` : Buste tiré en arrière par saccades simulant la tension extrême de la ligne de pêche.
6. `animation.player.celestial_ascension` : Redressement vertical à la mort, silhouette lumineuse blanche pure qui lévite de 2 blocs avant de s'évanouir en étincelles étoilées (remplace la bascule rouge vanilla).

### B. Animations PNJ Vivants (4 Animations) :
7. `animation.npc.idle_breathing` : Élévation de 2 cm de la cage thoracique (boucle 3.5s) et clignement périodique des yeux.
8. `animation.npc.track_player_head` : Pivotement fluide et amorti de la tête et du cou vers le joueur le plus proche ($< 7\text{m}$).
9. `animation.npc.thorgar_hammering` : Frappe cadencée du lourd marteau sur l'enclume d'acier avec étincelles et sons synchronisés toutes les 1.8s.
10. `animation.npc.eldrin_book_levitation` : Flottement sinusoïdal du grimoire dans les airs avec rotation des pages toutes les 8s.

### C. Animations Boss & Créatures (4 Animations) :
11. `animation.vampire.shadow_dash` : Dématérialisation en brume noire, translation de 8 blocs en 4 ticks et réapparition dans le dos de la victime.
12. `animation.golem.ground_slam` : Télégraphie visuelle de 1.5s les deux bras levés (failles de lave brillantes), puis impact violent déclenchant l'onde sismique.
13. `animation.bramble.root_sprout` : Projection des mains vers le sol, 4 tentacules épineux jaillissent en vrille pour emprisonner l'escouade.
14. `animation.leviathan.water_leap` : Jaillissement vertical de 6 blocs hors de l'océan, cambrure des écailles et replongeon dans une immense gerbe d'écume.

### D. Animations de Blocs & Objets Dynamiques (4 Animations) :
15. `animation.portal.stargate_rotation` : L'anneau extérieur tourne à $20^\circ/\text{s}$ dans le sens horaire, l'anneau intérieur à $35^\circ/\text{s}$ en sens antihoraire.
16. `animation.faction_heart.heartbeat` : Battement apaisé à 60 bpm en bleu ciel en temps normal, accélérant à 140 bpm en rouge vermillon lors des sièges.
17. `animation.airdrop.parachute_sway` : Oscillation pendulaire réaliste de $12^\circ$ simulant la prise au vent céleste lors de la descente.
18. `animation.composter.churn_gears` : Rotation continue des engrenages à $180^\circ/\text{s}$ et mouvement alternatif des lames lors de l'insertion de végétaux.

---

# 🏁 ÉPILOGUE & CONCLUSION DE PRODUCTION

Ce **Master Codex** constitue la spécification intégrale, définitive et inviolable d'Opal Heaven. Chaque chiffre, chaque formule mathématique, chaque taux de drop, chaque identifiant technique et chaque règle d'anti-exploit est désormais formellement gravé pour assurer un déploiement parfait, un équilibre durable sur 6 mois et une expérience MMORPG sans équivalent sur Minecraft Bedrock.
