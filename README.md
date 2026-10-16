# 🌌 Opal Heaven — Serveurs Minecraft Bedrock Edition

Bienvenue sur le projet **Opal Heaven**, serveur PvP-Faction & RPG nouvelle génération pour Minecraft Bedrock (Axolotl-PM 5.49.3+dev / PocketMine-MP 5 sur protocole Bedrock v26.50+).

---

## 🏗️ Architecture du Projet

```
Opal Heaven/
├── start_all.cmd           # Lance en 1 clic le Hub et le Faction simultanément
├── start_hub.cmd           # Lance uniquement le Hub Sanctuaire (Port 19135)
├── start_faction.cmd       # Lance uniquement le Faction Overworld (Port 19132)
│
├── server_hub/             # SERVEUR HUB SANCTUAIRE (Port 19135)
│   ├── worlds/hub/         # Monde du Hub (converti depuis eckigerhub.zip en Bedrock LevelDB)
│   ├── schematics/         # Dossier des schématiques (.schem, .mcstructure)
│   ├── plugins/OpalCore/   # Plugin central (rôle: hub, 0 PvP, PNJ, mannequins)
│   └── start.cmd           # Script de lancement du Hub
│
├── server/                 # SERVEUR FACTION OVERWORLD (Port 19132)
│   ├── worlds/world/       # Monde Overworld 10 000 x 10 000 (Générateur opal 40 biomes)
│   ├── plugins/OpalCore/   # Plugin central (rôle: faction, claims, posture/parade, HDV, quêtes)
│   └── start.cmd           # Script de lancement du Faction
│
├── database/               # BASE DE DONNÉES PARTAGÉE & SYNCHRONISÉE
│   ├── opal_shared.db      # SQLite WAL partagé (profils, rôles, soldes, factions, quêtes)
│   └── schema.sql          # Schéma SQL de référence
│
├── model/                  # MODÈLES BLOCKBENCH
│   ├── Block/              # Blocs moddés 3D
│   └── Entité/             # 20 monstres moddés et Boss de Donjons
│
├── network/                # COMPOSANTS RÉSEAU & PROXY
│   └── proxy_gopher/       # Anciens composants et passerelles proxy Go
│
└── scripts/                # OUTILS & SCRIPTS DE MAINTENANCE
    └── archives/           # Archives de dépendances et sauvegardes
```

---

## ⚡ Fonctionnalités Clés & Commandes

### 1. Administration & Permissions
* **Rôle ADMIN avec Permission Étoile (`*`) :** Attribué par défaut à `Katsun1246`. Accès à 100% des commandes.
* `/setrole <joueur> <ADMIN|JOUEUR>` : Définir le rôle d'un joueur.
* `/role [joueur]` : Consulter le rôle et le préfixe.
* `/pastehub <fichier>` : Importer/coller une structure ou un schéma au Hub.

### 2. Réseau & Téléportations Inter-Serveurs
* `/hub` : Téléportation immédiate vers le Hub Sanctuaire (`127.0.0.1:19135`).
* `/faction` : Téléportation immédiate vers le serveur Faction (`127.0.0.1:19132`).
* `/spawn` : Retour au point d'apparition avec canalisation (3s).

### 3. Gameplay & Systèmes du Livre Blanc
* **Combat & Posture :** Parade parfaite au Sneak (fenêtre de 9 ticks, son d'enclume céleste, recul et étincelles), Stagger 40 ticks avec CameraShake, et exécution critique.
* **Mannequin de Parade :** Entraînement interactif via `/dummy` ou `/parry`.
* **Les 4 Ordres :** `/order` (Sang, Terre, Abysses, Forge) avec niveaux et passifs.
* **Factions & Claims :** `/f [create|claim|unclaim|map|bank|sethome|home|info]`.
* **Économie & HDV :** `/money`, `/pay`, et `/ah` (Hôtel des Ventes par catégories et ordres boursiers).
* **Troc Sécurisé :** `/trade <joueur>` et `/trade accept` (délai anti-switch 3s et swap atomique).
* **Quêtes Scénarisées :** `/quests` (Voie Initiatique en 10 chapitres).
* **Primes :** `/bounty <joueur> <montant>`.
