<?php
<?php
$url = $_GET['url'] ?? '';

if (!$url) { http_response_code(400); exit; }

$allowed = ['cdn.akamai.steamstatic.com', 'cdn.cloudflare.steamstatic.com'];
$host = parse_url($url, PHP_URL_HOST);
$permitido = false;
foreach ($allowed as $a) {
    if ($host === $a || str_ends_with($host, '.'.$a)) {
        $permitido = true; break;
    }
}
if (!$permitido) { http_response_code(403); exit; }

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT        => 8,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; TerrorDigital/1.0)',
]);

$data = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (!$data || $httpCode !== 200) { http_response_code(502); exit; }

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->buffer($data);

header('Content-Type: ' . $mime);
header('Cache-Control: public, max-age=86400');
echo $data;