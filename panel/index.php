<?php
// panel/index.php — Ana Giriş Yönlendirme
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Config dosyasını oku
$configFile = __DIR__ . '/../redirect_config.ini';
if (!file_exists($configFile)) {
    die('Config dosyası bulunamadı');
}
$config = parse_ini_file($configFile);

// Yetki kontrolü
if (!isset($_GET['password']) || $_GET['password'] !== $config['panel_password']) {
    http_response_code(403);
    die('Yetkisiz erişim');
}

// Dashboard sayfasına yönlendir
header('Location: dashboard.php?password=' . urlencode($config['panel_password']));
exit;
?>