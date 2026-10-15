<?php

require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

$sslConfig = "C:/Users/basti/OneDrive/Bureau/Opal Heaven/server/bin/extras/ssl/openssl.cnf";
$key256 = openssl_pkey_new(['config' => $sslConfig, 'curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC]);
$details = openssl_pkey_get_details($key256);
$pem = $details['key'];
echo "PEM raw:\n" . addcslashes($pem, "\r\n\t") . "\n";
echo "Has \\r: " . (str_contains($pem, "\r") ? "YES" : "NO") . "\n";
