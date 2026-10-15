<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\cache;

use pocketmine\color\Color;
use pocketmine\data\bedrock\BedrockDataFiles;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\AvailableActorIdentifiersPacket;
use pocketmine\network\mcpe\protocol\BiomeDefinitionListPacket;
use pocketmine\network\mcpe\protocol\FeatureRegistryPacket;
use pocketmine\network\mcpe\protocol\JigsawStructureDataPacket;
use pocketmine\network\mcpe\protocol\VoxelShapesPacket;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer;
use pocketmine\network\mcpe\protocol\types\biome\BiomeDefinitionEntry;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\utils\Filesystem;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\Utils;
use pocketmine\world\biome\model\BiomeDefinitionEntryData;
use Symfony\Component\Filesystem\Path;
use const pocketmine\RESOURCE_PATH;
use function count;
use function file_exists;
use function get_debug_type;
use function gzdecode;
use function is_array;
use function json_decode;

class StaticPacketCache{
	use SingletonTrait;

	/**
	 * @phpstan-return CacheableNbt<\pocketmine\nbt\tag\CompoundTag>
	 */
	private static function loadCompoundFromFile(string $filePath) : CacheableNbt{
		return new CacheableNbt((new NetworkNbtSerializer())->read(Filesystem::fileGetContents($filePath))->mustGetCompoundTag());
	}

	/**
	 * @return list<BiomeDefinitionEntry>
	 */
	private static function loadBiomeDefinitionModel(string $filePath) : array{
		$biomeEntries = json_decode(Filesystem::fileGetContents($filePath), associative: true);
		if(!is_array($biomeEntries)){
			throw new SavedDataLoadingException("$filePath root should be an array, got " . get_debug_type($biomeEntries));
		}

		$jsonMapper = new \JsonMapper();
		$jsonMapper->bExceptionOnMissingData = true;
		$jsonMapper->bStrictObjectTypeChecking = true;
		$jsonMapper->bEnforceMapType = false;

		$entries = [];
		foreach(Utils::promoteKeys($biomeEntries) as $biomeName => $entry){
			if(!is_array($entry)){
				throw new SavedDataLoadingException("$filePath should be an array of objects, got " . get_debug_type($entry));
			}

			try{
				$biomeDefinition = $jsonMapper->map($entry, new BiomeDefinitionEntryData());

				$mapWaterColour = $biomeDefinition->mapWaterColour;
				$entries[] = new BiomeDefinitionEntry(
					(string) $biomeName,
					$biomeDefinition->id,
					$biomeDefinition->temperature,
					$biomeDefinition->downfall,
					$biomeDefinition->foliageSnow,
					$biomeDefinition->depth,
					$biomeDefinition->scale,
					new Color(
						$mapWaterColour->r,
						$mapWaterColour->g,
						$mapWaterColour->b,
						$mapWaterColour->a
					),
					$biomeDefinition->rain,
					count($biomeDefinition->tags) > 0 ? $biomeDefinition->tags : null,
				);
			}catch(\JsonMapper_Exception $e){
				throw new \RuntimeException($e->getMessage(), 0, $e);
			}
		}

		return $entries;
	}

	private static function make() : self{
		$emptyJigsawNbt = CompoundTag::create()
			->setTag("processors", new ListTag([], NBT::TAG_Compound))
			->setTag("template_pools", new ListTag([], NBT::TAG_Compound))
			->setTag("jigsaws", new ListTag([], NBT::TAG_Compound))
			->setTag("structure_sets", new ListTag([], NBT::TAG_Compound));
		$jigsawPacket = JigsawStructureDataPacket::create(new CacheableNbt($emptyJigsawNbt));

		return new self(
			BiomeDefinitionListPacket::fromDefinitions(self::loadBiomeDefinitionModel(BedrockDataFiles::BIOME_DEFINITIONS_JSON)),
			AvailableActorIdentifiersPacket::create(self::loadCompoundFromFile(BedrockDataFiles::ENTITY_IDENTIFIERS_NBT)),
			FeatureRegistryPacket::create([]),
			$jigsawPacket,
			VoxelShapesPacket::create([], [], 0)
		);
	}

	public function __construct(
		private BiomeDefinitionListPacket $biomeDefs,
		private AvailableActorIdentifiersPacket $availableActorIdentifiers,
		private FeatureRegistryPacket $featureRegistry,
		private JigsawStructureDataPacket $jigsawStructureData,
		private VoxelShapesPacket $voxelShapes
	){}

	public function getBiomeDefs() : BiomeDefinitionListPacket{
		return $this->biomeDefs;
	}

	public function getAvailableActorIdentifiers() : AvailableActorIdentifiersPacket{
		return $this->availableActorIdentifiers;
	}

	public function getFeatureRegistry() : FeatureRegistryPacket{
		return $this->featureRegistry;
	}

	public function getJigsawStructureData() : JigsawStructureDataPacket{
		return $this->jigsawStructureData;
	}

	public function getVoxelShapes() : VoxelShapesPacket{
		return $this->voxelShapes;
	}

	/** @var BlockPaletteEntry[]|null */
	private ?array $blockPalette = null;

	/**
	 * @return BlockPaletteEntry[]
	 * @phpstan-return list<BlockPaletteEntry>
	 */
	public function getBlockPalette() : array{
		if($this->blockPalette === null){
			$tc = TypeConverter::getInstance();
			$states = $tc->getBlockTranslator()->getBlockStateDictionary()->getStates();

			$this->blockPalette = [];
			foreach($states as $state){
				$stateData = $state->generateStateData();
				$statesTag = CompoundTag::create();
				foreach($stateData->getStates() as $k => $v){
					$statesTag->setTag($k, $v);
				}
				$this->blockPalette[] = new BlockPaletteEntry($stateData->getName(), new CacheableNbt($statesTag));
			}
		}
		return $this->blockPalette;
	}
}
