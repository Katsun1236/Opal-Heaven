<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$creationsDir = $root . '/MES_CREATIONS';
$packDir = $root . '/server/resource_packs/OpalPack';

echo "=== INTEGRATION DU RESOURCE PACK OPALPACK ===\n";

// 1. Create target directory structure
$subdirs = [
    'texts',
    'textures',
    'textures/blocks',
    'textures/items',
    'textures/models',
    'textures/models/armor',
    'textures/entity',
    'models',
    'models/blocks',
    'models/entity',
    'animations',
];
foreach ($subdirs as $sub) {
    $dir = $packDir . '/' . $sub;
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
        echo "Created directory: $sub\n";
    }
}

// Helper: Bedrock geometry conversion
function convertBbToBedrock(array $bbData, string $defaultIdentifier): array {
    $identifier = $bbData['geometry_name'] ?? $defaultIdentifier;
    if (!str_starts_with($identifier, 'geometry.')) {
        $identifier = 'geometry.' . $identifier;
    }
    
    $texWidth = $bbData['resolution']['width'] ?? 64;
    $texHeight = $bbData['resolution']['height'] ?? 64;
    $visBox = $bbData['visible_box'] ?? [16, 16, 16];
    
    $elementsByUuid = [];
    if (isset($bbData['elements'])) {
        foreach ($bbData['elements'] as $elem) {
            if (isset($elem['uuid'])) {
                $elementsByUuid[$elem['uuid']] = $elem;
            }
        }
    }
    
    $bones = [];
    $processedElementUuids = [];
    
    $processGroup = function(array $group, ?string $parentName = null) use (&$processGroup, &$bones, &$elementsByUuid, &$processedElementUuids) {
        $boneName = $group['name'] ?? 'bone_' . count($bones);
        $bone = [
            'name' => $boneName,
            'pivot' => $group['origin'] ?? [0, 0, 0],
        ];
        if ($parentName !== null) {
            $bone['parent'] = $parentName;
        }
        if (!empty($group['rotation'])) {
            $bone['rotation'] = $group['rotation'];
        }
        
        $cubes = [];
        if (isset($group['children'])) {
            foreach ($group['children'] as $child) {
                if (is_string($child) && isset($elementsByUuid[$child])) {
                    $elem = $elementsByUuid[$child];
                    $processedElementUuids[$child] = true;
                    $from = $elem['from'] ?? [0, 0, 0];
                    $to = $elem['to'] ?? [1, 1, 1];
                    $size = [
                        round($to[0] - $from[0], 4),
                        round($to[1] - $from[1], 4),
                        round($to[2] - $from[2], 4)
                    ];
                    $cube = [
                        'origin' => $from,
                        'size' => $size,
                    ];
                    if (isset($elem['uv_offset'])) {
                        $cube['uv'] = $elem['uv_offset'];
                    } elseif (isset($elem['faces'])) {
                        $faces = [];
                        foreach ($elem['faces'] as $fName => $fData) {
                            if (isset($fData['uv'])) {
                                $uv = $fData['uv'];
                                $faces[$fName] = [
                                    'uv' => [$uv[0], $uv[1]],
                                    'uv_size' => [$uv[2] - $uv[0], $uv[3] - $uv[1]]
                                ];
                            }
                        }
                        $cube['uv'] = $faces;
                    }
                    if (!empty($elem['inflate'])) {
                        $cube['inflate'] = $elem['inflate'];
                    }
                    if (!empty($elem['rotation']) && $elem['rotation'] !== [0, 0, 0]) {
                        $cube['rotation'] = $elem['rotation'];
                        $cube['pivot'] = $elem['origin'] ?? $bone['pivot'];
                    }
                    $cubes[] = $cube;
                } elseif (is_array($child)) {
                    $processGroup($child, $boneName);
                }
            }
        }
        if (!empty($cubes)) {
            $bone['cubes'] = $cubes;
        }
        $bones[] = $bone;
    };
    
    if (isset($bbData['outliner']) && !empty($bbData['outliner'])) {
        foreach ($bbData['outliner'] as $item) {
            if (is_array($item)) {
                $processGroup($item, null);
            } elseif (is_string($item) && isset($elementsByUuid[$item])) {
                $elem = $elementsByUuid[$item];
                $processedElementUuids[$item] = true;
                $from = $elem['from'] ?? [0, 0, 0];
                $to = $elem['to'] ?? [1, 1, 1];
                $bones[] = [
                    'name' => 'root_' . count($bones),
                    'pivot' => $elem['origin'] ?? [0, 0, 0],
                    'cubes' => [[
                        'origin' => $from,
                        'size' => [round($to[0] - $from[0], 4), round($to[1] - $from[1], 4), round($to[2] - $from[2], 4)],
                        'uv' => $elem['uv_offset'] ?? [0, 0]
                    ]]
                ];
            }
        }
    }
    
    $orphanCubes = [];
    foreach ($elementsByUuid as $uuid => $elem) {
        if (!isset($processedElementUuids[$uuid])) {
            $from = $elem['from'] ?? [0, 0, 0];
            $to = $elem['to'] ?? [1, 1, 1];
            $orphanCubes[] = [
                'origin' => $from,
                'size' => [round($to[0] - $from[0], 4), round($to[1] - $from[1], 4), round($to[2] - $from[2], 4)],
                'uv' => $elem['uv_offset'] ?? [0, 0]
            ];
        }
    }
    if (!empty($orphanCubes)) {
        $bones[] = [
            'name' => 'extra_cubes',
            'pivot' => [0, 0, 0],
            'cubes' => $orphanCubes
        ];
    }
    
    return [
        'format_version' => '1.12.0',
        'minecraft:geometry' => [
            [
                'description' => [
                    'identifier' => $identifier,
                    'texture_width' => $texWidth,
                    'texture_height' => $texHeight,
                    'visible_bounds_width' => round(($visBox[0] ?? 16) / 16, 2),
                    'visible_bounds_height' => round(($visBox[1] ?? 16) / 16, 2),
                    'visible_bounds_offset' => [0, round(($visBox[1] ?? 16) / 32, 2), 0]
                ],
                'bones' => $bones
            ]
        ]
    ];
}

