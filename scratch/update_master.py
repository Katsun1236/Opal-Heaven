import os

path = r"c:\Users\basti\OneDrive\Bureau\LIVRE_BLANC_OPAL_HEAVEN.md"
with open(path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Update Chapitre 9
old_ch9_start = "## Chapitre 9 : L'Architecture Multi-Mondes Dédiée"
new_ch9 = """## Chapitre 9 : L'Architecture des 4 Mondes Dédiés (Concentration 100% Warzone)

Pour maximiser l'intensité du PvP, éliminer la dispersion des joueurs dans des instances privées ("safe-farming") et garantir un débit parfait de 20 TPS avec plus de 300 joueurs connectés, Opal Heaven centralise **100% de ses affrontements majeurs en monde ouvert au sein d'une Warzone souveraine**.

```
                   CARTE DU RÉSEAU DES 4 MONDES D'OPAL HEAVEN
                                 [ HUB CÉLESTE ]
                            (Sanctuaire Paisible 0 PvP)
                                       │
            ┌──────────────────────────┼──────────────────────────┐
            ▼                          ▼                          ▼
  [ OVERWORLD FACTION ]      [ WARZONE SOUVERAINE ]       [ MONDE MINAGE ]
  10 000 x 10 000 blocs      2 000 x 2 000 blocs          5 000 x 5 000 blocs
  40 Biomes Moddés           PvP / PvPvE Non-Stop         Reset Mensuel Net
  Bases, Claims & Guerres    KOTH, Totems, Airdrops       Gisements Bruts Neufs
                             & 3 Boss Mondiaux Contestés  (Zéro dégradation Overworld)
```

### Spécifications & Rôles des 4 Mondes :
1. **Le Hub Céleste (`hub`) :** Sanctuaire flottant dans le vide astral. Dégâts désactivés, faim gelée, inventaires protégés. Regroupe l'ensemble des PNJ vivants, la Banque de Cassandra, la Grande Bourse, les mannequins d'entraînement de parade et les Portails Stellaires.
2. **L'Overworld de Faction (`opal_main`) :** Carte pérenne de $10\\,000 \\times 10\\,000$ blocs générée avec les 40 biomes moddés exclusifs. C'est le monde sacré des forteresses, des claims, des fermes industrielles et de la guerre de territoire. Aucun reset de terrain n'y intervient pendant toute la saison de 6 mois.
3. **La Warzone Souveraine (`warzone`) :** L'épicentre absolu du serveur ($2\\,000 \\times 2\\,000$ blocs). Aucun claim possible, perte intégrale du stuff à la mort. **C'est ici et nulle part ailleurs que se déroulent tous les combats majeurs :** KOTHs quotidiens, Totems de guerre, Airdrops toutes les 2h, et **les 3 Sanctuaires de Boss Mondiaux (Seigneur Sanglant, Golem de Basalte, Reine des Ronces)** qui s'affrontent en plein champ de bataille contesté par toutes les factions en PvPvE ouvert.
4. **Le Monde Minage & Ressources (`mining_world`) :** Dimension de $5\\,000 \\times 5\\,000$ blocs dédiée à l'extraction brute de masse. **Ce monde est intégralement régénéré le 1er de chaque mois à 04h00 du matin.** Cela garantit des gisements de minerais neufs permanents et évite que l'Overworld de faction ne ressemble à un champ de cratères gruyère."""

idx9 = content.find(old_ch9_start)
idx10 = content.find("## Chapitre 10 :")
if idx9 != -1 and idx10 != -1:
    content = content[:idx9] + new_ch9 + "\n\n---\n\n" + content[idx10:]
    print("Chapitre 9 updated successfully!")
else:
    print("Failed to find Chapitre 9 boundary:", idx9, idx10)

# 2. Update Chapitre 11
old_ch11_start = "## Chapitre 11 : Les Donjons Instanciés PvE & Les 3 Grands Boss"
new_ch11 = """## Chapitre 11 : Les Boss Mondiaux de la Warzone (PvPvE Ouvert & Sanctuaires Contestés)

En cohérence avec le choix fondamental de concentrer **100% de la rivalité en Warzone**, Opal Heaven bannit les donjons instanciés en circuit fermé. Les 3 Grands Boss du jeu ne sont pas enfermés dans des donjons sécurisés : **ils règnent au grand jour dans des sanctuaires ouverts de la Warzone, créant des batailles PvPvE épiques où les factions s'affrontent tout en combattant le boss.**

```
                 DYNAMIQUE D'UN COMBAT DE BOSS EN WARZONE (PvPvE)
   ┌────────────────────────────────────────────────────────────────────────┐
   │                           LE BOSS MONDIAL                              │
   │           Attaques de zone, patterns martiaux & jauge de posture       │
   └───────────────────▲────────────────────────────────▲───────────────────┘
                       │                                │
            Frappe le boss                      Attaque en traître
                       │                                │
          ┌────────────┴────────────┐      ┌────────────┴────────────┐
          │  FACTION ASSAILLANTE A  │ ◄──► │   FACTION RIVALE B      │
          │  (Tente d'abattre le    │ PvP  │   (Tente d'éliminer la  │
          │   boss & sécuriser drop)│ Total│    faction A & voler)   │
          └─────────────────────────┘      └─────────────────────────┘
```

### A. Les 3 Sanctuaires Ouverts de Boss en Warzone :

#### 1. La Fosse Sanglante du Seigneur Vampire (Sud de la Warzone)
* **Boss :** `opal:boss_vampire_lord` (**1 200 HP** - barre de boss globale visible par toute la Warzone).
* **Sanctuaire :** Amphithéâtre d'obsidienne ouvert à ciel ouvert, cerné de piliers de sang. Aucun claim, aucune protection.
* **Patterns de Combat :**
  * *Balayage Mortel ($360^\\circ$) :* Toutes les 6 secondes, le boss arme sa faux pendant 15 ticks. **Doit être paré au sneak.** En cas d'échec, le boss vole 8 cœurs à tous les joueurs touchés et régénère $10\\%$ de sa vie.
  * *Dash d'Ombre :* Se téléporte instantanément derrière le joueur ayant infligé le plus de dégâts récents.
* **Intérêt PvP :** Si une faction adverse surgit pendant le combat, le Seigneur Sanglant frappe indifféremment tous les joueurs présents : le champ de bataille devient une mêlée chaotique à trois fronts !

#### 2. La Fonderie Magmatique du Golem de Basalte (Nord de la Warzone)
* **Boss :** `opal:boss_basalt_golem` (**2 000 HP**).
* **Sanctuaire :** Cratère de roche en fusion parsemé de ponts de basalte étroits au-dessus de lacs de lave.
* **Patterns de Combat :**
  * *Écrasement Tectonique (Ground Slam) :* Lève ses deux bras au-dessus de sa tête pendant 1.5s (télégraphie visuelle de lave incandescente), puis percute le sol : onde sismique brisant la posture de quiconque ne saute pas au moment précis de l'impact et projetant les joueurs dans la lave.
  * *Éruption Magmatique :* Fait jaillir des geysers de flammes sous les joueurs immobiles depuis plus de 2 secondes.

#### 3. Le Bosquet Vénéneux de la Reine des Ronces (Ouest de la Warzone)
* **Boss :** `opal:boss_bramble_queen` (**1 500 HP**).
* **Sanctuaire :** Jungle de racines primordiales géantes et ronces toxiques ralentissant les mouvements.
* **Patterns de Combat :**
  * *Prisons de Ronces :* Enracine plusieurs joueurs sur place dans des sarments épineux. Les coéquipiers doivent briser les ronces à la hache en moins de 4 secondes avant qu'elles n'explosent.
  * *Salve d'Épines Téléguidées :* Projette 24 épines perforantes dans toutes les directions.

### B. Mécaniques d'Apparition & Le Coffre Céleste Contesté :
* **Déclenchement des Boss :**
  1. *Spawns Mondiaux Programmés :* Mercredi à 21h00, Vendredi à 21h30 et Dimanche à 16h00 (annoncés par un broadcast sonore d'alerte dans tout le serveur 15 minutes à l'avance).
  2. *Invocation Manuelle par Autel :* Une faction peut invoquer un boss à tout moment en déposant un **Orbe d'Invocation Céleste** sur l'Autel du Sanctuaire. L'activation déclenche un faisceau lumineux géant vers le ciel et alerte immédiatement toutes les factions ennemies !
* **Le Coffre Céleste Contesté (Anti-Ninja Loot) :**
  * À la mort d'un Boss de Warzone, **aucun objet n'est directement déposé dans l'inventaire**.
  * Un **Coffre Céleste Scellé** apparaît au centre de l'arène avec un **compte à rebours de déverrouillage de 30 secondes**.
  * Les factions doivent sécuriser la zone et s'entretuer pour être la seule faction debout à l'ouverture du coffre et s'emparer des plans d'armure T5, gemmes d'Opale et orbes de prestige."""

idx11 = content.find(old_ch11_start)
idx12 = content.find("# TOME IV :")
if idx11 != -1 and idx12 != -1:
    content = content[:idx11] + new_ch11 + "\n\n---\n\n" + content[idx12:]
    print("Chapitre 11 updated successfully!")
else:
    print("Failed to find Chapitre 11 boundary:", idx11, idx12)

# 3. Minor occurrences
content = content.replace("4 cœurs de feu de donjon", "4 Cœurs Magmatiques du Golem de Warzone")
content = content.replace("1 Clé de Donjon Primordiale", "1 Orbe d'Invocation du Seigneur Sanglant")
content = content.replace("l'achèvement d'un Donjon Instancié de Tier 1", "la participation à la chute d'un Boss Mondial de Warzone")
content = content.replace("### D. Boss de Donjons & Monstres d'Élite", "### D. Boss Mondiaux de Warzone & Monstres d'Élite")

with open(path, "w", encoding="utf-8") as f:
    f.write(content)

# Copy to mirror locations
mirror1 = r"c:\Users\basti\OneDrive\Bureau\Opal Heaven\LIVRE_BLANC_OPAL_HEAVEN.md"
mirror2 = r"C:\Users\basti\.gemini\antigravity\brain\a84cb543-866c-4f9a-9e8e-e051865b6fae\OPAL_HEAVEN_LIVRE_BLANC_GAME_DESIGN_MASTER.md"

with open(mirror1, "w", encoding="utf-8") as f:
    f.write(content)
with open(mirror2, "w", encoding="utf-8") as f:
    f.write(content)

print(f"Master document updated across all mirrors! Total size: {len(content)} characters.")
