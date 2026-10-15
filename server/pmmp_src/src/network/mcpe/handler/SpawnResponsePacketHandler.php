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

namespace pocketmine\network\mcpe\handler;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ClientCacheStatusPacket;
use pocketmine\network\mcpe\protocol\DisconnectPacket;
use pocketmine\network\mcpe\protocol\EmoteListPacket;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\PacketViolationWarningPacket;
use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;
use pocketmine\network\mcpe\protocol\ServerboundLoadingScreenPacket;
use pocketmine\network\mcpe\protocol\SetLocalPlayerAsInitializedPacket;
use pocketmine\network\mcpe\protocol\StructureTemplateDataRequestPacket;
use pocketmine\network\mcpe\protocol\StructureTemplateDataResponsePacket;

#[SilentDiscard(EmoteListPacket::class, comment: "Probably not needed?")]
#[SilentDiscard(InteractPacket::class, comment: "Player interacting with itself somehow")]
#[SilentDiscard(MobEquipmentPacket::class, comment: "Player equipping its held item on spawn, not needed")]
#[SilentDiscard(PlayerAuthInputPacket::class, comment: "Spammed after StartGame even though player has no controls")]
#[SilentDiscard(RequestChunkRadiusPacket::class, comment: "Radius request may arrive during spawn response")]
#[SilentDiscard(ClientCacheStatusPacket::class, comment: "Client cache status")]
final class SpawnResponsePacketHandler extends PacketHandler{
	private ?NetworkSession $session = null;
	private \Closure $responseCallback;

	/**
	 * @phpstan-param NetworkSession|(\Closure() : void) $sessionOrCallback
	 * @phpstan-param (\Closure() : void)|null $responseCallback
	 */
	public function __construct(
		NetworkSession|\Closure $sessionOrCallback,
		?\Closure $responseCallback = null
	){
		if($sessionOrCallback instanceof \Closure){
			$this->responseCallback = $sessionOrCallback;
			$this->session = null;
		}else{
			$this->session = $sessionOrCallback;
			if($responseCallback === null){
				throw new \InvalidArgumentException("Response callback must be provided when session is passed");
			}
			$this->responseCallback = $responseCallback;
		}
	}

	public function handleSetLocalPlayerAsInitialized(SetLocalPlayerAsInitializedPacket $packet) : bool{
		($this->responseCallback)();
		return true;
	}

	public function handlePacketViolationWarning(PacketViolationWarningPacket $packet) : bool{
		\GlobalLogger::get()->error(">> [CLIENT-VIOLATION] Type: {$packet->getType()} | Severity: {$packet->getSeverity()} | PacketId: 0x" . dechex($packet->getPacketId()) . " | Message: {$packet->getMessage()}");
		return true;
	}

	public function handleServerboundLoadingScreen(ServerboundLoadingScreenPacket $packet) : bool{
		\GlobalLogger::get()->notice("[SPAWN-RESPONSE] [LOADING SCREEN] << Type: " . $packet->getLoadingScreenType()->name);
		return true;
	}

	public function handleDisconnect(DisconnectPacket $packet) : bool{
		\GlobalLogger::get()->error(">> [CLIENT-DISCONNECT-PACKET] Raison: {$packet->reason} | Message: " . ($packet->message ?? "N/A") . " | FilteredMessage: " . ($packet->filteredMessage ?? "N/A"));
		return true;
	}
}
