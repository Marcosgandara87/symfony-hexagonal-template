<?php
// Asegurar que la carpeta exista
if (!is_dir('config/jwt')) {
    mkdir('config/jwt', 0777, true);
}

// Configurar la matemática criptográfica RSA de 2048 bits pasándole el archivo del sistema
$config = [
    "digest_alg" => "sha256",
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
    "config" => getenv('OPENSSL_CONF') // 🚀 Le inyectamos la variable que pusimos en el .env
];

$resource = openssl_pkey_new($config);
if (!$resource) {
    die("❌ Error crítico: OpenSSL sigue sin funcionar. Revisá la ruta de OPENSSL_CONF en tu .env\n");
}

openssl_pkey_export($resource, $privateKey, null, $config);
$details = openssl_pkey_get_details($resource);
$publicKey = $details["key"];

// Escribirlas de forma limpia en el disco duro de Windows
file_put_contents('config/jwt/private.pem', $privateKey);
file_put_contents('config/jwt/public.pem', $publicKey);

echo "¡G-L-O-R-I-O-S-O! Tus llaves reales se crearon y emparejaron con éxito.\n";
