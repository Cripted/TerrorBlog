<?php
/**
 * php/image_proxy.php
 * Descarga y sirve imágenes externas para evitar ERR_BLOCKED_BY_ORB
 */
$url = $_GET['url'] ?? '';

if (!$url) {
    http_response_code(400);
    exit;
}

// Solo permitir dominios de Steam
$allowed = [
    'cdn.akamai.steamstatic.com',
    'cdn.cloudflare.steamstatic.com',
    'store.steampowered.com',
];

$host = parse_url($url, PHP_URL_HOST);
$permitido = false;
foreach ($allowed as $a) {
    if ($host === $a || str_ends_with($host, '.'.$a)) {
        $permitido = true;
        break;
    }
}

if (!$permitido) {
    http_response_code(403);
    exit;
}

$ctx = stream_context_create([
    'http' => [
        'timeout' => 8,
        'header'  => 'User-Agent: Mozilla/5.0 (compatible; TerrorDigital/1.0)',
    ],
    'ssl' => [
        'verify_peer'      => false,
        'verify_peer_name' => false,
    ],
]);

$data = @file_get_contents($url, false, $ctx);

if ($data === false) {
    http_response_code(502);
    exit;
}

// Detectar tipo de imagen
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->buffer($data);

header('Content-Type: ' . $mime);
header('Cache-Control: public, max-age=86400');
echo $data; 