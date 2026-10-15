<?php

declare(strict_types=1);

namespace opalheaven\core\orders;

enum OrderType : string {
    case SANG = "SANG";         // La Voie Martiale (PvP, Arènes, Primes, Sièges)
    case ABYSSES = "ABYSSES";   // La Voie Géologique (Minage tellurique, Résonance, Failles)
    case TERRE = "TERRE";       // La Voie du Vivant (Botanique mystique, Élevage sélectif, Gastronomie)

    public function getDisplayName() : string {
        return match($this) {
            self::SANG => "§cOrdre du Sang",
            self::ABYSSES => "§9Ordre des Abysses",
            self::TERRE => "§aOrdre de la Terre"
        };
    }

    public function getDescription() : string {
        return match($this) {
            self::SANG => "Domptez l'art du duel, la parade chirurgicale, la rupture de posture et le pillage de bastions.",
            self::ABYSSES => "Fondez la roche, détectez les gemmes primordiales d'opale et provoquez des fissures sismiques.",
            self::TERRE => "Développez la génétique végétale (lianes de fer), élevez des bêtes de guerre et concoctez des festins de siège."
        };
    }

    public function getColorCode() : string {
        return match($this) {
            self::SANG => "§c",
            self::ABYSSES => "§9",
            self::TERRE => "§a"
        };
    }

    /**
     * @return SpecializationType[]
     */
    public function getSpecializations() : array {
        return match($this) {
            self::SANG => [
                SpecializationType::BRETTEUR,
                SpecializationType::BERSERKER,
                SpecializationType::TRAQUEUR
            ],
            self::ABYSSES => [
                SpecializationType::SISMOLOGUE,
                SpecializationType::PROSPECTEUR,
                SpecializationType::TERRASCULPTEUR
            ],
            self::TERRE => [
                SpecializationType::BOTANISTE,
                SpecializationType::MAITRE_DES_BETES,
                SpecializationType::APOTHICAIRE
            ]
        };
    }
}
