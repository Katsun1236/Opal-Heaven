<?php

/*
 * This file is part of BedrockProtocol.
 * Copyright (C) 2014-2022 PocketMine Team <https://github.com/pmmp/BedrockProtocol>
 *
 * BedrockProtocol is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use pmmp\encoding\Byte;
use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\LE;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use function count;
use function in_array;

class ResourcePackClientResponsePacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_CLIENT_RESPONSE_PACKET;

	public const STATUS_REFUSED = 1;
	public const STATUS_SEND_PACKS = 2;
	public const STATUS_HAVE_ALL_PACKS = 3;
	public const STATUS_COMPLETED = 4;

	public int $status;
	/** @var string[] */
	public array $packIds = [];

	/**
	 * @generate-create-func
	 * @param string[] $packIds
	 */
	public static function create(int $status, array $packIds) : self{
		$result = new self;
		$result->status = $status;
		$result->packIds = $packIds;
		return $result;
	}

	protected function decodePayload(ByteBufferReader $in) : void{
		$offset = $in->getOffset();
		$rawStatus = VarInt::readUnsignedInt($in);
		$this->packIds = [];

		$isNewFormat = false;
		if($in->getUnreadLength() > 0){
			try{
				$str = CommonTypes::getString($in);
				if(in_array($str, ["cancel", "downloading", "downloadingfinished", "resourcepackstackfinished"], true)){
					$isNewFormat = true;
					$this->status = match($rawStatus){
						0 => self::STATUS_REFUSED,
						1 => self::STATUS_SEND_PACKS,
						2 => self::STATUS_HAVE_ALL_PACKS,
						3 => self::STATUS_COMPLETED,
						default => self::STATUS_COMPLETED
					};

					if($rawStatus === 1 && $in->getUnreadLength() > 0){
						$entryCount = VarInt::readUnsignedInt($in);
						while($entryCount-- > 0 && $in->getUnreadLength() > 0){
							$this->packIds[] = CommonTypes::getString($in);
						}
					}
				}
			}catch(\Throwable $e){
				$isNewFormat = false;
			}
		}

		if(!$isNewFormat){
			$in->setOffset($offset);
			$this->status = Byte::readUnsigned($in);
			if($in->getUnreadLength() > 0){
				$entryCount = LE::readUnsignedShort($in);
				while($entryCount-- > 0 && $in->getUnreadLength() > 0){
					$this->packIds[] = CommonTypes::getString($in);
				}
			}
		}
	}

	protected function encodePayload(ByteBufferWriter $out) : void{
		VarInt::writeUnsignedInt($out, match($this->status){
			self::STATUS_REFUSED => 0,
			self::STATUS_SEND_PACKS => 1,
			self::STATUS_HAVE_ALL_PACKS => 2,
			self::STATUS_COMPLETED => 3,
			default => 3
		});
		$name = match($this->status){
			self::STATUS_REFUSED => "cancel",
			self::STATUS_SEND_PACKS => "downloading",
			self::STATUS_HAVE_ALL_PACKS => "downloadingfinished",
			self::STATUS_COMPLETED => "resourcepackstackfinished",
			default => "resourcepackstackfinished"
		};
		CommonTypes::putString($out, $name);
		if($this->status === self::STATUS_SEND_PACKS){
			VarInt::writeUnsignedInt($out, count($this->packIds));
			foreach($this->packIds as $id){
				CommonTypes::putString($out, $id);
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleResourcePackClientResponse($this);
	}
}
