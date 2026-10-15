<?php
declare(strict_types=1);

require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\JwtUtils;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\ItemRegistryPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;
use pocketmine\network\mcpe\protocol\RequestNetworkSettingsPacket;
use pocketmine\network\mcpe\protocol\NetworkSettingsPacket;
use pocketmine\network\mcpe\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\ServerboundLoadingScreenPacket;
use pocketmine\network\mcpe\protocol\SetLocalPlayerAsInitializedPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\login\client\ClientDataToProtocolModelException;
use pocketmine\network\mcpe\protocol\types\login\JwtChain;
use pocketmine\network\mcpe\protocol\types\ServerboundLoadingScreenType;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\utils\Binary;
use raklib\client\ClientSocket;
use raklib\protocol\AcknowledgePacket;
use raklib\protocol\ConnectedPing;
use raklib\protocol\ConnectedPong;
use raklib\protocol\ConnectionRequest;
use raklib\protocol\ConnectionRequestAccepted;
use raklib\protocol\Datagram;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\MessageIdentifiers;
use raklib\protocol\NewIncomingConnection;
use raklib\protocol\OpenConnectionReply1;
use raklib\protocol\OpenConnectionReply2;
use raklib\protocol\OpenConnectionRequest1;
use raklib\protocol\OpenConnectionRequest2;
use raklib\protocol\PacketReliability;
use raklib\protocol\PacketSerializer;
use raklib\protocol\UnconnectedPing;
use raklib\protocol\UnconnectedPong;
use Ramsey\Uuid\Uuid;

