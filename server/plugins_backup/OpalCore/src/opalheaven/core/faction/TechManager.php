<?php

declare(strict_types=1);

namespace opalheaven\core\faction;

class TechManager {

    public static function getTechTiers() : array {
        return [
            1  => ["name" => "Fondation Sacrée", "xp" => 10000, "desc" => "Création du Cœur, 10 membres max."],
            2  => ["name" => "Coffre Commun I", "xp" => 25000, "desc" => "Coffre partagé de guilde."],
            3  => ["name" => "Volonté d'Acier", "xp" => 45000, "desc" => "Perte de Power réduite à -2.5 par mort."],
            4  => ["name" => "Aura de Vitalité", "xp" => 70000, "desc" => "Régénération I permanente sur les claims."],
            5  => ["name" => "Expansion I", "xp" => 100000, "desc" => "Capacité maximale étendue à 12 membres."],
            6  => ["name" => "Tourelle Arcanique", "xp" => 140000, "desc" => "Tourelle de défense automatique au Cœur."],
            7  => ["name" => "Focalisation Boréale", "xp" => 190000, "desc" => "Délai /f home réduit à 3 secondes."],
            8  => ["name" => "Coffre Commun II", "xp" => 250000, "desc" => "Coffre de guilde étendu à 27 slots."],
            9  => ["name" => "Hâte Tellurique", "xp" => 320000, "desc" => "Hâte I permanente sous les claims de faction."],
            10 => ["name" => "Sanctuaire d'Ascension", "xp" => 400000, "desc" => "Stèle de réassignation d'Ordre au Cœur."],
            11 => ["name" => "Blindage Sismique", "xp" => 500000, "desc" => "-20% de dégâts TNT sur les murailles."],
            12 => ["name" => "Privilège Marchand", "xp" => 620000, "desc" => "Taxe Hôtel des Ventes réduite à 3.5%."],
            13 => ["name" => "Grenier d'Abondance", "xp" => 760000, "desc" => "Faim ralentie de 50% sur le territoire."],
            14 => ["name" => "Coffre Commun III", "xp" => 920000, "desc" => "Coffre de guilde étendu à 54 slots."],
            15 => ["name" => "Expansion II", "xp" => 1100000, "desc" => "Capacité maximale étendue à 15 membres."],
            16 => ["name" => "Coffre-Fort Inviolable", "xp" => 1350000, "desc" => "9 slots d'objets impillables même après chute."],
            17 => ["name" => "Aura de Maîtrise", "xp" => 1650000, "desc" => "+10% de gains d'XP d'Ordre en territoire."],
            18 => ["name" => "Foudre Orbitale", "xp" => 2000000, "desc" => "Frappes de foudre sur les assaillants en siège."],
            19 => ["name" => "Dernier Rempart", "xp" => 2450000, "desc" => "Résistance Héroïque II si Cœur sous 20% PV."],
            20 => ["name" => "Statut Mythique", "xp" => 3000000, "desc" => "Bannière Holographique animée & gloire éternelle."]
        ];
    }
}
