<?php

declare(strict_types=1);

namespace opalheaven\core\orders;

enum SpecializationType : string {
    // Ordre du Sang
    case BRETTEUR = "BRETTEUR";
    case BERSERKER = "BERSERKER";
    case TRAQUEUR = "TRAQUEUR";

    // Ordre des Abysses
    case SISMOLOGUE = "SISMOLOGUE";
    case PROSPECTEUR = "PROSPECTEUR";
    case TERRASCULPTEUR = "TERRASCULPTEUR";

    // Ordre de la Terre
    case BOTANISTE = "BOTANISTE";
    case MAITRE_DES_BETES = "MAITRE_DES_BETES";
    case APOTHICAIRE = "APOTHICAIRE";

    public function getOrder() : OrderType {
        return match($this) {
            self::BRETTEUR, self::BERSERKER, self::TRAQUEUR => OrderType::SANG,
            self::SISMOLOGUE, self::PROSPECTEUR, self::TERRASCULPTEUR => OrderType::ABYSSES,
            self::BOTANISTE, self::MAITRE_DES_BETES, self::APOTHICAIRE => OrderType::TERRE
        };
    }

    public function getDisplayName() : string {
        return match($this) {
            self::BRETTEUR => "§cLe Bretteur",
            self::BERSERKER => "§4Le Berserker",
            self::TRAQUEUR => "§8Le Traqueur",
            self::SISMOLOGUE => "§9Le Sismologue",
            self::PROSPECTEUR => "§bLe Prospecteur",
            self::TERRASCULPTEUR => "§6Le Terrasculpteur",
            self::BOTANISTE => "§aLe Botaniste Mystique",
            self::MAITRE_DES_BETES => "§2Le Maître des Bêtes",
            self::APOTHICAIRE => "§dL'Apothicaire"
        };
    }

    public function getSubtitle() : string {
        return match($this) {
            self::BRETTEUR => "Duelliste & Réflecteur de Choc",
            self::BERSERKER => "Fureur de Première Ligne & Lifesteal",
            self::TRAQUEUR => "Infiltrateur des Ombres & Chasseur de Primes",
            self::SISMOLOGUE => "Destructeur de Roche & Ondes de Choc",
            self::PROSPECTEUR => "Chasseur de Gemmes Rares d'Opale & Précision",
            self::TERRASCULPTEUR => "Ingénieur Forage Industriel & Auto-Smelt",
            self::BOTANISTE => "Génétique Végétale & Lianes Métallifères",
            self::MAITRE_DES_BETES => "Génétique Animale & Montures de Guerre",
            self::APOTHICAIRE => "Gastronomie de Combat & Alchimie Triple"
        };
    }

    public function getPassiveName() : string {
        return match($this) {
            self::BRETTEUR => "Réflecteur de Choc",
            self::BERSERKER => "Soif de Bataille",
            self::TRAQUEUR => "Lame Furtive",
            self::SISMOLOGUE => "Fracture en Chaîne",
            self::PROSPECTEUR => "Œil Tellurique",
            self::TERRASCULPTEUR => "Fusion Immédiate",
            self::BOTANISTE => "Pousse Symbiotique",
            self::MAITRE_DES_BETES => "Vigueur de Meute",
            self::APOTHICAIRE => "Concentration Pure"
        };
    }

    public function getPassiveDescription() : string {
        return match($this) {
            self::BRETTEUR => "Renvoie +40% de posture supplémentaire sur parade (39.2% total) et restaure 10% de faim.",
            self::BERSERKER => "+1.0% dégâts et +0.5% vol de vie par 5% PV manquants (jusqu'à +16% dégâts / +8% lifesteal).",
            self::TRAQUEUR => "+35% de dégâts bruts sur les coups portés dans le cône arrière de 120° de la cible.",
            self::SISMOLOGUE => "À 100% de charge séismique, pulvérise instantanément 27 blocs (3x3x3) avec drops garantis.",
            self::PROSPECTEUR => "+25% drop gemmes primordiales. Diamant/opale minés avec blocs adjacents intacts = Qualité Impeccable (+35% prix).",
            self::TERRASCULPTEUR => "Cuit instantanément fer et cuivre bruts en lingots et compacte par multiple de 9 en blocs.",
            self::BOTANISTE => "30% de chance d'auto-replantation instantanée. Possibilité d'hybrider des Lianes de Fer métallifères.",
            self::MAITRE_DES_BETES => "Bêtes nées d'élevage bénéficient de +50% PV, vitesse x1.8 et saut à 3.5 blocs.",
            self::APOTHICAIRE => "Durée des consommables cuisinés doublée (+100%) et confère +2 Cœurs d'Absorption (10 min)."
        };
    }

    public function getActiveName() : string {
        return match($this) {
            self::BRETTEUR => "Rupture Ciblée",
            self::BERSERKER => "Cri de Guerre",
            self::TRAQUEUR => "Pas de l'Ombre",
            self::SISMOLOGUE => "Fissure Sismique",
            self::PROSPECTEUR => "Résonance Spectrale",
            self::TERRASCULPTEUR => "Frénésie Tellurique",
            self::BOTANISTE => "Aura de Chlorophylle",
            self::MAITRE_DES_BETES => "Sifflet de Rappel",
            self::APOTHICAIRE => "Nuage Vivifiant"
        };
    }

    public function getActiveCooldown() : int {
        return match($this) {
            self::BRETTEUR => 22,
            self::BERSERKER => 30,
            self::TRAQUEUR => 25,
            self::SISMOLOGUE => 35,
            self::PROSPECTEUR => 45,
            self::TERRASCULPTEUR => 60,
            self::BOTANISTE => 40,
            self::MAITRE_DES_BETES => 50,
            self::APOTHICAIRE => 45
        };
    }

    public function getActiveDescription() : string {
        return match($this) {
            self::BRETTEUR => "La prochaine attaque dans les 3s ignore 50% d'armure et applique +35% de posture directe (CD: 22s).",
            self::BERSERKER => "Onde de choc 5 blocs repoussant les ennemis de 3.5 blocs + Speed II & Resistance I pendant 8s (CD: 30s).",
            self::TRAQUEUR => "Blink de 5 blocs dans le dos de la cible avec 1s d'invisibilité (CD: 25s).",
            self::SISMOLOGUE => "Fissure tellurique sur 10 blocs étourdissant les monstres et appliquant Slowness III 4s aux ennemis (CD: 35s).",
            self::PROSPECTEUR => "Pendant 10s, met en surbrillance avec des particules les diamants/débris/opales à 12 blocs (CD: 45s).",
            self::TERRASCULPTEUR => "Octroie Haste III & Speed II pendant 20s et rend la pioche incassable durant le buff (CD: 60s).",
            self::BOTANISTE => "Plante un totem de sève multipliant la pousse par 4.0 dans un rayon de 15x15 pendant 30s (CD: 40s).",
            self::MAITRE_DES_BETES => "Invoque un Loup Céleste spectral interceptant 30% des dégâts et appliquant Slowness II (CD: 50s).",
            self::APOTHICAIRE => "Nuage de brume verte de 6 blocs régénérant 2 cœurs par seconde (4 HP/s) pendant 8s (CD: 45s)."
        };
    }
}
