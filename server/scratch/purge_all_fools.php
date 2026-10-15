<?php

require 'pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer;
use pocketmine\nbt\TreePrinter;

echo "=== DÉBUT DU NETTOYAGE CHIRURGICAL DES RESTES DU CANULAR CHAOS CUBED ===\n";

$baseDir = 'pmmp_src/vendor/pocketmine/bedrock-data';

function backupAndSave(string $filePath, string $newContent): void {
    $bak = $filePath . '.bak_fools';
    if (!file_exists($bak)) {
        copy($filePath, $bak);
        echo "[BACKUP] Sauvegardé $bak\n";
    }
    file_put_contents($filePath, $newContent);
    echo "[MIS À JOUR] $filePath\n";
}

// 1. entity_identifiers.nbt
$entFile = "$baseDir/entity_identifiers.nbt";
$serializer = new NetworkNbtSerializer();
$tag = $serializer->read(file_get_contents($entFile))->mustGetCompoundTag();
$idList = $tag->getListTag("idlist");
$newList = new \pocketmine\nbt\tag\ListTag();
$removedEnts = 0;
foreach ($idList as $compound) {
    $id = $compound->getString("id");
    if (str_contains($id, "sulfur") || str_contains($id, "cinnabar")) {
        $removedEnts++;
        echo " - Retiré entité: $id\n";
        continue;
    }
    $newList->push($compound);
}
$tag->setTag("idlist", $newList);
$newEntContent = $serializer->write(new \pocketmine\nbt\TreeRoot($tag));
backupAndSave($entFile, $newEntContent);
echo "Entités retirées: $removedEnts\n";

// 2. entity_id_map.json
$eidFile = "$baseDir/entity_id_map.json";
$eids = json_decode(file_get_contents($eidFile), true);
$before = count($eids);
foreach ($eids as $k => $v) {
    if (str_contains($k, "sulfur") || str_contains($k, "cinnabar")) {
        unset($eids[$k]);
        echo " - Retiré de entity_id_map: $k\n";
    }
}
backupAndSave($eidFile, json_encode($eids, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "entity_id_map: $before -> " . count($eids) . "\n";

// 3. biome_definitions.json
$biomeDefFile = "$baseDir/biome_definitions.json";
$biomeDefs = json_decode(file_get_contents($biomeDefFile), true);
$before = count($biomeDefs);
foreach ($biomeDefs as $k => $v) {
    if (str_contains($k, "sulfur") || str_contains($k, "cinnabar")) {
        unset($biomeDefs[$k]);
        echo " - Retiré de biome_definitions: $k\n";
    }
}
backupAndSave($biomeDefFile, json_encode($biomeDefs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "biome_definitions: $before -> " . count($biomeDefs) . "\n";

// 4. biome_id_map.json
$biomeMapFile = "$baseDir/biome_id_map.json";
$biomeMaps = json_decode(file_get_contents($biomeMapFile), true);
$before = count($biomeMaps);
foreach ($biomeMaps as $k => $v) {
    if (str_contains($k, "sulfur") || str_contains($k, "cinnabar")) {
        unset($biomeMaps[$k]);
        echo " - Retiré de biome_id_map: $k\n";
    }
}
backupAndSave($biomeMapFile, json_encode($biomeMaps, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "biome_id_map: $before -> " . count($biomeMaps) . "\n";

// 5. required_item_list.json
$reqItemFile = "$baseDir/required_item_list.json";
$reqItems = json_decode(file_get_contents($reqItemFile), true);
$before = count($reqItems);
foreach ($reqItems as $k => $v) {
    if (str_contains($k, "sulfur") || str_contains($k, "cinnabar")) {
        unset($reqItems[$k]);
        echo " - Retiré de required_item_list: $k\n";
    }
}
backupAndSave($reqItemFile, json_encode($reqItems, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "required_item_list: $before -> " . count($reqItems) . "\n";

// 6. block_id_to_item_id_map.json
$bidFile = "$baseDir/block_id_to_item_id_map.json";
$bids = json_decode(file_get_contents($bidFile), true);
$before = count($bids);
foreach ($bids as $k => $v) {
    if (str_contains($k, "sulfur") || str_contains($k, "cinnabar")) {
        unset($bids[$k]);
    }
}
backupAndSave($bidFile, json_encode($bids, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "block_id_to_item_id_map: $before -> " . count($bids) . "\n";

// 7. block_properties_table.json
$bpropFile = "$baseDir/block_properties_table.json";
$bprops = json_decode(file_get_contents($bpropFile), true);
$before = count($bprops);
foreach ($bprops as $k => $v) {
    if (str_contains($k, "sulfur") || str_contains($k, "cinnabar")) {
        unset($bprops[$k]);
    }
}
backupAndSave($bpropFile, json_encode($bprops, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "block_properties_table: $before -> " . count($bprops) . "\n";

// 8. item_tags.json
$tagFile = "$baseDir/item_tags.json";
$itemTags = json_decode(file_get_contents($tagFile), true);
foreach ($itemTags as $tagKey => &$tagList) {
    if (is_array($tagList)) {
        $tagList = array_values(array_filter($tagList, fn($item) => !str_contains($item, "sulfur") && !str_contains($item, "cinnabar")));
    }
}
unset($tagList);
backupAndSave($tagFile, json_encode($itemTags, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "item_tags mis à jour\n";

// 9. creative folders
foreach (['construction.json', 'items.json', 'nature.json'] as $cFile) {
    $cPath = "$baseDir/creative/$cFile";
    if (file_exists($cPath)) {
        $cData = json_decode(file_get_contents($cPath), true);
        $before = count($cData);
        $cData = array_values(array_filter($cData, function($item) {
            $str = json_encode($item);
            return !str_contains($str, "sulfur") && !str_contains($str, "cinnabar");
        }));
        backupAndSave($cPath, json_encode($cData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "creative/$cFile: $before -> " . count($cData) . "\n";
    }
}

// 10. recipes/shaped_crafting.json and shapeless_crafting.json
foreach (['shaped_crafting.json', 'shapeless_crafting.json'] as $rFile) {
    $rPath = "$baseDir/recipes/$rFile";
    if (file_exists($rPath)) {
        $rData = json_decode(file_get_contents($rPath), true);
        $before = count($rData);
        $rData = array_values(array_filter($rData, function($recipe) {
            $str = json_encode($recipe);
            return !str_contains($str, "sulfur") && !str_contains($str, "cinnabar");
        }));
        backupAndSave($rPath, json_encode($rData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "recipes/$rFile: $before -> " . count($rData) . "\n";
    }
}

echo "=== TOUS LES FICHIERS ONT ÉTÉ PURGÉS AVEC SUCCÈS ===\n";
