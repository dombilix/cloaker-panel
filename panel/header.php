<?php
$config = parse_ini_file(__DIR__ . '/../redirect_config.ini');
$pw     = urlencode($config['panel_password']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Cloaker Panel</title>
  <link rel="stylesheet" href="/panel/assets/css/style.bundle.css">
</head>
<body class="<?= htmlspecialchars($config['theme_mode'] ?? 'light') ?>">
  <aside class="sidebar">
    <!-- Menü buraya -->
    <ul class="nav">
      <li><a href="dashboard.php?password=<?= $pw ?>">Dashboard</a></li>
      <li>
        Trafik
        <ul>
          <li><a href="yonlenen.php?password=<?= $pw ?>">Yönlenen</a></li>
          <li><a href="engellenen.php?password=<?= $pw ?>">Engellenen</a></li>
          <li><a href="tum-istatistik.php?password=<?= $pw ?>">Tüm İstatistik</a></li>
        </ul>
      </li>
      <li><a href="settings.php?password=<?= $pw ?>">Kurulum</a></li>
      <li><a href="donusum.php?password=<?= $pw ?>">Dönüşüm</a></li>
    </ul>
  </aside>
  <main class="main-content">
