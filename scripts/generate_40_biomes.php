<?php

$biomesDir = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/plugins/OpalCore/resources/biomes';
if (!is_dir($biomesDir)) {
    mkdir($biomesDir, 0777, true);
}

$biomes = [
    // 1. ROYAUME CELESTE & OPALE
    [
        'file' => 'foret_opale_cristalline.yml',
        'id' => 'opal:foret_opale_cristalline',
        'name' => "§dForêt d'Opale Cristalline",
        'category' => 'CELESTE',
        'rarity' => 8,
        'climate' => ['temperature' => 0.6, 'humidity' => 0.8, 'downfall' => 0.4],
        'colors' => ['sky_color' => '#b388ff', 'fog_color' => '#e1bee7', 'water_color' => '#7c4dff', 'grass_color' => '#ce93d8'],
        'blocks' => ['top_block' => 'amethyst_block', 'sub_block' => 'calcite', 'deep_block' => 'smooth_basalt'],
        'effects' => ['aura_effect' => 'REGENERATION', 'particle' => 'minecraft:enchantment_table', 'gravity' => 1.0],
        'desc' => "Une forêt sacrée où les troncs d'arbres sont faits d'améthyste et où l'air scintille de poussière d'Opale."
    ],
    [
        'file' => 'vallee_des_cieux.yml',
        'id' => 'opal:vallee_des_cieux',
        'name' => "§bVallée des Cieux",
        'category' => 'CELESTE',
        'rarity' => 7,
        'climate' => ['temperature' => 0.3, 'humidity' => 0.5, 'downfall' => 0.1],
        'colors' => ['sky_color' => '#80d8ff', 'fog_color' => '#b3e5fc', 'water_color' => '#00b0ff', 'grass_color' => '#81d4fa'],
        'blocks' => ['top_block' => 'quartz_block', 'sub_block' => 'white_concrete', 'deep_block' => 'diorite'],
        'effects' => ['aura_effect' => 'JUMP_BOOST', 'particle' => 'minecraft:end_rod', 'gravity' => 0.75],
        'desc' => "Plateaux éthérés baignés dans la lumière d'altitude où la gravité est plus légère."
    ],
    [
        'file' => 'pics_auroraux.yml',
        'id' => 'opal:pics_auroraux',
        'name' => "§3Pics Auroraux",
        'category' => 'CELESTE',
        'rarity' => 6,
        'climate' => ['temperature' => -0.2, 'humidity' => 0.9, 'downfall' => 0.8],
        'colors' => ['sky_color' => '#00e5ff', 'fog_color' => '#84ffff', 'water_color' => '#00b4d8', 'grass_color' => '#48cae4'],
        'blocks' => ['top_block' => 'packed_ice', 'sub_block' => 'blue_ice', 'deep_block' => 'deepslate'],
        'effects' => ['aura_effect' => 'SPEED', 'particle' => 'minecraft:glow_particle', 'gravity' => 1.0],
        'desc' => "Aiguilles de glace bleue illuminées par des aurores boréales permanentes boostant le sprint."
    ],
    [
        'file' => 'jardin_des_seraphins.yml',
        'id' => 'opal:jardin_des_seraphins',
        'name' => "§eJardin des Séraphins",
        'category' => 'CELESTE',
        'rarity' => 7,
        'climate' => ['temperature' => 0.8, 'humidity' => 0.7, 'downfall' => 0.3],
        'colors' => ['sky_color' => '#fff59d', 'fog_color' => '#fff9c4', 'water_color' => '#ffd54f', 'grass_color' => '#ffe082'],
        'blocks' => ['top_block' => 'moss_block', 'sub_block' => 'ochre_froglight', 'deep_block' => 'calcite'],
        'effects' => ['aura_effect' => 'SATURATION', 'particle' => 'minecraft:totem_particle', 'gravity' => 1.0],
        'desc' => "Prairies dorées fleuries dont le pollen divin rassasie continuellement les voyageurs."
    ],
    [
        'file' => 'plaine_stellaire.yml',
        'id' => 'opal:plaine_stellaire',
        'name' => "§5Plaine Stellaire",
        'category' => 'CELESTE',
        'rarity' => 6,
        'climate' => ['temperature' => 0.5, 'humidity' => 0.4, 'downfall' => 0.2],
        'colors' => ['sky_color' => '#4a148c', 'fog_color' => '#7b1fa2', 'water_color' => '#6a1b9a', 'grass_color' => '#8e24aa'],
        'blocks' => ['top_block' => 'purpur_block', 'sub_block' => 'obsidian', 'deep_block' => 'end_stone'],
        'effects' => ['aura_effect' => 'NIGHT_VISION', 'particle' => 'minecraft:portal', 'gravity' => 0.9],
        'desc' => "Étendue cosmique tapissée de poussière d'étoiles où la nuit révèle des secrets oubliés."
    ],

    // 2. PROFONDEURS ABYSSALES & OCEANIQUES
    [
        'file' => 'abysse_luminescent.yml',
        'id' => 'opal:abysse_luminescent',
        'name' => "§1Abysse Luminescent",
        'category' => 'ABYSSAL',
        'rarity' => 6,
        'climate' => ['temperature' => 0.4, 'humidity' => 1.0, 'downfall' => 1.0],
        'colors' => ['sky_color' => '#01579b', 'fog_color' => '#0288d1', 'water_color' => '#00e5ff', 'grass_color' => '#0097a7'],
        'blocks' => ['top_block' => 'prismarine', 'sub_block' => 'dark_prismarine', 'deep_block' => 'sea_lantern'],
        'effects' => ['aura_effect' => 'WATER_BREATHING', 'particle' => 'minecraft:water_bubble', 'gravity' => 1.0],
        'desc' => "Fonds marins baignés d'une lueur azur où les coraux vivants oxygènent naturellement les plongeurs."
    ],
    [
        'file' => 'recif_de_corail_fantome.yml',
        'id' => 'opal:recif_de_corail_fantome',
        'name' => "§bRécif de Corail Fantôme",
        'category' => 'ABYSSAL',
        'rarity' => 5,
        'climate' => ['temperature' => 0.7, 'humidity' => 1.0, 'downfall' => 1.0],
        'colors' => ['sky_color' => '#80deea', 'fog_color' => '#b2ebf2', 'water_color' => '#26c6da', 'grass_color' => '#4dd0e1'],
        'blocks' => ['top_block' => 'pearlescent_froglight', 'sub_block' => 'prismarine_bricks', 'deep_block' => 'calcite'],
        'effects' => ['aura_effect' => 'DOLPHINS_GRACE', 'particle' => 'minecraft:splash', 'gravity' => 1.0],
        'desc' => "Écosystème corallien spectral où nager procure une vitesse fulgurante équivalente aux dauphins."
    ],
    [
        'file' => 'fosse_hydrothermale.yml',
        'id' => 'opal:fosse_hydrothermale',
        'name' => "§4Fosse Hydrothermale",
        'category' => 'ABYSSAL',
        'rarity' => 7,
        'climate' => ['temperature' => 1.5, 'humidity' => 1.0, 'downfall' => 0.9],
        'colors' => ['sky_color' => '#b71c1c', 'fog_color' => '#d32f2f', 'water_color' => '#e53935', 'grass_color' => '#c62828'],
        'blocks' => ['top_block' => 'magma_block', 'sub_block' => 'blackstone', 'deep_block' => 'crying_obsidian'],
        'effects' => ['aura_effect' => 'FIRE_RESISTANCE', 'particle' => 'minecraft:lava', 'gravity' => 1.0],
        'desc' => "Cheminées sous-marines volcaniques crachant du magma noir et des vapeurs soufrées."
    ],
    [
        'file' => 'marais_des_sirenes.yml',
        'id' => 'opal:marais_des_sirenes',
        'name' => "§5Marais des Sirènes",
        'category' => 'ABYSSAL',
        'rarity' => 5,
        'climate' => ['temperature' => 0.8, 'humidity' => 0.9, 'downfall' => 0.8],
        'colors' => ['sky_color' => '#4a148c', 'fog_color' => '#6a1b9a', 'water_color' => '#8e24aa', 'grass_color' => '#ab47bc'],
        'blocks' => ['top_block' => 'mud', 'sub_block' => 'packed_mud', 'deep_block' => 'muddy_mangrove_roots'],
        'effects' => ['aura_effect' => 'NAUSEA', 'particle' => 'minecraft:witch_spell', 'gravity' => 1.0],
        'desc' => "Eaux saumâtres envoûtantes ornées de nénuphars géants où un chant mystérieux trouble l'esprit."
    ],
    [
        'file' => 'baie_des_epaves_perdues.yml',
        'id' => 'opal:baie_des_epaves_perdues',
        'name' => "§6Baie des Épaves Perdues",
        'category' => 'ABYSSAL',
        'rarity' => 6,
        'climate' => ['temperature' => 0.9, 'humidity' => 0.8, 'downfall' => 0.5],
        'colors' => ['sky_color' => '#ffb300', 'fog_color' => '#ffe082', 'water_color' => '#00acc1', 'grass_color' => '#ffa000'],
        'blocks' => ['top_block' => 'sand', 'sub_block' => 'sandstone', 'deep_block' => 'prismarine'],
        'effects' => ['aura_effect' => 'LUCK', 'particle' => 'minecraft:villager_happy', 'gravity' => 1.0],
        'desc' => "Cimetière marin de navires anciens regorgeant d'huîtres célestes et de coffres de dragage."
    ],

    // 3. FAILLE VOLCANIQUE & D'ONIX
    [
        'file' => 'terres_brulees_d_onix.yml',
        'id' => 'opal:terres_brulees_d_onix',
        'name' => "§8Terres Brûlées d'Onix",
        'category' => 'VOLCANIQUE',
        'rarity' => 7,
        'climate' => ['temperature' => 2.0, 'humidity' => 0.0, 'downfall' => 0.0],
        'colors' => ['sky_color' => '#212121', 'fog_color' => '#424242', 'water_color' => '#ff3d00', 'grass_color' => '#37474f'],
        'blocks' => ['top_block' => 'gilded_blackstone', 'sub_block' => 'blackstone', 'deep_block' => 'bedrock'],
        'effects' => ['aura_effect' => 'STRENGTH', 'particle' => 'minecraft:smoke', 'gravity' => 1.0],
        'desc' => "Mines à ciel ouvert de minerai d'Onix pur traversées par des coulées de soufre en fusion."
    ],
    [
        'file' => 'caldeira_magmatique.yml',
        'id' => 'opal:caldeira_magmatique',
        'name' => "§cCaldeira Magmatique",
        'category' => 'VOLCANIQUE',
        'rarity' => 6,
        'climate' => ['temperature' => 2.5, 'humidity' => 0.0, 'downfall' => 0.0],
        'colors' => ['sky_color' => '#bf360c', 'fog_color' => '#d84315', 'water_color' => '#ff5722', 'grass_color' => '#e64a19'],
        'blocks' => ['top_block' => 'magma_block', 'sub_block' => 'netherrack', 'deep_block' => 'obsidian'],
        'effects' => ['aura_effect' => 'FIRE_RESISTANCE', 'particle' => 'minecraft:flame', 'gravity' => 1.0],
        'desc' => "Bassin bouillonnant géant au sommet d'un super-volcan abritant les forges naturelles."
    ],
    [
        'file' => 'foret_de_cendres_eternelles.yml',
        'id' => 'opal:foret_de_cendres_eternelles',
        'name' => "§7Forêt de Cendres Éternelles",
        'category' => 'VOLCANIQUE',
        'rarity' => 5,
        'climate' => ['temperature' => 1.2, 'humidity' => 0.1, 'downfall' => 0.0],
        'colors' => ['sky_color' => '#616161', 'fog_color' => '#9e9e9e', 'water_color' => '#757575', 'grass_color' => '#757575'],
        'blocks' => ['top_block' => 'basalt', 'sub_block' => 'polished_basalt', 'deep_block' => 'deepslate'],
        'effects' => ['aura_effect' => 'SLOWNESS', 'particle' => 'minecraft:campfire_cosy_smoke', 'gravity' => 1.0],
        'desc' => "Arbres pétrifiés par un cataclysme séculaire où le sol est recouvert de suie volcanique meuble."
    ],
    [
        'file' => 'faille_tellurique.yml',
        'id' => 'opal:faille_tellurique',
        'name' => "§6Faille Tellurique",
        'category' => 'VOLCANIQUE',
        'rarity' => 7,
        'climate' => ['temperature' => 1.8, 'humidity' => 0.1, 'downfall' => 0.0],
        'colors' => ['sky_color' => '#e65100', 'fog_color' => '#f57c00', 'water_color' => '#ff9800', 'grass_color' => '#fb8c00'],
        'blocks' => ['top_block' => 'terracotta', 'sub_block' => 'red_sandstone', 'deep_block' => 'smooth_basalt'],
        'effects' => ['aura_effect' => 'HASTE', 'particle' => 'minecraft:crit', 'gravity' => 1.0],
        'desc' => "Canyon sismique instable où les parois vibrent d'énergie métallique boostant la vitesse de pioche."
    ],
    [
        'file' => 'dunes_d_obsidienne.yml',
        'id' => 'opal:dunes_d_obsidienne',
        'name' => "§0Dunes d'Obsidienne",
        'category' => 'VOLCANIQUE',
        'rarity' => 6,
        'climate' => ['temperature' => 1.4, 'humidity' => 0.0, 'downfall' => 0.0],
        'colors' => ['sky_color' => '#000000', 'fog_color' => '#212121', 'water_color' => '#37474f', 'grass_color' => '#263238'],
        'blocks' => ['top_block' => 'obsidian', 'sub_block' => 'crying_obsidian', 'deep_block' => 'bedrock'],
        'effects' => ['aura_effect' => 'RESISTANCE', 'particle' => 'minecraft:portal', 'gravity' => 1.0],
        'desc' => "Désert de verre volcanique tranchant où chaque bloc résiste naturellement aux pires explosifs."
    ],

    // 4. BIOMES BIOLOGIQUES & CHIMIQUES
    [
        'file' => 'jungle_de_spores_toxiques.yml',
        'id' => 'opal:jungle_de_spores_toxiques',
        'name' => "§2Jungle de Spores Toxiques",
        'category' => 'BIOLOGIQUE',
        'rarity' => 6,
        'climate' => ['temperature' => 1.1, 'humidity' => 0.95, 'downfall' => 0.9],
        'colors' => ['sky_color' => '#1b5e20', 'fog_color' => '#2e7d32', 'water_color' => '#43a047', 'grass_color' => '#66bb6a'],
        'blocks' => ['top_block' => 'moss_block', 'sub_block' => 'spore_blossom', 'deep_block' => 'rooted_dirt'],
        'effects' => ['aura_effect' => 'POISON', 'particle' => 'minecraft:spore_blossom_ambient', 'gravity' => 1.0],
        'desc' => "Végétation luxuriante mais venimeuse où les fleurs géantes projettent des toxines de siège."
    ],
    [
        'file' => 'marais_d_acide_corrosif.yml',
        'id' => 'opal:marais_d_acide_corrosif',
        'name' => "§aMarais d'Acide Corrosif",
        'category' => 'BIOLOGIQUE',
        'rarity' => 7,
        'climate' => ['temperature' => 1.0, 'humidity' => 0.9, 'downfall' => 0.7],
        'colors' => ['sky_color' => '#76ff03', 'fog_color' => '#b2ff59', 'water_color' => '#64dd17', 'grass_color' => '#76ff03'],
        'blocks' => ['top_block' => 'mud', 'sub_block' => 'sculk_catalyst', 'deep_block' => 'deepslate'],
        'effects' => ['aura_effect' => 'WEAKNESS', 'particle' => 'minecraft:villager_angry', 'gravity' => 1.0],
        'desc' => "Marécage acide capable de dissoudre les alliages basiques pour en extraire des essences de combat."
    ],
    [
        'file' => 'canopee_carnivore.yml',
        'id' => 'opal:canopee_carnivore',
        'name' => "§2Canopée Carnivore",
        'category' => 'BIOLOGIQUE',
        'rarity' => 5,
        'climate' => ['temperature' => 1.2, 'humidity' => 0.85, 'downfall' => 0.8],
        'colors' => ['sky_color' => '#004d40', 'fog_color' => '#00695c', 'water_color' => '#00897b', 'grass_color' => '#00bfa5'],
        'blocks' => ['top_block' => 'mangrove_roots', 'sub_block' => 'mangrove_wood', 'deep_block' => 'packed_mud'],
        'effects' => ['aura_effect' => 'HUNGER', 'particle' => 'minecraft:redstone_ore_dust', 'gravity' => 1.0],
        'desc' => "Forêt vivante de racines géantes cherchant à capturer les intrus imprudents."
    ],
    [
        'file' => 'clairiere_bioluminescente.yml',
        'id' => 'opal:clairiere_bioluminescente',
        'name' => "§aClairière Bioluminescente",
        'category' => 'BIOLOGIQUE',
        'rarity' => 6,
        'climate' => ['temperature' => 0.7, 'humidity' => 0.8, 'downfall' => 0.6],
        'colors' => ['sky_color' => '#00e676', 'fog_color' => '#69f0ae', 'water_color' => '#1de9b6', 'grass_color' => '#00c853'],
        'blocks' => ['top_block' => 'sculk_sensor', 'sub_block' => 'moss_block', 'deep_block' => 'calcite'],
        'effects' => ['aura_effect' => 'GLOWING', 'particle' => 'minecraft:glow_particle', 'gravity' => 1.0],
        'desc' => "Oasis végétale émettant une lumière verte éclatante idéale pour la floraison rapide des cultures."
    ],
    [
        'file' => 'vallee_fongique_geante.yml',
        'id' => 'opal:vallee_fongique_geante',
        'name' => "§dVallée Fongique Géante",
        'category' => 'BIOLOGIQUE',
        'rarity' => 5,
        'climate' => ['temperature' => 0.9, 'humidity' => 0.9, 'downfall' => 0.7],
        'colors' => ['sky_color' => '#880e4f', 'fog_color' => '#ad1457', 'water_color' => '#c2185b', 'grass_color' => '#e91e63'],
        'blocks' => ['top_block' => 'mycelium', 'sub_block' => 'mushroom_stem', 'deep_block' => 'red_mushroom_block'],
        'effects' => ['aura_effect' => 'REGENERATION', 'particle' => 'minecraft:heart', 'gravity' => 1.0],
        'desc' => "Forêt de champignons de 30 blocs de haut formant des ponts aériens naturels."
    ],

    // 5. TERRES DESOLEES & CORROMPUES
    [
        'file' => 'ruines_du_neant_obscur.yml',
        'id' => 'opal:ruines_du_neant_obscur',
        'name' => "§8Ruines du Néant Obscur",
        'category' => 'CORROMPU',
        'rarity' => 8,
        'climate' => ['temperature' => 0.1, 'humidity' => 0.2, 'downfall' => 0.0],
        'colors' => ['sky_color' => '#000000', 'fog_color' => '#001e2b', 'water_color' => '#003344', 'grass_color' => '#002533'],
        'blocks' => ['top_block' => 'sculk', 'sub_block' => 'sculk_shrieker', 'deep_block' => 'deepslate_tiles'],
        'effects' => ['aura_effect' => 'BLINDNESS', 'particle' => 'minecraft:sonic_explosion', 'gravity' => 1.0],
        'desc' => "Cité antique engloutie sous une chape de sculk où chaque son résonne d'un écho menaçant."
    ],
    [
        'file' => 'plaine_de_sang_vampirique.yml',
        'id' => 'opal:plaine_de_sang_vampirique',
        'name' => "§4Plaine de Sang Vampirique",
        'category' => 'CORROMPU',
        'rarity' => 7,
        'climate' => ['temperature' => 1.0, 'humidity' => 0.3, 'downfall' => 0.1],
        'colors' => ['sky_color' => '#880000', 'fog_color' => '#aa0000', 'water_color' => '#cc0000', 'grass_color' => '#ff1744'],
        'blocks' => ['top_block' => 'crimson_nylium', 'sub_block' => 'nether_wart_block', 'deep_block' => 'crimson_stem'],
        'effects' => ['aura_effect' => 'STRENGTH', 'particle' => 'minecraft:damage_indicator', 'gravity' => 1.0],
        'desc' => "Sanctuaire des guerriers de l'Ordre du Sang conférant un vol de vie naturel lors des duels."
    ],
    [
        'file' => 'cimetiere_des_titans.yml',
        'id' => 'opal:cimetiere_des_titans',
        'name' => "§7Cimetière des Titans",
        'category' => 'CORROMPU',
        'rarity' => 6,
        'climate' => ['temperature' => 0.5, 'humidity' => 0.3, 'downfall' => 0.2],
        'colors' => ['sky_color' => '#3e2723', 'fog_color' => '#4e342e', 'water_color' => '#5d4037', 'grass_color' => '#6d4c41'],
        'blocks' => ['top_block' => 'bone_block', 'sub_block' => 'soul_sand', 'deep_block' => 'soul_soil'],
        'effects' => ['aura_effect' => 'WITHER', 'particle' => 'minecraft:soul', 'gravity' => 1.0],
        'desc' => "Plaine hantée parsemée de cages thoraciques fossilisées de léviathans célestes anciens."
    ],
    [
        'file' => 'desert_d_ossements.yml',
        'id' => 'opal:desert_d_ossements',
        'name' => "§fDésert d'Ossements",
        'category' => 'CORROMPU',
        'rarity' => 5,
        'climate' => ['temperature' => 1.7, 'humidity' => 0.0, 'downfall' => 0.0],
        'colors' => ['sky_color' => '#cfd8dc', 'fog_color' => '#eceff1', 'water_color' => '#90a4ae', 'grass_color' => '#b0bec5'],
        'blocks' => ['top_block' => 'calcite', 'sub_block' => 'bone_block', 'deep_block' => 'white_concrete_powder'],
        'effects' => ['aura_effect' => 'WEAKNESS', 'particle' => 'minecraft:white_smoke', 'gravity' => 1.0],
        'desc' => "Dunes de poudre blanche où le vent soulève une tempête d'ossements aveuglante."
    ],
    [
        'file' => 'bastion_des_ombres.yml',
        'id' => 'opal:bastion_des_ombres',
        'name' => "§0Bastion des Ombres",
        'category' => 'CORROMPU',
        'rarity' => 7,
        'climate' => ['temperature' => 0.2, 'humidity' => 0.1, 'downfall' => 0.0],
        'colors' => ['sky_color' => '#121212', 'fog_color' => '#1e1e1e', 'water_color' => '#2c2c2c', 'grass_color' => '#1f1f1f'],
        'blocks' => ['top_block' => 'chiseled_deepslate', 'sub_block' => 'reinforced_deepslate', 'deep_block' => 'bedrock'],
        'effects' => ['aura_effect' => 'INVISIBILITY', 'particle' => 'minecraft:mob_spell', 'gravity' => 1.0],
        'desc' => "Forteresse naturelle rocheuse absorbant la lumière, idéale pour bâtir des avant-postes secrets."
    ],

    // 6. LE GEL POLAIRE CELESTE
    [
        'file' => 'toundra_de_permafrost_bleu.yml',
        'id' => 'opal:toundra_de_permafrost_bleu',
        'name' => "§bToundra de Permafrost Bleu",
        'category' => 'GLACIAIRE',
        'rarity' => 5,
        'climate' => ['temperature' => -0.5, 'humidity' => 0.5, 'downfall' => 0.6],
        'colors' => ['sky_color' => '#b2ebf2', 'fog_color' => '#e0f7fa', 'water_color' => '#4dd0e1', 'grass_color' => '#80deea'],
        'blocks' => ['top_block' => 'snow_block', 'sub_block' => 'packed_ice', 'deep_block' => 'stone'],
        'effects' => ['aura_effect' => 'SLOWNESS', 'particle' => 'minecraft:snowflake', 'gravity' => 1.0],
        'desc' => "Étendue glacée impénétrable où le sol ne dégèle jamais sous les pas des montures."
    ],
    [
        'file' => 'glacier_des_ames_gelees.yml',
        'id' => 'opal:glacier_des_ames_gelees',
        'name' => "§9Glacier des Âmes Gelées",
        'category' => 'GLACIAIRE',
        'rarity' => 6,
        'climate' => ['temperature' => -0.8, 'humidity' => 0.7, 'downfall' => 0.9],
        'colors' => ['sky_color' => '#0277bd', 'fog_color' => '#29b6f6', 'water_color' => '#0288d1', 'grass_color' => '#03a9f4'],
        'blocks' => ['top_block' => 'blue_ice', 'sub_block' => 'frosted_ice', 'deep_block' => 'packed_ice'],
        'effects' => ['aura_effect' => 'RESISTANCE', 'particle' => 'minecraft:blue_flame', 'gravity' => 1.0],
        'desc' => "Cathédrale de glace bleue translucide renfermant des reliques de givre antique."
    ],
    [
        'file' => 'banquise_miroir.yml',
        'id' => 'opal:banquise_miroir',
        'name' => "§fBanquise Miroir",
        'category' => 'GLACIAIRE',
        'rarity' => 5,
        'climate' => ['temperature' => -0.6, 'humidity' => 0.6, 'downfall' => 0.7],
        'colors' => ['sky_color' => '#e1f5fe', 'fog_color' => '#ffffff', 'water_color' => '#b3e5fc', 'grass_color' => '#e0f7fa'],
        'blocks' => ['top_block' => 'ice', 'sub_block' => 'packed_ice', 'deep_block' => 'diorite'],
        'effects' => ['aura_effect' => 'SPEED', 'particle' => 'minecraft:sparkler', 'gravity' => 1.0],
        'desc' => "Surface parfaitement lisse réfléchissant le ciel comme un miroir argenté géant."
    ],
    [
        'file' => 'caverne_des_blizzards.yml',
        'id' => 'opal:caverne_des_blizzards',
        'name' => "§3Caverne des Blizzards",
        'category' => 'GLACIAIRE',
        'rarity' => 7,
        'climate' => ['temperature' => -1.0, 'humidity' => 0.9, 'downfall' => 1.0],
        'colors' => ['sky_color' => '#006064', 'fog_color' => '#00838f', 'water_color' => '#0097a7', 'grass_color' => '#00acc1'],
        'blocks' => ['top_block' => 'packed_ice', 'sub_block' => 'dripstone_block', 'deep_block' => 'deepslate'],
        'effects' => ['aura_effect' => 'BLINDNESS', 'particle' => 'minecraft:snow_dust', 'gravity' => 1.0],
        'desc' => "Grottes balayées par des tourbillons polaires arrachant les torches posées."
    ],
    [
        'file' => 'vallee_des_geysers_de_glace.yml',
        'id' => 'opal:vallee_des_geysers_de_glace',
        'name' => "§bVallée des Geysers de Glace",
        'category' => 'GLACIAIRE',
        'rarity' => 6,
        'climate' => ['temperature' => -0.4, 'humidity' => 0.8, 'downfall' => 0.5],
        'colors' => ['sky_color' => '#4fc3f7', 'fog_color' => '#81d4fa', 'water_color' => '#29b6f6', 'grass_color' => '#039be5'],
        'blocks' => ['top_block' => 'snow', 'sub_block' => 'blue_ice', 'deep_block' => 'calcite'],
        'effects' => ['aura_effect' => 'LEVITATION', 'particle' => 'minecraft:cloud', 'gravity' => 0.8],
        'desc' => "Fissures dans le permafrost propulsant des colonnes d'azote liquide projetant les joueurs en l'air."
    ],

    // 7. DESERT SISMOLOGIQUE & ELECTRIQUE
    [
        'file' => 'desert_de_verre_fulgure.yml',
        'id' => 'opal:desert_de_verre_fulgure',
        'name' => "§6Désert de Verre Fulguré",
        'category' => 'ELECTRIQUE',
        'rarity' => 6,
        'climate' => ['temperature' => 1.9, 'humidity' => 0.05, 'downfall' => 0.0],
        'colors' => ['sky_color' => '#ffd600', 'fog_color' => '#ffee58', 'water_color' => '#fff59d', 'grass_color' => '#ffeb3b'],
        'blocks' => ['top_block' => 'tinted_glass', 'sub_block' => 'sand', 'deep_block' => 'sandstone'],
        'effects' => ['aura_effect' => 'HASTE', 'particle' => 'minecraft:electric_spark', 'gravity' => 1.0],
        'desc' => "Désert vitrifié par des milliers d'impacts de foudre où la charge statique stimule le minage."
    ],
    [
        'file' => 'canyon_des_tempetes_magnetiques.yml',
        'id' => 'opal:canyon_des_tempetes_magnetiques',
        'name' => "§cCanyon des Tempêtes Magnétiques",
        'category' => 'ELECTRIQUE',
        'rarity' => 7,
        'climate' => ['temperature' => 1.6, 'humidity' => 0.1, 'downfall' => 0.1],
        'colors' => ['sky_color' => '#d50000', 'fog_color' => '#ff1744', 'water_color' => '#ff5252', 'grass_color' => '#ff8a80'],
        'blocks' => ['top_block' => 'red_sandstone', 'sub_block' => 'iron_block', 'deep_block' => 'raw_iron_block'],
        'effects' => ['aura_effect' => 'STRENGTH', 'particle' => 'minecraft:lightning_bolt', 'gravity' => 1.0],
        'desc' => "Gorges rocheuses magnétisées où les armes métalliques crépitent d'éclairs de combat."
    ],
    [
        'file' => 'oasis_mirage_temporelle.yml',
        'id' => 'opal:oasis_mirage_temporelle',
        'name' => "§aOasis Mirage Temporelle",
        'category' => 'ELECTRIQUE',
        'rarity' => 8,
        'climate' => ['temperature' => 1.3, 'humidity' => 0.7, 'downfall' => 0.4],
        'colors' => ['sky_color' => '#a7ffeb', 'fog_color' => '#64ffda', 'water_color' => '#1de9b6', 'grass_color' => '#00bfa5'],
        'blocks' => ['top_block' => 'verdant_froglight', 'sub_block' => 'mossy_cobblestone', 'deep_block' => 'sand'],
        'effects' => ['aura_effect' => 'REGENERATION', 'particle' => 'minecraft:happy_villager', 'gravity' => 1.0],
        'desc' => "Refuge d'eau claire et de palmiers célestes qui se déplace à chaque aurore."
    ],
    [
        'file' => 'plateau_des_monolithes.yml',
        'id' => 'opal:plateau_des_monolithes',
        'name' => "§7Plateau des Monolithes",
        'category' => 'ELECTRIQUE',
        'rarity' => 5,
        'climate' => ['temperature' => 1.1, 'humidity' => 0.2, 'downfall' => 0.1],
        'colors' => ['sky_color' => '#546e7a', 'fog_color' => '#78909c', 'water_color' => '#90a4ae', 'grass_color' => '#b0bec5'],
        'blocks' => ['top_block' => 'chiseled_stone_bricks', 'sub_block' => 'stone_bricks', 'deep_block' => 'deepslate'],
        'effects' => ['aura_effect' => 'RESISTANCE', 'particle' => 'minecraft:enchantment_table', 'gravity' => 1.0],
        'desc' => "Piliers de pierre taillée alignés selon des configurations cosmiques guidant les flux d'énergie."
    ],
    [
        'file' => 'vallee_des_sables_mouvants.yml',
        'id' => 'opal:vallee_des_sables_mouvants',
        'name' => "§6Vallée des Sables Mouvants",
        'category' => 'ELECTRIQUE',
        'rarity' => 6,
        'climate' => ['temperature' => 2.0, 'humidity' => 0.0, 'downfall' => 0.0],
        'colors' => ['sky_color' => '#ff6f00', 'fog_color' => '#ff8f00', 'water_color' => '#ffa000', 'grass_color' => '#ffb300'],
        'blocks' => ['top_block' => 'sand', 'sub_block' => 'gravel', 'deep_block' => 'sandstone'],
        'effects' => ['aura_effect' => 'SLOWNESS', 'particle' => 'minecraft:dust', 'gravity' => 1.0],
        'desc' => "Dunes instables aspirant lentement quiconque cesse de se déplacer."
    ],

    // 8. TERRES MYTHIQUES & D'ETHER
    [
        'file' => 'foret_d_or_alchimique.yml',
        'id' => 'opal:foret_d_or_alchimique',
        'name' => "§6Forêt d'Or Alchimique",
        'category' => 'ETHER',
        'rarity' => 7,
        'climate' => ['temperature' => 0.8, 'humidity' => 0.6, 'downfall' => 0.4],
        'colors' => ['sky_color' => '#ffab00', 'fog_color' => '#ffd54f', 'water_color' => '#ffc107', 'grass_color' => '#ffca28'],
        'blocks' => ['top_block' => 'raw_gold_block', 'sub_block' => 'gold_block', 'deep_block' => 'yellow_concrete'],
        'effects' => ['aura_effect' => 'ABSORPTION', 'particle' => 'minecraft:wax_on', 'gravity' => 1.0],
        'desc' => "Végétation d'or liquide où chaque tronc produit de la sève métallique pour la monnaie."
    ],
    [
        'file' => 'archipel_des_iles_flottantes.yml',
        'id' => 'opal:archipel_des_iles_flottantes',
        'name' => "§bArchipel des Îles Flottantes",
        'category' => 'ETHER',
        'rarity' => 8,
        'climate' => ['temperature' => 0.4, 'humidity' => 0.5, 'downfall' => 0.2],
        'colors' => ['sky_color' => '#80d8ff', 'fog_color' => '#b3e5fc', 'water_color' => '#40c4ff', 'grass_color' => '#00b0ff'],
        'blocks' => ['top_block' => 'grass_block', 'sub_block' => 'diorite', 'deep_block' => 'calcite'],
        'effects' => ['aura_effect' => 'SLOW_FALLING', 'particle' => 'minecraft:end_rod', 'gravity' => 0.6],
        'desc' => "Îles de pierre suspendues dans l'azur où les chutes sont ralenties par des courants ascendants."
    ],
    [
        'file' => 'sanctuaire_des_constellations.yml',
        'id' => 'opal:sanctuaire_des_constellations',
        'name' => "§9Sanctuaire des Constellations",
        'category' => 'ETHER',
        'rarity' => 8,
        'climate' => ['temperature' => 0.2, 'humidity' => 0.4, 'downfall' => 0.1],
        'colors' => ['sky_color' => '#1a237e', 'fog_color' => '#283593', 'water_color' => '#303f9f', 'grass_color' => '#3949ab'],
        'blocks' => ['top_block' => 'lapis_block', 'sub_block' => 'deepslate', 'deep_block' => 'bedrock'],
        'effects' => ['aura_effect' => 'CONDUIT_POWER', 'particle' => 'minecraft:portal', 'gravity' => 1.0],
        'desc' => "Observatoire millénaire gravé dans la roche où les constellations doublent les enchantements."
    ],
    [
        'file' => 'jardin_de_nectar_dionysiaque.yml',
        'id' => 'opal:jardin_de_nectar_dionysiaque',
        'name' => "§5Jardin de Nectar Dionysiaque",
        'category' => 'ETHER',
        'rarity' => 7,
        'climate' => ['temperature' => 0.9, 'humidity' => 0.8, 'downfall' => 0.5],
        'colors' => ['sky_color' => '#880e4f', 'fog_color' => '#c2185b', 'water_color' => '#e91e63', 'grass_color' => '#ad1457'],
        'blocks' => ['top_block' => 'flowering_azalea', 'sub_block' => 'rooted_dirt', 'deep_block' => 'moss_block'],
        'effects' => ['aura_effect' => 'HASTE', 'particle' => 'minecraft:heart', 'gravity' => 1.0],
        'desc' => "Vignes divines produisant des nectars d'exaltation guerrière et d'inspiration alchimique."
    ],
    [
        'file' => 'nexus_d_opale_primordiale.yml',
        'id' => 'opal:nexus_d_opale_primordiale',
        'name' => "§l§dLE NEXUS D'OPALE PRIMORDIALE",
        'category' => 'APEX',
        'rarity' => 10,
        'climate' => ['temperature' => 0.77, 'humidity' => 0.77, 'downfall' => 0.77],
        'colors' => ['sky_color' => '#e040fb', 'fog_color' => '#ea80fc', 'water_color' => '#d500f9', 'grass_color' => '#aa00ff'],
        'blocks' => ['top_block' => 'amethyst_block', 'sub_block' => 'diamond_block', 'deep_block' => 'netherite_block'],
        'effects' => ['aura_effect' => 'HERO_OF_THE_VILLAGE', 'particle' => 'minecraft:totem_particle', 'gravity' => 1.0],
        'desc' => "LE SAINT DES SAINTS : Le biome mythique absolu d'Opal Heaven (0.1% de chance). La source de l'Opale sacrée, conférant une invulnérabilité partielle et des filons de richesses inestimables."
    ]
];