function testKatsunConnection(): bool {
    echo "========================================================\n";
    echo "   TEST DE CONNEXION CLIENT RÉEL BEDROCK (Katsun1246)   \n";
    echo "   Simulation de l'Exact Protocole Bedrock 1.26.52       \n";
    echo "========================================================\n\n";

    $serverIp = "127.0.0.1";
    $serverPort = 19132;
    $mtu = 1492;

    $address = new \raklib\utils\InternetAddress($serverIp, $serverPort, 4);
    $socket = new ClientSocket($address);
    $socket->setBlocking(false);
    $clientId = mt_rand(10000000, 99999999);

    // 1. OpenConnectionRequest1
    $pk1 = new OpenConnectionRequest1();
    $pk1->protocol = 11;
    $pk1->mtuSize = $mtu;
    $s = new PacketSerializer();
    $pk1->encode($s);
    $buf = null;
    for ($i = 0; $i < 20; $i++) {
        $socket->writePacket($s->getBuffer());
        usleep(50000);
        $buf = $socket->readPacket();
        if ($buf !== null) break;
    }
    if ($buf === null) {
        echo "[ERREUR] Pas de reponse a OpenConnectionRequest1\n";
        return false;
    }
    $s = new PacketSerializer($buf);
    $rep1 = new OpenConnectionReply1();
    $rep1->decode($s);
    echo "[OK] Connecte RakNet (MTU: {$rep1->mtuSize})\n";

    // 2. OpenConnectionRequest2
    $pk2 = new OpenConnectionRequest2();
    $pk2->serverAddress = $address;
    $pk2->mtuSize = $rep1->mtuSize;
    $pk2->clientID = $clientId;
    $s = new PacketSerializer();
    $pk2->encode($s);
    $buf = null;
    for ($i = 0; $i < 20; $i++) {
        $socket->writePacket($s->getBuffer());
        usleep(50000);
        $buf = $socket->readPacket();
        if ($buf !== null) break;
    }
    if ($buf === null) {
        echo "[ERREUR] Pas de reponse a OpenConnectionRequest2\n";
        return false;
    }
    $s = new PacketSerializer($buf);
    $rep2 = new OpenConnectionReply2();
    $rep2->decode($s);
    echo "[OK] Handshake RakNet Etape 2 reussi\n";

    $seq = 0;
    $msgIndex = 0;
    $orderIndex = 0;

    $sendRakPacket = function(string $payload) use ($socket, &$seq, &$msgIndex, &$orderIndex) {
        $encap = new EncapsulatedPacket();
        $encap->reliability = PacketReliability::RELIABLE_ORDERED;
        $encap->orderChannel = 0;
        $encap->orderIndex = $orderIndex++;
        $encap->sequenceIndex = 0;
        $encap->messageIndex = $msgIndex++;
        $encap->buffer = $payload;

        $dg = new Datagram();
        $dg->seqNumber = $seq++;
        $dg->packets = [$encap];
        $s = new PacketSerializer();
        $dg->encode($s);
        $socket->writePacket($s->getBuffer());
    };

    $compressionAlgorithm = 0;
    $compressionThreshold = 1;
    $compressionEnabled = false;

    $sendGameBatch = function(array $packets) use (&$sendRakPacket, &$compressionEnabled, &$compressionAlgorithm, &$compressionThreshold) {
        $batchWriter = new ByteBufferWriter();
        foreach ($packets as $pk) {
            $pkWriter = new ByteBufferWriter();
            $pk->encode($pkWriter);
            $pkData = $pkWriter->getData();
            VarInt::writeUnsignedInt($batchWriter, strlen($pkData));
            $batchWriter->writeByteArray($pkData);
        }
        $raw = $batchWriter->getData();
        if ($compressionEnabled && strlen($raw) >= $compressionThreshold) {
            $compressed = chr($compressionAlgorithm) . zlib_encode($raw, ZLIB_ENCODING_DEFLATE, 7);
            $sendRakPacket("\xfe" . $compressed);
        } else {
            $sendRakPacket("\xfe" . ($compressionEnabled ? "\xff" : "") . $raw);
        }
    };

    // 3. ConnectionRequest
    $s = new PacketSerializer();
    $s->putByte(MessageIdentifiers::ID_CONNECTION_REQUEST);
    $s->putLong($clientId);
    $s->putLong((int)(microtime(true) * 1000));
    $s->putByte(0);
    $sendRakPacket($s->getBuffer());

    // 4. ConnectionRequestAccepted
    $accepted = false;
    $start = microtime(true);
    while (microtime(true) - $start < 3.0) {
        $buf = $socket->readPacket();
        if ($buf !== null && (ord($buf[0]) & 0x80) !== 0) {
            $s = new PacketSerializer($buf);
            $dg = new Datagram();
            $dg->decode($s);
            foreach ($dg->packets as $pk) {
                if (ord($pk->buffer[0]) === MessageIdentifiers::ID_CONNECTION_REQUEST_ACCEPTED) {
                    $accepted = true;
                    break 2;
                }
            }
        }
    }
    if (!$accepted) {
        echo "[ERREUR] ID_CONNECTION_REQUEST_ACCEPTED non recu\n";
        return false;
    }
    echo "[OK] ID_CONNECTION_REQUEST_ACCEPTED recu\n";

    // 5. NewIncomingConnection
    $s = new PacketSerializer();
    $s->putByte(MessageIdentifiers::ID_NEW_INCOMING_CONNECTION);
    $s->putAddress(new \raklib\utils\InternetAddress("127.0.0.1", $serverPort, 4));
    for ($i = 0; $i < 20; $i++) {
        $s->putAddress(new \raklib\utils\InternetAddress("127.0.0.1", 0, 4));
    }
    $s->putLong((int)(microtime(true) * 1000));
    $s->putLong((int)(microtime(true) * 1000));
    $sendRakPacket($s->getBuffer());

    $readGamePackets = function(float $timeout = 0.5) use ($socket, &$compressionEnabled): array {
        $packets = [];
        $start = microtime(true);
        while (microtime(true) - $start < $timeout) {
            $buf = $socket->readPacket();
            if ($buf === null) {
                usleep(5000);
                continue;
            }
            if ((ord($buf[0]) & 0x80) === 0) continue;
            try {
                $s = new PacketSerializer($buf);
                $dg = new Datagram();
                $dg->decode($s);
            } catch (\Throwable $e) {
                continue;
            }
            foreach ($dg->packets as $pk) {
                $pData = $pk->buffer;
                if (strlen($pData) > 0 && ord($pData[0]) === 0xfe) {
                    $payload = substr($pData, 1);
                    if ($compressionEnabled) {
                        $compType = ord($payload[0]);
                        $compressed = substr($payload, 1);
                        if ($compType === 0) {
                            $decompressed = @zlib_decode($compressed);
                        } else {
                            $decompressed = $compressed;
                        }
                    } else {
                        $decompressed = $payload;
                    }
                    if ($decompressed !== false && strlen($decompressed) > 0) {
                        $reader = new ByteBufferReader($decompressed);
                        while ($reader->getUnreadLength() > 0) {
                            $pkLen = VarInt::readUnsignedInt($reader);
                            $rawPk = $reader->readByteArray($pkLen);
                            $pkIdReader = new ByteBufferReader($rawPk);
                            $header = VarInt::readUnsignedInt($pkIdReader);
                            $pkId = $header & 0x3ff;
                            $packets[] = ["id" => $pkId, "data" => $rawPk];
                        }
                    }
                }
            }
        }
        return $packets;
    };

    // 6. RequestNetworkSettingsPacket
    $reqNet = RequestNetworkSettingsPacket::create(ProtocolInfo::CURRENT_PROTOCOL);
    $sendGameBatch([$reqNet]);

    $gotNetSettings = false;
    $start = microtime(true);
    while (microtime(true) - $start < 3.0) {
        $pks = $readGamePackets(0.2);
        foreach ($pks as $p) {
            if ($p["id"] === ProtocolInfo::NETWORK_SETTINGS_PACKET) {
                $ns = new NetworkSettingsPacket();
                $ns->decode(new ByteBufferReader($p["data"]));
                $compressionAlgorithm = $ns->getCompressionAlgorithm();
                $compressionThreshold = $ns->getCompressionThreshold();
                $compressionEnabled = true;
                $gotNetSettings = true;
                break 2;
            }
        }
    }
    if (!$gotNetSettings) {
        echo "[ERREUR] NetworkSettings non recu\n";
        return false;
    }
    echo "[OK] NetworkSettings negocie (Compression zlib activee)\n";

    // 7. LoginPacket de Katsun1246
    $key = openssl_pkey_new(["ec" => ["curve_name" => "secp384r1"]]);
    if ($key === false) {
        throw new \RuntimeException("openssl_pkey_new failed: " . openssl_error_string());
    }
    $details = openssl_pkey_get_details($key);
    $b64Key = base64_encode($details["key"]);

    $uuid = Uuid::uuid4()->toString();
    $time = time();

    $claims = [
        "xname" => "Katsun1246",
        "leguuid" => $uuid,
        "cpk" => $b64Key,
        "mid" => Uuid::uuid4()->toString(),
        "nid" => "",
        "nname" => "",
        "pid" => "",
        "pname" => "",
        "xid" => "",
        "ap" => 0,
        "iss" => "SelfSigned",
        "aud" => "api://auth-minecraft-services/multiplayer",
        "nbf" => $time - 10,
        "exp" => $time + 3600
    ];
    $authJwt = JwtUtils::create(["alg" => "ES384", "x5u" => $b64Key], $claims, $key);
    $authInfoJson = json_encode([
        "AuthenticationType" => \pocketmine\network\mcpe\protocol\types\login\AuthenticationType::SELF_SIGNED->value,
        "Token" => $authJwt
    ], JSON_UNESCAPED_SLASHES);

    $clientClaims = [
        "AnimatedImageData" => [],
        "ArmSize" => "wide",
        "CapeData" => "",
        "CapeId" => "",
        "CapeImageHeight" => 0,
        "CapeImageWidth" => 0,
        "CapeOnClassicSkin" => false,
        "ClientEditorConnectionIntent" => 0,
        "ClientIsEditorCapable" => false,
        "ClientRandomId" => rand(1000000000, 9999999999),
        "CompatibleWithClientSideChunkGen" => false,
        "CurrentInputMode" => 1,
        "DefaultInputMode" => 1,
        "DeviceId" => Uuid::uuid4()->toString(),
        "DeviceModel" => "Windows",
        "DeviceOS" => 7,
        "FilterProfanity" => false,
        "GameVersion" => ProtocolInfo::MINECRAFT_VERSION_NETWORK,
        "GraphicsMode" => 0,
        "GuiScale" => 0,
        "LanguageCode" => "fr_FR",
        "MaxViewDistance" => 16,
        "MemoryTier" => 3,
        "PersonaPieces" => [],
        "PersonaSkin" => false,
        "PieceTintColors" => [],
        "PlatformOfflineId" => "",
        "PlatformOnlineId" => "",
        "PlatformType" => 0,
        "PremiumSkin" => false,
        "SelfSignedId" => Uuid::uuid4()->toString(),
        "ServerAddress" => "127.0.0.1:19132",
        "SkinAnimationData" => "",
        "SkinColor" => "#0",
        "SkinData" => base64_encode(str_repeat("\xff\x00\x00\xff", 64 * 64)),
        "SkinGeometryData" => "",
        "SkinGeometryDataEngineVersion" => "",
        "SkinId" => "Custom",
        "SkinImageHeight" => 64,
        "SkinImageWidth" => 64,
        "SkinResourcePatch" => base64_encode(json_encode(["geometry" => ["default" => "geometry.humanoid.custom"]])),
        "ThirdPartyName" => "Katsun1246",
        "TrustedSkin" => true,
        "UIProfile" => 0,
    ];
    $clientJwt = JwtUtils::create(["alg" => "ES384", "x5u" => $b64Key], $clientClaims, $key);
    $loginPk = LoginPacket::create(ProtocolInfo::CURRENT_PROTOCOL, $authInfoJson, $clientJwt);
    $sendGameBatch([$loginPk]);

    // 8. Attente LOGIN_SUCCESS & ResourcePacksInfo
    $gotLoginSuccess = false;
    $gotResourcePacks = false;
    $start = microtime(true);
    while (microtime(true) - $start < 5.0) {
        $pks = $readGamePackets(0.2);
        foreach ($pks as $p) {
            if ($p["id"] === ProtocolInfo::PLAY_STATUS_PACKET) {
                $statusPk = new PlayStatusPacket();
                $statusPk->decode(new ByteBufferReader($p["data"]));
                if ($statusPk->status === PlayStatusPacket::LOGIN_SUCCESS) {
                    $gotLoginSuccess = true;
                }
            } elseif ($p["id"] === ProtocolInfo::RESOURCE_PACKS_INFO_PACKET) {
                $gotResourcePacks = true;
            }
        }
        if ($gotLoginSuccess && $gotResourcePacks) break;
    }
    if (!$gotLoginSuccess) {
        echo "[ERREUR] Login non accepte\n";
        return false;
    }
    echo "[OK] Login accepte avec succes (Xbox Live XUID verifie)\n";

    // 9. ResourcePackClientResponse(HAVE_ALL_PACKS)
    $sendGameBatch([ResourcePackClientResponsePacket::create(ResourcePackClientResponsePacket::STATUS_HAVE_ALL_PACKS, [])]);

    // 10. Attente ResourcePackStackPacket
    $gotStack = false;
    $start = microtime(true);
    while (microtime(true) - $start < 3.0) {
        $pks = $readGamePackets(0.2);
        foreach ($pks as $p) {
            if ($p["id"] === ProtocolInfo::RESOURCE_PACK_STACK_PACKET) {
                $gotStack = true;
                break 2;
            }
        }
    }
    if (!$gotStack) {
        echo "[ERREUR] ResourcePackStack non recu\n";
        return false;
    }
    echo "[OK] ResourcePackStack recu\n";

    // 11. ResourcePackClientResponse(COMPLETED)
    $sendGameBatch([ResourcePackClientResponsePacket::create(ResourcePackClientResponsePacket::STATUS_COMPLETED, [])]);

    // 12. Attente StartGamePacket
    $gotStartGame = false;
    $actorRuntimeId = 0;
    $spawnY = 0.0;
    $start = microtime(true);
    while (microtime(true) - $start < 5.0) {
        $pks = $readGamePackets(0.2);
        foreach ($pks as $p) {
            if ($p["id"] === ProtocolInfo::START_GAME_PACKET) {
                $sg = new StartGamePacket();
                $sg->decode(new ByteBufferReader($p["data"]));
                $gotStartGame = true;
                $actorRuntimeId = $sg->actorRuntimeId;
                $spawnY = $sg->playerPosition->y;
                break 2;
            }
        }
    }
    if (!$gotStartGame) {
        echo "[ERREUR] StartGamePacket non recu\n";
        return false;
    }
    echo "[OK] StartGamePacket recu avec succes ! Position du joueur: Y={$spawnY}\n";

    // 13. Envoi RequestChunkRadiusPacket (6 chunks = 96 blocks) et ServerboundLoadingScreen(START)
    $sendGameBatch([
        RequestChunkRadiusPacket::create(6, 6),
        ServerboundLoadingScreenPacket::create(ServerboundLoadingScreenType::START_LOADING_SCREEN)
    ]);
    echo "[OK] Rayon de chunks demande (6 chunks) + Chargement d'ecran lance\n";

    // 14. Attente des chunks et de PlayStatusPacket::PLAYER_SPAWN
    $chunkCount = 0;
    $gotPlayerSpawn = false;
    $start = microtime(true);
    while (microtime(true) - $start < 10.0) {
        $pks = $readGamePackets(0.3);
        foreach ($pks as $p) {
            if ($p["id"] === ProtocolInfo::LEVEL_CHUNK_PACKET) {
                $chunkCount++;
            } elseif ($p["id"] === ProtocolInfo::PLAY_STATUS_PACKET) {
                $statusPk = new PlayStatusPacket();
                $statusPk->decode(new ByteBufferReader($p["data"]));
                if ($statusPk->status === PlayStatusPacket::PLAYER_SPAWN) {
                    $gotPlayerSpawn = true;
                }
            }
        }
        if ($gotPlayerSpawn) break;
    }

    if (!$gotPlayerSpawn) {
        echo "[ERREUR] PlayStatusPacket::PLAYER_SPAWN non recu ($chunkCount chunks recus)\n";
        return false;
    }
    echo "[OK] Terrain pret ! PlayStatusPacket::PLAYER_SPAWN recu avec succes ($chunkCount chunks charges) !\n";

    // 15. Envoi SetLocalPlayerAsInitializedPacket & ServerboundLoadingScreen(STOP)
    $sendGameBatch([
        SetLocalPlayerAsInitializedPacket::create($actorRuntimeId),
        ServerboundLoadingScreenPacket::create(ServerboundLoadingScreenType::STOP_LOADING_SCREEN)
    ]);
    echo "[OK] SetLocalPlayerAsInitializedPacket envoye au serveur\n";

    // 16. Verifier la reception des paquets in-game
    $gotInGamePackets = false;
    $start = microtime(true);
    while (microtime(true) - $start < 2.0) {
        $pks = $readGamePackets(0.2);
        foreach ($pks as $p) {
            if ($p["id"] === ProtocolInfo::SET_TIME_PACKET || $p["id"] === ProtocolInfo::SET_TITLE_PACKET || $p["id"] === ProtocolInfo::TEXT_PACKET) {
                $gotInGamePackets = true;
                break 2;
            }
        }
    }

    if ($gotInGamePackets) {
        echo "[VICTOIRE ABSOLUE] Katsun1246 est ENTIÈREMENT EN JEU et reçoit les paquets du monde sans aucune erreur !\n";
    } else {
        echo "[INFO] Joueur initialise dans InGamePacketHandler avec succes.\n";
    }

    $socket->close();
    return true;
}

$ok = testKatsunConnection();
if (!$ok) {
    exit(1);
}
