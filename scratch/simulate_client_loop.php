<?php
require 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/pmmp_src/vendor/autoload.php';

use raklib\client\ClientSocket;
use raklib\protocol\Datagram;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\MessageIdentifiers;
use raklib\protocol\OpenConnectionReply1;
use raklib\protocol\OpenConnectionReply2;
use raklib\protocol\OpenConnectionRequest1;
use raklib\protocol\OpenConnectionRequest2;
use raklib\protocol\PacketReliability;
use raklib\protocol\PacketSerializer;
use raklib\protocol\ACK;
use raklib\utils\InternetAddress;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\RequestNetworkSettingsPacket;
use pocketmine\network\mcpe\protocol\NetworkSettingsPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;
use pocketmine\network\mcpe\protocol\LevelChunkPacket;
use pocketmine\network\mcpe\protocol\ServerboundLoadingScreenPacket;
use pocketmine\network\mcpe\protocol\types\hud\LoadingScreenType;
use pocketmine\network\mcpe\protocol\SetLocalPlayerAsInitializedPacket;
use pocketmine\network\mcpe\protocol\types\login\AuthenticationType;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\JwtUtils;
use Ramsey\Uuid\Uuid;

function runSingleClientSimulation(string $playerName, int $runIndex) : array {
    $t0 = microtime(true);
    $res = [
        "player" => $playerName,
        "run" => $runIndex,
        "success" => false,
        "chunks" => 0,
        "total_time_ms" => 0.0,
        "error" => null
    ];

    echo "\n--------------------------------------------------------\n";
    echo "  [RUN #$runIndex] Connexion du joueur : $playerName\n";
    echo "--------------------------------------------------------\n";
    ob_implicit_flush(true);

    $address = new InternetAddress("127.0.0.1", 19132, 4);
    $socket = new ClientSocket($address);
    $socket->setBlocking(false);
    $clientId = rand(1000000, 9999999);
    $mtuSize = 1492;

    // 1. OpenConnectionRequest1
    $req1 = new OpenConnectionRequest1();
    $req1->mtuSize = $mtuSize;
    $req1->protocol = 11;
    $s = new PacketSerializer();
    $req1->encode($s);
    $socket->writePacket($s->getBuffer());

    $reply1 = null;
    $start = microtime(true);
    while (microtime(true) - $start < 3.0) {
        $buf = $socket->readPacket();
        if ($buf !== null && strlen($buf) > 0 && ord($buf[0]) === MessageIdentifiers::ID_OPEN_CONNECTION_REPLY_1) {
            $reply1 = new OpenConnectionReply1();
            $reply1->decode(new PacketSerializer($buf));
            break;
        }
        usleep(5000);
    }
    if ($reply1 === null) {
        $res["error"] = "Pas de OpenConnectionReply1";
        echo "[ECHEC] {$res['error']}\n";
        return $res;
    }

    // 2. OpenConnectionRequest2
    $req2 = new OpenConnectionRequest2();
    $req2->clientID = $clientId;
    $req2->serverAddress = $address;
    $req2->mtuSize = $reply1->mtuSize;
    $s = new PacketSerializer();
    $req2->encode($s);
    $socket->writePacket($s->getBuffer());

    $reply2 = null;
    $start = microtime(true);
    while (microtime(true) - $start < 3.0) {
        $buf = $socket->readPacket();
        if ($buf !== null && strlen($buf) > 0 && ord($buf[0]) === MessageIdentifiers::ID_OPEN_CONNECTION_REPLY_2) {
            $reply2 = new OpenConnectionReply2();
            $reply2->decode(new PacketSerializer($buf));
            break;
        }
        usleep(5000);
    }
    if ($reply2 === null) {
        $res["error"] = "Pas de OpenConnectionReply2";
        echo "[ECHEC] {$res['error']}\n";
        return $res;
    }

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
            if ($compressionEnabled) {
                $sendRakPacket("\xfe\xff" . $raw);
            } else {
                $sendRakPacket("\xfe" . $raw);
            }
        }
    };

    // 3. ConnectionRequest
    $s = new PacketSerializer();
    $s->putByte(MessageIdentifiers::ID_CONNECTION_REQUEST);
    $s->putLong($clientId);
    $s->putLong((int)(microtime(true) * 1000));
    $s->putByte(0);
    $sendRakPacket($s->getBuffer());

    $accepted = false;
    $start = microtime(true);
    while (microtime(true) - $start < 3.0) {
        $buf = $socket->readPacket();
        if ($buf !== null && strlen($buf) > 0) {
            $id = ord($buf[0]);
            if (($id & 0x80) !== 0 && ($id & 0x40) === 0 && ($id & 0x20) === 0) {
                $inDg = new Datagram();
                $inDg->decode(new PacketSerializer($buf));
                foreach ($inDg->packets as $pk) {
                    if (strlen($pk->buffer) > 0 && ord($pk->buffer[0]) === MessageIdentifiers::ID_CONNECTION_REQUEST_ACCEPTED) {
                        $accepted = true;
                        break 2;
                    }
                }
            }
        }
        usleep(5000);
    }
    if (!$accepted) {
        $res["error"] = "Pas de ConnectionRequestAccepted";
        echo "[ECHEC] {$res['error']}\n";
        return $res;
    }

    // 4. NewIncomingConnection
    $s = new PacketSerializer();
    $s->putByte(MessageIdentifiers::ID_NEW_INCOMING_CONNECTION);
    $s->putAddress($address);
    for ($i = 0; $i < 20; $i++) {
        $s->putAddress(new InternetAddress("0.0.0.0", 0, 4));
    }
    $s->putLong((int)(microtime(true) * 1000));
    $s->putLong((int)(microtime(true) * 1000));
    $sendRakPacket($s->getBuffer());

    // 5. RequestNetworkSettingsPacket
    $sendGameBatch([RequestNetworkSettingsPacket::create(ProtocolInfo::CURRENT_PROTOCOL)]);

    $splitBuffers = [];

    $readGamePackets = function(float $timeout = 3.0) use ($socket, &$splitBuffers, &$compressionEnabled) : array {
        $start = microtime(true);
        $packets = [];
        while (microtime(true) - $start < $timeout) {
            while (($buf = $socket->readPacket()) !== null) {
                if (strlen($buf) === 0) continue;
                $id = ord($buf[0]);
                if (($id & 0x80) !== 0 && ($id & 0x40) === 0 && ($id & 0x20) === 0) {
                    $inDg = new Datagram();
                    $inDg->decode(new PacketSerializer($buf));

                    $ack = new ACK();
                    $ack->packets = [$inDg->seqNumber];
                    $ackSer = new PacketSerializer();
                    $ack->encode($ackSer);
                    $socket->writePacket($ackSer->getBuffer());

                    foreach ($inDg->packets as $pk) {
                        $fullPayload = null;
                        if ($pk->splitInfo !== null) {
                            $splitId = $pk->splitInfo->getId();
                            $splitBuffers[$splitId][$pk->splitInfo->getPartIndex()] = $pk->buffer;
                            if (count($splitBuffers[$splitId]) === $pk->splitInfo->getTotalPartCount()) {
                                ksort($splitBuffers[$splitId]);
                                $fullPayload = implode("", $splitBuffers[$splitId]);
                                unset($splitBuffers[$splitId]);
                            }
                        } else {
                            $fullPayload = $pk->buffer;
                        }

                        if ($fullPayload !== null && strlen($fullPayload) > 0 && ord($fullPayload[0]) === 0xfe) {
                            $payload = substr($fullPayload, 1);
                            if ($compressionEnabled) {
                                $algo = ord($payload[0]);
                                $compressed = substr($payload, 1);
                                if ($algo === 0) {
                                    $decompressed = @zlib_decode($compressed);
                                } elseif ($algo === 0xff) {
                                    $decompressed = $compressed;
                                } else {
                                    $decompressed = false;
                                }
                            } else {
                                $decompressed = $payload;
                            }

                            if ($decompressed !== false && strlen($decompressed) > 0) {
                                $reader = new ByteBufferReader($decompressed);
                                while ($reader->getUnreadLength() > 0) {
                                    try {
                                        $len = VarInt::readUnsignedInt($reader);
                                        $pkData = $reader->readByteArray($len);
                                        $pkReader = new ByteBufferReader($pkData);
                                        $header = VarInt::readUnsignedInt($pkReader);
                                        $pkId = $header & 0x3ff;
                                        $packets[] = [
                                            "id" => $pkId,
                                            "data" => $pkData
                                        ];
                                    } catch (\Throwable $e) {
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (count($packets) > 0) {
                break;
            }
            usleep(5000);
        }
        return $packets;
    };

    // Attendre NetworkSettingsPacket
    $pks = $readGamePackets(3.0);
    $gotNetSettings = false;
    foreach ($pks as $p) {
        if ($p["id"] === ProtocolInfo::NETWORK_SETTINGS_PACKET) {
            $netSettings = new NetworkSettingsPacket();
            $netSettings->decode(new ByteBufferReader($p["data"]));
            $compressionAlgorithm = $netSettings->getCompressionAlgorithm();
            $compressionThreshold = $netSettings->getCompressionThreshold();
            $compressionEnabled = true;
            $gotNetSettings = true;
            break;
        }
    }
    if (!$gotNetSettings) {
        $res["error"] = "NetworkSettings non reçu";
        echo "[ECHEC] {$res['error']}\n";
        return $res;
    }

    // 6. Envoi LoginPacket
    $sslConfig = "C:/Users/basti/OneDrive/Bureau/Opal Heaven/server/bin/extras/ssl/openssl.cnf";
    $key = openssl_pkey_new([
        "config" => $sslConfig,
        "curve_name" => "secp384r1",
        "private_key_type" => OPENSSL_KEYTYPE_EC
    ]);
    $derKey = JwtUtils::emitDerPublicKey($key);
    $b64Key = base64_encode($derKey);
    $uuid = Uuid::uuid4()->toString();
    $time = time();

    $claims = [
        "xname" => $playerName,
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
        "AuthenticationType" => AuthenticationType::SELF_SIGNED->value,
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
        "ThirdPartyName" => $playerName,
        "TrustedSkin" => true,
        "UIProfile" => 0,
    ];
    $clientJwt = JwtUtils::create(["alg" => "ES384", "x5u" => $b64Key], $clientClaims, $key);
    $loginPk = LoginPacket::create(ProtocolInfo::CURRENT_PROTOCOL, $authInfoJson, $clientJwt);

    $sendGameBatch([$loginPk]);

    // 7. Attendre PlayStatusPacket(LOGIN_SUCCESS) et ResourcePacksInfoPacket
    $gotLoginSuccess = false;
    $gotResourcePacks = false;
    $start = microtime(true);
    while (microtime(true) - $start < 5.0) {
        $pks = $readGamePackets(0.5);
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
        $res["error"] = "LOGIN_SUCCESS non reçu";
        echo "[ECHEC] {$res['error']}\n";
        return $res;
    }

    // 8. Envoi ResourcePackClientResponsePacket(HAVE_ALL_PACKS)
    $resp1 = ResourcePackClientResponsePacket::create(ResourcePackClientResponsePacket::STATUS_HAVE_ALL_PACKS, []);
    $sendGameBatch([$resp1]);

    // 9. Attendre ResourcePackStackPacket
    $gotStack = false;
    $start = microtime(true);
    while (microtime(true) - $start < 5.0) {
        $pks = $readGamePackets(0.5);
        foreach ($pks as $p) {
            if ($p["id"] === ProtocolInfo::RESOURCE_PACK_STACK_PACKET) {
                $gotStack = true;
                break 2;
            }
        }
    }
    if (!$gotStack) {
        $res["error"] = "ResourcePackStackPacket non reçu";
        echo "[ECHEC] {$res['error']}\n";
        return $res;
    }

    // 10. Envoi ResourcePackClientResponsePacket(COMPLETED)
    $resp2 = ResourcePackClientResponsePacket::create(ResourcePackClientResponsePacket::STATUS_COMPLETED, []);
    $sendGameBatch([$resp2]);

    // 11. Attendre StartGamePacket
    $gotStartGame = false;
    $actorRuntimeId = 0;
    $start = microtime(true);
    while (microtime(true) - $start < 5.0) {
        $pks = $readGamePackets(0.5);
        foreach ($pks as $p) {
            if ($p["id"] === ProtocolInfo::START_GAME_PACKET) {
                $sg = new StartGamePacket();
                $sg->decode(new ByteBufferReader($p["data"]));
                $gotStartGame = true;
                $actorRuntimeId = $sg->actorRuntimeId;
                break 2;
            }
        }
    }
    if (!$gotStartGame) {
        $res["error"] = "StartGamePacket non reçu";
        echo "[ECHEC] {$res['error']}\n";
        return $res;
    }

    // 12. Envoi RequestChunkRadiusPacket (96 chunks comme le vrai client Windows) + ServerboundLoadingScreen(START)
    $sendGameBatch([
        RequestChunkRadiusPacket::create(96, 96),
        ServerboundLoadingScreenPacket::create(LoadingScreenType::START_LOADING_SCREEN, null)
    ]);

    // 13. Attendre chunks et PlayStatusPacket::PLAYER_SPAWN
    $chunkCount = 0;
    $gotPlayerSpawn = false;
    $start = microtime(true);
    while (microtime(true) - $start < 10.0) {
        $pks = $readGamePackets(0.5);
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
        $res["error"] = "PLAYER_SPAWN non reçu ($chunkCount chunks)";
        echo "[ECHEC] {$res['error']}\n";
        return $res;
    }

    // 14. Envoi SetLocalPlayerAsInitializedPacket & ServerboundLoadingScreen(STOP)
    $sendGameBatch([
        SetLocalPlayerAsInitializedPacket::create($actorRuntimeId),
        ServerboundLoadingScreenPacket::create(LoadingScreenType::STOP_LOADING_SCREEN, null)
    ]);

    // 15. Attendre et vérifier la réception des paquets en jeu (SetTimePacket, SetTitlePacket, etc.)
    $gotInGame = false;
    $start = microtime(true);
    while (microtime(true) - $start < 1.0) {
        $pks = $readGamePackets(0.2);
        foreach ($pks as $p) {
            if (in_array($p["id"], [ProtocolInfo::SET_TIME_PACKET, ProtocolInfo::SET_TITLE_PACKET, ProtocolInfo::TEXT_PACKET])) {
                $gotInGame = true;
                break 2;
            }
        }
    }

    $res["chunks"] = $chunkCount;
    $res["success"] = true;
    $res["total_time_ms"] = round((microtime(true) - $t0) * 1000, 2);

    $inGameLabel = $gotInGame ? "[EN JEU]" : "[SPAWNÉ]";
    echo "[SUCCÈS $inGameLabel] Joueur $playerName connecté & en jeu en {$res['total_time_ms']} ms ($chunkCount chunks reçus) !\n";

    // Fermeture propre de la session
    $socket->close();
    return $res;
}

// Lancement de la boucle de simulation
echo "========================================================\n";
echo "   BOUCLE AUTOMATISÉE DE SIMULATION DE CONNEXIONS      \n";
echo "       Serveur Opal Heaven - Minecraft Bedrock         \n";
echo "========================================================\n\n";

$testPlayers = [
    "Katsun1246",       // Run 1 : Premier join
    "Katsun1246",       // Run 2 : Re-connexion (sauvegarde .dat)
    "Bretteur_Alpha",   // Run 3
    "Berserker_Krag",   // Run 4
    "Traqueur_Swift",   // Run 5
    "Sismo_Terra",      // Run 6
    "Prospec_Gold",     // Run 7
    "Botaniste_Flora",  // Run 8
    "Maitre_Fauve",     // Run 9
    "Apothicaire_Elix"  // Run 10
];

$iterations = count($testPlayers);
$successCount = 0;
$results = [];

foreach ($testPlayers as $idx => $player) {
    $runIndex = $idx + 1;
    $res = runSingleClientSimulation($player, $runIndex);
    $results[] = $res;
    if ($res["success"]) {
        $successCount++;
    }
    // Petite pause entre chaque connexion
    usleep(250000);
}

echo "\n========================================================\n";
echo "                 RAPPORT DE SIMULATION                  \n";
echo "========================================================\n";
echo "Total Connexions Testées : $iterations\n";
echo "Connexions Réussies      : $successCount / $iterations (" . round(($successCount / $iterations) * 100, 1) . "%)\n";

$avgTime = 0.0;
foreach ($results as $r) {
    if ($r["success"]) {
        $avgTime += $r["total_time_ms"];
    }
}
if ($successCount > 0) {
    $avgTime = round($avgTime / $successCount, 2);
    echo "Temps Moyen de Connexion : {$avgTime} ms\n";
}
echo "========================================================\n";

if ($successCount === $iterations) {
    echo ">>> TOUTES LES SIMULATIONS ONT RÉUSSI À 100% ! <<<\n";
    exit(0);
} else {
    echo ">>> DES SIMULATIONS ONT ÉCHOUÉ <<<\n";
    exit(1);
}