echo "Generation des 40 fichiers de configuration de biomes...\n";

foreach ($biomes as $b) {
    $yaml = "# Configuration de Biome Modde - OPAL HEAVEN\n";
    $yaml .= "# ID unique et Enregistrement dans le Moteur de Monde\n";
    $yaml .= "id: \"{$b['id']}\"\n";
    $yaml .= "name: \"{$b['name']}\"\n";
    $yaml .= "category: \"{$b['category']}\"\n";
    $yaml .= "rarity: {$b['rarity']}\n";
    $yaml .= "description: \"{$b['desc']}\"\n\n";

    $yaml .= "climate:\n";
    $yaml .= "  temperature: {$b['climate']['temperature']}\n";
    $yaml .= "  humidity: {$b['climate']['humidity']}\n";
    $yaml .= "  downfall: {$b['climate']['downfall']}\n\n";

    $yaml .= "colors:\n";
    $yaml .= "  sky_color: \"{$b['colors']['sky_color']}\"\n";
    $yaml .= "  fog_color: \"{$b['colors']['fog_color']}\"\n";
    $yaml .= "  water_color: \"{$b['colors']['water_color']}\"\n";
    $yaml .= "  grass_color: \"{$b['colors']['grass_color']}\"\n\n";

    $yaml .= "blocks:\n";
    $yaml .= "  top_block: \"{$b['blocks']['top_block']}\"\n";
    $yaml .= "  sub_block: \"{$b['blocks']['sub_block']}\"\n";
    $yaml .= "  deep_block: \"{$b['blocks']['deep_block']}\"\n\n";

    $yaml .= "effects:\n";
    $yaml .= "  aura_effect: \"{$b['effects']['aura_effect']}\"\n";
    $yaml .= "  particle: \"{$b['effects']['particle']}\"\n";
    $yaml .= "  gravity: {$b['effects']['gravity']}\n\n";

    $yaml .= "ores:\n";
    $yaml .= "  - ore: \"opal:opal_ore\"\n";
    $yaml .= "    chance_per_chunk: " . ($b['rarity'] >= 8 ? 6 : 2) . "\n";
    $yaml .= "    min_y: 5\n";
    $yaml .= "    max_y: 45\n";
    $yaml .= "  - ore: \"diamond_ore\"\n";
    $yaml .= "    chance_per_chunk: 8\n";
    $yaml .= "    min_y: -60\n";
    $yaml .= "    max_y: 16\n";

    file_put_contents($biomesDir . '/' . $b['file'], $yaml);
    echo "  [OK] " . $b['file'] . " -> " . $b['name'] . "\n";
}

echo "\n40 BIOMES GENERES AVEC SUCCES DANS $biomesDir !\n";
