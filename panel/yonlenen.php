<?php
$config = parse_ini_file(__DIR__ . '/../redirect_config.ini');
if (!isset($_GET['password']) || $_GET['password']!==$config['panel_password']) { http_response_code(403); die('Yetkisiz erişim'); }
$lines = file(__DIR__.'/../logs.txt', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
$records = [];
foreach ($lines as $line) {
    if (strpos($line, '[REAL]')===0 && preg_match('/\[(.*?)\] \[(.*?)\] IP: (.*?) \| UA: (.*)/',$line,$m)) {
        $records[]=['time'=>$m[2],'ip'=>$m[3],'ua'=>$m[4]];
    }
}
?>
<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><title>Yönlendirilen Trafik</title><link rel="stylesheet" href="assets/css/style.bundle.css"></head>
<body class="<?=htmlspecialchars($config['theme_mode'])?>"><?php include 'header.php'; ?>
<div class="container"><h1>Yönlendirilen Trafik</h1><table><thead><tr><th>#</th><th>IP</th><th>Zaman</th><th>UA</th></tr></thead><tbody><?php foreach($records as $i=>$r):?><tr><td><?= $i+1 ?></td><td><?=htmlspecialchars($r['ip'])?></td><td><?=htmlspecialchars($r['time'])?></td><td><?=htmlspecialchars($r['ua'])?></td></tr><?php endforeach;?></tbody></table></div><?php include 'footer.php'; ?>