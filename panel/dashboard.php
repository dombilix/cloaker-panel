<?php
// panel/dashboard.php — Temiz, Hata İçermeyen Sürüm
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

// Log verilerini oku
$logFile = __DIR__ . '/../logs.txt';
if (!file_exists($logFile)) {
    $lines = [];
} else {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

$total = count($lines);
$realLines   = array_filter($lines, fn($l) => str_starts_with($l, '[REAL]'));
$blockedLines= array_filter($lines, fn($l) => str_starts_with($l, '[BLOCKED]'));
$countReal   = count($realLines);
$countBlocked= count($blockedLines);

function parseLogLine(string $line): ?array {
    if (preg_match('/\[(REAL|BLOCKED)\] \[(.*?)\] IP: (.*?) \| UA: (.*)/', $line, $m)) {
        return [
            'type' => $m[1],
            'time' => $m[2],
            'ip'   => $m[3],
            'ua'   => $m[4],
        ];
    }
    return null;
}

$reals    = array_filter(
    array_map('parseLogLine', array_slice(array_reverse($realLines), 0, 5))
);
$blockeds = array_filter(
    array_map('parseLogLine', array_slice(array_reverse($blockedLines), 0, 5))
);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="assets/css/style.bundle.css">
</head>
<body class="<?= htmlspecialchars($config['theme_mode'] ?? 'light') ?>">
  <?php include __DIR__ . '/header.php'; ?>

  <div class="container">
    <h1>Günlük Trafik İstatistiği</h1>
    <div class="stats-overview">
      <div class="stat-card">
        <div class="stat-number"><?= $total ?></div>
        <div class="stat-label">Toplam Trafik</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $countReal ?></div>
        <div class="stat-label">Yönlendirilen Trafik</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $countBlocked ?></div>
        <div class="stat-label">Engellenen Trafik</div>
      </div>
    </div>

    <div class="tables-row">
      <div class="table-container">
        <h2>Son 5 Yönlendirilen Trafik</h2>
        <table>
          <thead><tr><th>#</th><th>IP Adresi</th><th>Zaman</th><th>UA</th></tr></thead>
          <tbody>
            <?php foreach ($reals as $i => $r): ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td><?= htmlspecialchars($r['ip']) ?></td>
              <td><?= htmlspecialchars($r['time']) ?></td>
              <td><?= htmlspecialchars($r['ua']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <a href="yonlenen.php?password=<?= urlencode($config['panel_password']) ?>" class="btn-small">Tüm Yönlendirilenler</a>
      </div>

      <div class="table-container">
        <h2>Son 5 Engellenen Trafik</h2>
        <table>
          <thead><tr><th>#</th><th>IP Adresi</th><th>Zaman</th><th>Neden</th></tr></thead>
          <tbody>
            <?php foreach ($blockeds as $i => $r): ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td><?= htmlspecialchars($r['ip']) ?></td>
              <td><?= htmlspecialchars($r['time']) ?></td>
              <td>Kurala Takıldı</td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <a href="engellenen.php?password=<?= urlencode($config['panel_password']) ?>" class="btn-small">Tüm Engellenenler</a>
      </div>
    </div>

    <div class="dashboard-actions">
      <a href="settings.php?password=<?= urlencode($config['panel_password']) ?>&download=csv" class="btn-green">CSV İndir</a>
      <a href="settings.php?password=<?= urlencode($config['panel_password']) ?>&reset=1" class="btn-red">Tıklamaları Sıfırla</a>
    </div>
  </div>

<?php include __DIR__ . '/footer.php'; ?>

</body>
</html>