// 2. Process Blocks & Ores
echo "\n--- Traitement des BLOCS ET MINERAIS ---\n";
$blocksDir = $creationsDir . '/BLOCS_ET_MINERAIS';
$blockFiles = [
    'minerai_opale' => '01_Minerai_Opale',
    'deepslate_minerai_opale' => '02_Deepslate_Minerai_Opale',
    'bloc_opale' => '03_Bloc_Opale',
    'briques_opale' => '04_Briques_Opale',
    'lampe_opale' => '05_Lampe_Opale',
    'minerai_argent' => '06_Minerai_Argent',
    'deepslate_minerai_argent' => '07_Deepslate_Minerai_Argent',
    'bloc_argent' => '08_Bloc_Argent',
    'minerai_rubis' => '09_Minerai_Rubis',
    'deepslate_minerai_rubis' => '10_Deepslate_Minerai_Rubis',
    'bloc_rubis' => '11_Bloc_Rubis',
    'amas_cristaux_rubis' => '12_Amas_Cristaux_Rubis'
];

$terrainTextures = [];
$itemTextures = [];

foreach ($blockFiles as $key => $folder) {
    $dir = $blocksDir . '/' . $folder;
    $pngSrc = $dir . '/' . $key . '.png';
    $jsonSrc = $dir . '/' . $key . '.json';
    
    if (file_exists($pngSrc)) {
        copy($pngSrc, $packDir . '/textures/blocks/' . $key . '.png');
        copy($pngSrc, $packDir . '/textures/items/' . $key . '.png');
        $terrainTextures[$key] = ['textures' => 'textures/blocks/' . $key];
        $itemTextures[$key] = ['textures' => 'textures/items/' . $key];
        echo "  [BLOCK TEXTURE] $key.png copié dans textures/blocks & textures/items\n";
    }
    
    if (file_exists($jsonSrc)) {
        copy($jsonSrc, $packDir . '/models/blocks/' . $key . '.json');
        
        // Also convert to Bedrock block geometry
        $jsonData = json_decode(file_get_contents($jsonSrc), true);
        if ($jsonData && isset($jsonData['elements'])) {
            $cubes = [];
            foreach ($jsonData['elements'] as $elem) {
                $from = $elem['from'] ?? [0, 0, 0];
                $to = $elem['to'] ?? [16, 16, 16];
                $cubes[] = [
                    'origin' => $from,
                    'size' => [$to[0] - $from[0], $to[1] - $from[1], $to[2] - $from[2]],
                    'uv' => [0, 0]
                ];
            }
            $geo = [
                'format_version' => '1.12.0',
                'minecraft:geometry' => [
                    [
                        'description' => [
                            'identifier' => 'geometry.' . $key,
                            'texture_width' => 16,
                            'texture_height' => 16
                        ],
                        'bones' => [
                            [
                                'name' => 'root',
                                'pivot' => [8, 8, 8],
                                'cubes' => $cubes
                            ]
                        ]
                    ]
                ]
            ];
            file_put_contents($packDir . '/models/blocks/' . $key . '.geo.json', json_encode($geo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        echo "  [BLOCK MODEL] $key.json installé dans models/blocks\n";
    }
}

// 3. Process Bosses
echo "\n--- Traitement des BOSS ---\n";
$bossDir = $creationsDir . '/BOSS';
$bossList = [
    'golem_de_basalte' => ['folder' => '01_Golem_de_Basalte', 'geo' => 'geometry.boss_basalt_golem', 'id' => 'boss_basalt_golem'],
    'hydre_des_cavernes' => ['folder' => '02_Hydre_des_Cavernes', 'geo' => 'geometry.boss_cave_hydra', 'id' => 'boss_cave_hydra'],
    'roi_dechu' => ['folder' => '03_Roi_Dechu', 'geo' => 'geometry.boss_fallen_monarch', 'id' => 'boss_fallen_monarch'],
    'seigneur_vampire' => ['folder' => '04_Seigneur_Vampire', 'geo' => 'geometry.boss_vampire_lord', 'id' => 'boss_vampire_lord'],
    'sentinelle_de_pierre' => ['folder' => '05_Sentinelle_de_Pierre', 'geo' => 'geometry.boss_stone_sentinel', 'id' => 'boss_stone_sentinel'],
    'behemoth_tellurique' => ['folder' => '02_Behemoth_Tellurique', 'geo' => 'geometry.boss_telluric_behemoth', 'id' => 'boss_telluric_behemoth'],
];

foreach ($bossList as $key => $info) {
    $dir = $bossDir . '/' . $info['folder'];
    $pngSrc = $dir . '/' . $key . '.png';
    $jsonSrc = $dir . '/' . $key . '.json';
    
    if (file_exists($pngSrc)) {
        copy($pngSrc, $packDir . '/textures/entity/' . $key . '.png');
        copy($pngSrc, $packDir . '/textures/items/' . $info['id'] . '.png');
        $itemTextures[$info['id']] = ['textures' => 'textures/items/' . $info['id']];
        echo "  [BOSS TEXTURE] $key.png copié dans textures/entity & textures/items\n";
    }
    
    if (file_exists($jsonSrc)) {
        copy($jsonSrc, $packDir . '/models/entity/' . $key . '.json');
        $bb = json_decode(file_get_contents($jsonSrc), true);
        if ($bb && isset($bb['outliner'])) {
            $geo = convertBbToBedrock($bb, $info['geo']);
            file_put_contents($packDir . '/models/entity/' . $key . '.geo.json', json_encode($geo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        echo "  [BOSS MODEL] $key.json & $key.geo.json installés dans models/entity\n";
    }
}

// 4. Process Monstres
echo "\n--- Traitement des MONSTRES ---\n";
$monstresDir = $creationsDir . '/MONSTRES';
$monstresList = [
    'scorpion_magma' => ['folder' => '01_Scorpion_Magma', 'geo' => 'geometry.mob_magma_scorpion', 'id' => 'mob_magma_scorpion'],
    'araignee_cristal' => ['folder' => '02_Araignee_de_Cristal', 'geo' => 'geometry.mob_crystal_spider', 'id' => 'mob_crystal_spider'],
    'crabe_opale' => ['folder' => '03_Crabe_Opale', 'geo' => 'geometry.mob_opal_crab', 'id' => 'mob_opal_crab'],
    'loup_ombres' => ['folder' => '04_Loup_des_Ombres', 'geo' => 'geometry.mob_shadow_wolf', 'id' => 'mob_shadow_wolf'],
    'gargouille_basalte' => ['folder' => '05_Gargouille_de_Basalte', 'geo' => 'geometry.mob_basalt_gargoyle', 'id' => 'mob_basalt_gargoyle'],
    'guerrier_tribu' => ['folder' => '05_Guerrier_Tribu', 'geo' => 'geometry.mob_tribal_warrior', 'id' => 'mob_tribal_warrior'],
    'archer_tribu' => ['folder' => '06_Archer_Tribu', 'geo' => 'geometry.mob_tribal_archer', 'id' => 'mob_tribal_archer'],
    'fouisseur_profondeurs' => ['folder' => '06_Fouisseur_des_Profondeurs', 'geo' => 'geometry.mob_deep_burrower', 'id' => 'mob_deep_burrower'],
    'chaman_tribu' => ['folder' => '07_Chaman_Tribu', 'geo' => 'geometry.mob_tribal_shaman', 'id' => 'mob_tribal_shaman'],
    'scarabee_mines' => ['folder' => '07_Scarabee_des_Mines', 'geo' => 'geometry.mob_mine_scarab', 'id' => 'mob_mine_scarab'],
    'berserker_tribu' => ['folder' => '08_Berserker_Tribu', 'geo' => 'geometry.mob_tribal_berserker', 'id' => 'mob_tribal_berserker'],
    'chauve_souris_cryptes' => ['folder' => '08_Chauve_Souris_des_Cryptes', 'geo' => 'geometry.mob_crypt_bat', 'id' => 'mob_crypt_bat'],
    'lezard_prismatique' => ['folder' => '09_Lezard_Prismatique', 'geo' => 'geometry.mob_prismatic_lizard', 'id' => 'mob_prismatic_lizard'],
    'moustique_geant' => ['folder' => '09_Moustique_Geant', 'geo' => 'geometry.mob_giant_mosquito', 'id' => 'mob_giant_mosquito'],
    'goule_marais' => ['folder' => '10_Goule_des_Marais', 'geo' => 'geometry.mob_swamp_ghoul', 'id' => 'mob_swamp_ghoul'],
    'spectre_cendres' => ['folder' => '11_Spectre_des_Cendres', 'geo' => 'geometry.mob_ash_spectre', 'id' => 'mob_ash_spectre'],
    'rampant_braises' => ['folder' => '12_Rampant_des_Braises', 'geo' => 'geometry.mob_ember_crawler', 'id' => 'mob_ember_crawler'],
];

foreach ($monstresList as $key => $info) {
    $dir = $monstresDir . '/' . $info['folder'];
    $pngSrc = $dir . '/' . $key . '.png';
    $jsonSrc = $dir . '/' . $key . '.json';
    
    if (file_exists($pngSrc)) {
        copy($pngSrc, $packDir . '/textures/entity/' . $key . '.png');
        copy($pngSrc, $packDir . '/textures/items/' . $info['id'] . '.png');
        $itemTextures[$info['id']] = ['textures' => 'textures/items/' . $info['id']];
        echo "  [MOB TEXTURE] $key.png copié dans textures/entity & textures/items\n";
    }
    
    if (file_exists($jsonSrc)) {
        copy($jsonSrc, $packDir . '/models/entity/' . $key . '.json');
        $bb = json_decode(file_get_contents($jsonSrc), true);
        if ($bb && isset($bb['outliner'])) {
            $geo = convertBbToBedrock($bb, $info['geo']);
            file_put_contents($packDir . '/models/entity/' . $key . '.geo.json', json_encode($geo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        echo "  [MOB MODEL] $key.json & $key.geo.json installés dans models/entity\n";
    }
}

// 5. Process Petits Mobs Paladium
echo "\n--- Traitement des PETITS MOBS PALADIUM ---\n";
$pmDir = $creationsDir . '/OPAL_HEAVEN/5_BOSS_ET_CREATURES/PETITS_MOBS_PALADIUM';
$petitsMobs = [
    'mob_petit_guerrier' => '01_Petit_Guerrier_Bouclier',
    'mob_petit_archer' => '02_Petit_Archer_Cornu',
    'mob_petit_shaman' => '03_Petit_Shaman_Opale',
    'mob_petit_berserker' => '04_Petit_Berserker_Dagues',
    'mob_petit_souffleur' => '05_Petit_Souffleur_Sarbacane',
    'mob_petit_moustique' => '06_Petit_Moustique_Marais',
];
foreach ($petitsMobs as $key => $folder) {
    $dir = $pmDir . '/' . $folder;
    $pngSrc = $dir . '/' . $key . '.png';
    $jsonSrc = $dir . '/' . $key . '.json';
    if (file_exists($pngSrc)) {
        copy($pngSrc, $packDir . '/textures/entity/' . $key . '.png');
        copy($pngSrc, $packDir . '/textures/items/' . $key . '.png');
        $itemTextures[$key] = ['textures' => 'textures/items/' . $key];
        echo "  [PETIT MOB TEXTURE] $key.png copié\n";
    }
    if (file_exists($jsonSrc)) {
        copy($jsonSrc, $packDir . '/models/entity/' . $key . '.json');
        $bb = json_decode(file_get_contents($jsonSrc), true);
        if ($bb && isset($bb['outliner'])) {
            $geo = convertBbToBedrock($bb, 'geometry.' . $key);
            file_put_contents($packDir . '/models/entity/' . $key . '.geo.json', json_encode($geo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        echo "  [PETIT MOB MODEL] $key.json & $key.geo.json installés\n";
    }
}

// 6. Process TITANE (Armures & Outils)
echo "\n--- Traitement de l'Équipement TITANE ---\n";
$titaneDir = $creationsDir . '/TITANE';
$titaneItems = [
    'casque_en_titane' => '01_Casque_en_Titane',
    'plastron_en_titane' => '02_Plastron_en_Titane',
    'jambieres_en_titane' => '03_Jambieres_en_Titane',
    'bottes_en_titane' => '04_Bottes_en_Titane',
    'epee_en_titane' => '05_Epee_en_Titane',
    'pioche_en_titane' => '06_Pioche_en_Titane',
    'hache_en_titane' => '07_Hache_en_Titane',
    'pelle_en_titane' => '08_Pelle_en_Titane',
    'masse_en_titane' => '09_Masse_en_Titane',
    'bouclier_en_titane' => '10_Bouclier_en_Titane',
];

foreach ($titaneItems as $key => $folder) {
    $dir = $titaneDir . '/' . $folder;
    $pngSrc = $dir . '/' . $key . '.png';
    $jsonSrc = $dir . '/' . $key . '.json';
    
    if (file_exists($pngSrc)) {
        copy($pngSrc, $packDir . '/textures/items/' . $key . '.png');
        if (str_contains($key, 'casque') || str_contains($key, 'plastron') || str_contains($key, 'jambieres') || str_contains($key, 'bottes')) {
            copy($pngSrc, $packDir . '/textures/models/armor/' . $key . '.png');
        }
        $itemTextures[$key] = ['textures' => 'textures/items/' . $key];
        echo "  [TITANE ITEM TEXTURE] $key.png copié\n";
    }
    
    if (file_exists($jsonSrc)) {
        copy($jsonSrc, $packDir . '/models/entity/' . $key . '.json');
        $bb = json_decode(file_get_contents($jsonSrc), true);
        if ($bb && isset($bb['outliner'])) {
            $geo = convertBbToBedrock($bb, 'geometry.' . $key);
            file_put_contents($packDir . '/models/entity/' . $key . '.geo.json', json_encode($geo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        echo "  [TITANE MODEL] $key.json installé dans models/entity\n";
    }
}

// 7. Process OPALE (Armures & Outils)
echo "\n--- Traitement de l'Équipement OPALE ---\n";
$opaleDir = $creationsDir . '/OPALE';
$opaleItems = [
    'epee_opale' => 'OUTILS_ET_ARMES/01_Epee_Opale',
    'pioche_opale' => 'OUTILS_ET_ARMES/02_Pioche_Opale',
    'hache_opale' => 'OUTILS_ET_ARMES/03_Hache_Opale',
    'pelle_opale' => 'OUTILS_ET_ARMES/04_Pelle_Opale',
    'faux_opale' => 'OUTILS_ET_ARMES/05_Faux_Opale',
    'pavois_opale' => 'OUTILS_ET_ARMES/06_Pavois_Opale',
    'casque_opale' => 'PIECES_D_ARMURE/01_Casque_Opale',
    'plastron_opale' => 'PIECES_D_ARMURE/02_Plastron_Opale',
    'jambieres_opale' => 'PIECES_D_ARMURE/03_Jambieres_Opale',
    'bottes_opale' => 'PIECES_D_ARMURE/04_Bottes_Opale',
];

foreach ($opaleItems as $key => $subPath) {
    $dir = $opaleDir . '/' . $subPath;
    $pngSrc = $dir . '/' . $key . '.png';
    $jsonSrc = $dir . '/' . $key . '.json';
    
    if (file_exists($pngSrc)) {
        copy($pngSrc, $packDir . '/textures/items/' . $key . '.png');
        if (str_contains($key, 'casque') || str_contains($key, 'plastron') || str_contains($key, 'jambieres') || str_contains($key, 'bottes')) {
            copy($pngSrc, $packDir . '/textures/models/armor/' . $key . '.png');
        }
        $itemTextures[$key] = ['textures' => 'textures/items/' . $key];
        echo "  [OPALE ITEM TEXTURE] $key.png copié\n";
    }
    
    if (file_exists($jsonSrc)) {
        copy($jsonSrc, $packDir . '/models/entity/' . $key . '.json');
        $bb = json_decode(file_get_contents($jsonSrc), true);
        if ($bb && isset($bb['outliner'])) {
            $geo = convertBbToBedrock($bb, 'geometry.' . $key);
            file_put_contents($packDir . '/models/entity/' . $key . '.geo.json', json_encode($geo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        echo "  [OPALE MODEL] $key.json installé dans models/entity\n";
    }
}

// 8. Process other Tiers (Armures & Armes 3D)
echo "\n--- Traitement des Armures & Armes des autres Tiers ---\n";
// Armures complètes
$armures = [
    'armure_opale_celeste' => '01_Armure_Opale_Celeste',
    'armure_titane_forge' => '02_Armure_Titane_Forge',
    'armure_basalte_magma' => '03_Armure_Basalte_Magma',
    'armure_obsidienne_neant' => '04_Armure_Obsidienne_Neant',
];
foreach ($armures as $key => $folder) {
    $dir = $creationsDir . '/OPAL_HEAVEN/7_ARMURES_3D/' . $folder;
    $pngSrc = $dir . '/' . $key . '.png';
    $jsonSrc = $dir . '/' . $key . '.json';
    if (file_exists($pngSrc)) {
        copy($pngSrc, $packDir . '/textures/models/armor/' . $key . '.png');
    }
    if (file_exists($jsonSrc)) {
        copy($jsonSrc, $packDir . '/models/entity/' . $key . '.json');
        $bb = json_decode(file_get_contents($jsonSrc), true);
        if ($bb && isset($bb['outliner'])) {
            $geo = convertBbToBedrock($bb, 'geometry.' . $key);
            file_put_contents($packDir . '/models/entity/' . $key . '.geo.json', json_encode($geo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
}

// Pièces d'armure Basalte & Obsidienne
$tierPieces = [
    'casque_basalte' => 'OPAL_HEAVEN/7_PIECES_D_ARMURES_3D/03_TIER_BASALTE_MAGMA/01_Casque_Basalte/casque_basalte',
    'plastron_basalte' => 'OPAL_HEAVEN/7_PIECES_D_ARMURES_3D/03_TIER_BASALTE_MAGMA/02_Plastron_Basalte/plastron_basalte',
    'jambieres_basalte' => 'OPAL_HEAVEN/7_PIECES_D_ARMURES_3D/03_TIER_BASALTE_MAGMA/03_Jambieres_Basalte/jambieres_basalte',
    'bottes_basalte' => 'OPAL_HEAVEN/7_PIECES_D_ARMURES_3D/03_TIER_BASALTE_MAGMA/04_Bottes_Basalte/bottes_basalte',
    'casque_neant' => 'OPAL_HEAVEN/7_PIECES_D_ARMURES_3D/04_TIER_OBSIDIENNE_NEANT/01_Casque_Neant/casque_neant',
    'plastron_neant' => 'OPAL_HEAVEN/7_PIECES_D_ARMURES_3D/04_TIER_OBSIDIENNE_NEANT/02_Plastron_Neant/plastron_neant',
    'jambieres_neant' => 'OPAL_HEAVEN/7_PIECES_D_ARMURES_3D/04_TIER_OBSIDIENNE_NEANT/03_Jambieres_Neant/jambieres_neant',
    'bottes_neant' => 'OPAL_HEAVEN/7_PIECES_D_ARMURES_3D/04_TIER_OBSIDIENNE_NEANT/04_Bottes_Neant/bottes_neant',
];
foreach ($tierPieces as $key => $sub) {
    $pngSrc = $creationsDir . '/' . $sub . '.png';
    $jsonSrc = $creationsDir . '/' . $sub . '.json';
    if (file_exists($pngSrc)) {
        copy($pngSrc, $packDir . '/textures/items/' . $key . '.png');
        copy($pngSrc, $packDir . '/textures/models/armor/' . $key . '.png');
        $itemTextures[$key] = ['textures' => 'textures/items/' . $key];
    }
    if (file_exists($jsonSrc)) {
        copy($jsonSrc, $packDir . '/models/entity/' . $key . '.json');
        $bb = json_decode(file_get_contents($jsonSrc), true);
        if ($bb && isset($bb['outliner'])) {
            $geo = convertBbToBedrock($bb, 'geometry.' . $key);
            file_put_contents($packDir . '/models/entity/' . $key . '.geo.json', json_encode($geo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
}

// Armes Basalte & Obsidienne
$extraWeapons = [
    'lame_volcanique' => 'OPAL_HEAVEN/8_OUTILS_ET_ARMES_3D/TIER_BASALTE_MAGMA/01_Lame_Volcanique/lame_volcanique',
    'pioche_magma' => 'OPAL_HEAVEN/8_OUTILS_ET_ARMES_3D/TIER_BASALTE_MAGMA/02_Pioche_Magma/pioche_magma',
    'hache_eruptive' => 'OPAL_HEAVEN/8_OUTILS_ET_ARMES_3D/TIER_BASALTE_MAGMA/03_Hache_Eruptive/hache_eruptive',
    'dague_braise' => 'OPAL_HEAVEN/8_OUTILS_ET_ARMES_3D/TIER_BASALTE_MAGMA/04_Dague_Braise/dague_braise',
    'faux_oblivion' => 'OPAL_HEAVEN/8_OUTILS_ET_ARMES_3D/TIER_OBSIDIENNE_NEANT/01_Faux_Oblivion/faux_oblivion',
    'dague_ombre' => 'OPAL_HEAVEN/8_OUTILS_ET_ARMES_3D/TIER_OBSIDIENNE_NEANT/02_Dagues_Jumelles_Ombre/dague_ombre',
    'baton_astral' => 'OPAL_HEAVEN/8_OUTILS_ET_ARMES_3D/TIER_OBSIDIENNE_NEANT/03_Baton_Astral/baton_astral',
    'pavois_neant' => 'OPAL_HEAVEN/8_OUTILS_ET_ARMES_3D/TIER_OBSIDIENNE_NEANT/04_Pavois_Neant/pavois_neant',
    'epee_lourde_titane' => 'OPAL_HEAVEN/8_OUTILS_ET_ARMES_3D/TIER_TITANE_FORGE/01_Epee_Lourde_Titane/epee_lourde_titane',
    'pioche_mecanique_titane' => 'OPAL_HEAVEN/8_OUTILS_ET_ARMES_3D/TIER_TITANE_FORGE/02_Pioche_Mecanique_Titane/pioche_mecanique_titane',
    'hache_bataille_titane' => 'OPAL_HEAVEN/8_OUTILS_ET_ARMES_3D/TIER_TITANE_FORGE/03_Hache_Bataille_Titane/hache_bataille_titane',
    'masse_forgeron_titane' => 'OPAL_HEAVEN/8_OUTILS_ET_ARMES_3D/TIER_TITANE_FORGE/04_Masse_Forgeron_Titane/masse_forgeron_titane',
];
foreach ($extraWeapons as $key => $sub) {
    $pngSrc = $creationsDir . '/' . $sub . '.png';
    $jsonSrc = $creationsDir . '/' . $sub . '.json';
    if (file_exists($pngSrc)) {
        copy($pngSrc, $packDir . '/textures/items/' . $key . '.png');
        $itemTextures[$key] = ['textures' => 'textures/items/' . $key];
    }
    if (file_exists($jsonSrc)) {
        copy($jsonSrc, $packDir . '/models/entity/' . $key . '.json');
        $bb = json_decode(file_get_contents($jsonSrc), true);
        if ($bb && isset($bb['outliner'])) {
            $geo = convertBbToBedrock($bb, 'geometry.' . $key);
            file_put_contents($packDir . '/models/entity/' . $key . '.geo.json', json_encode($geo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
}

// 9. Copy Bedrock Animations
echo "\n--- Traitement des ANIMATIONS BEDROCK ---\n";
$animDir = $creationsDir . '/OPAL_HEAVEN/6_ANIMATIONS_BEDROCK';
if (is_dir($animDir)) {
    foreach (glob($animDir . '/*.json') as $animFile) {
        copy($animFile, $packDir . '/animations/' . basename($animFile));
        echo "  [ANIMATION] " . basename($animFile) . " copié dans animations/\n";
    }
}

// 10. Base fallback items
$itemTextures['opal_gem'] = ['textures' => 'textures/items/bloc_opale'];
$itemTextures['celestite_ingot'] = ['textures' => 'textures/items/bloc_argent'];
$itemTextures['onix_shard'] = ['textures' => 'textures/items/bloc_rubis'];

// 11. Write textures/terrain_texture.json
echo "\n--- Génération de terrain_texture.json ---\n";
$terrainData = [
    'resource_pack_name' => 'OpalPack',
    'texture_name' => 'atlas.terrain',
    'padding' => 8,
    'num_mip_levels' => 4,
    'texture_data' => $terrainTextures
];
file_put_contents(
    $packDir . '/textures/terrain_texture.json',
    json_encode($terrainData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);
echo "  Enregistré avec " . count($terrainTextures) . " textures de terrain.\n";

// 12. Write textures/item_texture.json
echo "\n--- Génération de item_texture.json ---\n";
$itemData = [
    'resource_pack_name' => 'OpalPack',
    'texture_name' => 'atlas.items',
    'texture_data' => $itemTextures
];
file_put_contents(
    $packDir . '/textures/item_texture.json',
    json_encode($itemData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);
echo "  Enregistré avec " . count($itemTextures) . " textures d'items.\n";

// 13. Write texts/fr_FR.lang
echo "\n--- Génération de texts/fr_FR.lang ---\n";
$frLang = [
    // Minerais & Blocs
    'tile.opal:opal_ore.name=Minerai d\'Opale Céleste',
    'tile.opal:deepslate_opal_ore.name=Minerai d\'Opale des Abîmes',
    'tile.opal:opal_block.name=Bloc d\'Opale Pure',
    'tile.opal:opal_bricks.name=Briques d\'Opale Céleste',
    'tile.opal:opal_lamp.name=Lampe d\'Opale Luminescente',
    'tile.opal:silver_ore.name=Minerai d\'Argent',
    'tile.opal:deepslate_silver_ore.name=Minerai d\'Argent des Abîmes',
    'tile.opal:silver_block.name=Bloc d\'Argent Massif',
    'tile.opal:ruby_ore.name=Minerai de Rubis Flamboyant',
    'tile.opal:deepslate_ruby_ore.name=Minerai de Rubis des Abîmes',
    'tile.opal:ruby_block.name=Bloc de Rubis Massif',
    'tile.opal:ruby_crystal_cluster.name=Amas de Cristaux de Rubis & Opale',
    'tile.opal:heart_block.name=Cœur de Faction (Nexus)',

    // Items de base
    'item.opal:opal_gem.name=Opale Céleste',
    'item.opal:celestite_ingot.name=Lingot d\'Argent Pur',
    'item.opal:onix_shard.name=Éclat de Rubis Ardent',

    // Équipement Titane
    'item.opal:casque_en_titane.name=Casque en Titane Forgé',
    'item.opal:plastron_en_titane.name=Plastron en Titane Forgé',
    'item.opal:jambieres_en_titane.name=Jambières en Titane Forgé',
    'item.opal:bottes_en_titane.name=Bottes en Titane Forgé',
    'item.opal:epee_en_titane.name=Épée en Titane',
    'item.opal:pioche_en_titane.name=Pioche en Titane',
    'item.opal:hache_en_titane.name=Hache en Titane',
    'item.opal:pelle_en_titane.name=Pelle en Titane',
    'item.opal:masse_en_titane.name=Masse de Guerre en Titane',
    'item.opal:bouclier_en_titane.name=Bouclier Renforcé en Titane',

    // Équipement Opale
    'item.opal:casque_opale.name=Casque en Opale Céleste',
    'item.opal:plastron_opale.name=Plastron en Opale Céleste',
    'item.opal:jambieres_opale.name=Jambières en Opale Céleste',
    'item.opal:bottes_opale.name=Bottes en Opale Céleste',
    'item.opal:epee_opale.name=Épée Céleste d\'Opale',
    'item.opal:pioche_opale.name=Pioche Céleste d\'Opale',
    'item.opal:hache_opale.name=Hache Céleste d\'Opale',
    'item.opal:pelle_opale.name=Pelle Céleste d\'Opale',
    'item.opal:faux_opale.name=Faux Céleste d\'Opale',
    'item.opal:pavois_opale.name=Pavois Céleste d\'Opale',

    // Boss & Monstres
    'entity.opal:boss_basalt_golem.name=Golem de Basalte',
    'entity.opal:boss_cave_hydra.name=Hydre des Cavernes',
    'entity.opal:boss_fallen_monarch.name=Roi Déchu',
    'entity.opal:boss_vampire_lord.name=Seigneur Vampire',
    'entity.opal:boss_stone_sentinel.name=Sentinelle de Pierre',
    'entity.opal:boss_telluric_behemoth.name=Béhémoth Tellurique',
    'entity.opal:mob_magma_scorpion.name=Scorpion de Magma',
    'entity.opal:mob_crystal_spider.name=Araignée de Cristal',
    'entity.opal:mob_opal_crab.name=Crabe d\'Opale',
    'entity.opal:mob_shadow_wolf.name=Loup des Ombres',
    'entity.opal:mob_basalt_gargoyle.name=Gargouille de Basalte',
    'entity.opal:mob_tribal_warrior.name=Guerrier de la Tribu',
    'entity.opal:mob_tribal_archer.name=Archer de la Tribu',
    'entity.opal:mob_deep_burrower.name=Fouisseur des Profondeurs',
    'entity.opal:mob_tribal_shaman.name=Chaman de la Tribu',
    'entity.opal:mob_mine_scarab.name=Scarabée des Mines',
    'entity.opal:mob_tribal_berserker.name=Berserker de la Tribu',
    'entity.opal:mob_crypt_bat.name=Chauve-souris des Cryptes',
    'entity.opal:mob_prismatic_lizard.name=Lézard Prismatique',
    'entity.opal:mob_giant_mosquito.name=Moustique Géant',
    'entity.opal:mob_swamp_ghoul.name=Goule des Marais',
    'entity.opal:mob_ash_spectre.name=Spectre des Cendres',
    'entity.opal:mob_ember_crawler.name=Rampant des Braises',

    // Petits Mobs
    'entity.opal:mob_petit_guerrier.name=Petit Guerrier au Bouclier',
    'entity.opal:mob_petit_archer.name=Petit Archer Cornu',
    'entity.opal:mob_petit_shaman.name=Petit Shaman d\'Opale',
    'entity.opal:mob_petit_berserker.name=Petit Berserker aux Dagues',
    'entity.opal:mob_petit_souffleur.name=Petit Souffleur à Sarbacane',
    'entity.opal:mob_petit_moustique.name=Petit Moustique des Marais',
];
file_put_contents($packDir . '/texts/fr_FR.lang', implode("\n", $frLang) . "\n");
file_put_contents($packDir . '/texts/en_US.lang', implode("\n", $frLang) . "\n");
echo "  fr_FR.lang & en_US.lang générés avec succès.\n";

// 14. Update manifest.json version
$manifest = json_decode(file_get_contents($packDir . '/manifest.json'), true);
$manifest['header']['version'] = [1, 1, 0];
$manifest['modules'][0]['version'] = [1, 1, 0];
file_put_contents($packDir . '/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

// 15. Create OpalPack.zip in server and server_hub
echo "\n--- Compression de OpalPack.zip ---\n";
function zipDirectory(string $sourceDir, string $outZipPath): void {
    if (file_exists($outZipPath)) {
        unlink($outZipPath);
    }
    $zip = new ZipArchive();
    if ($zip->open($outZipPath, ZipArchive::CREATE) !== true) {
        throw new RuntimeException("Cannot open $outZipPath");
    }
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    foreach ($files as $file) {
        if (!$file->isFile()) continue;
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen(realpath($sourceDir)) + 1);
        $relativePath = str_replace('\\', '/', $relativePath);
        $zip->addFile($filePath, $relativePath);
    }
    $zip->close();
    echo "  Archive créée : $outZipPath (" . round(filesize($outZipPath) / 1024, 1) . " KB)\n";
}

$zipDest1 = $root . '/server/resource_packs/OpalPack.zip';
$zipDest2 = $root . '/server_hub/resource_packs/OpalPack.zip';

zipDirectory($packDir, $zipDest1);
copy($zipDest1, $zipDest2);
echo "  Copié vers $zipDest2\n";

// 16. Update resource_packs.yml in server & server_hub
echo "\n--- Configuration de resource_packs.yml ---\n";
$ymlContent = <<<YAML
# Opal Heaven Resource Packs Configuration
force_resources: true
resource_stack:
  - "OpalPack.zip"

YAML;

file_put_contents($root . '/server/resource_packs/resource_packs.yml', $ymlContent);
file_put_contents($root . '/server_hub/resource_packs/resource_packs.yml', $ymlContent);
echo "  resource_packs.yml configuré avec force_resources: true et OpalPack.zip dans server & server_hub.\n";

echo "\n=== INTÉGRATION RESOURCE PACK TERMINÉE AVEC SUCCÈS ! ===\n";
