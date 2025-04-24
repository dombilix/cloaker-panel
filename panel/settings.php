<?php
// panel/settings.php — Kurulum
ini_set('display_errors',1);
error_reporting(E_ALL);

$configFile = __DIR__ . '/../redirect_config.ini';
if (!file_exists($configFile)) die('Config dosyası bulunamadı');
$config = parse_ini_file($configFile);

if (!isset($_GET['password']) || $_GET['password'] !== $config['panel_password']) {
    http_response_code(403);
    die('Yetkisiz erişim');
}

$message = '';
if (isset($_GET['download']) && $_GET['download']=='csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="logs.csv"');
    echo "type,time,ip,ua\n";
    foreach(file(__DIR__.'/../logs.txt') as $l){
        if(preg_match('/\[(REAL|BLOCKED)\] \[(.*?)\] IP: (.*?) \| UA: (.*)/',$l,$m)){
            $ua = str_replace('"','""',$m[4]);
            echo "{$m[1]},{$m[2]},{$m[3]},\"{$ua}\"\n";
        }
    }
    exit;
}
if (isset($_GET['reset'])&&$_GET['reset']=='1'){
    file_put_contents(__DIR__.'/../logs.txt','');
    $message='Tıklamalar sıfırlandı.';
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $new = [
        'panel_password'=>trim($_POST['panel_password'])?:$config['panel_password'],
        'theme_mode'=>trim($_POST['theme_mode'])?:$config['theme_mode'],
        'redirect_url'=>trim($_POST['redirect_url'])?:$config['redirect_url'],
        'redirect_enabled'=>isset($_POST['redirect_enabled'])?1:0,
        'allowed_countries'=>trim($_POST['allowed_countries'])?:$config['allowed_countries'],
        'allowed_languages'=>trim($_POST['allowed_languages'])?:$config['allowed_languages'],
        'allowed_devices'=>trim($_POST['allowed_devices'])?:$config['allowed_devices'],
        'allowed_platforms'=>trim($_POST['allowed_platforms'])?:$config['allowed_platforms'],
    ];
    $out='';
    foreach($new as $k=>$v){
        $v=str_replace('"','\\"',$v);
        $out.="$k=\"{$v}\"\n";
    }
    file_put_contents($configFile,$out);
    $message='Ayarlar kaydedildi.';
    $config=$new;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head><meta charset="UTF-8"><title>Kurulum</title>
<link rel="stylesheet" href="assets/css/style.bundle.css">
</head><body class="<?=htmlspecialchars($config['theme_mode'])?>">
<?php include 'header.php'; ?>
<div class="container"><h1>Cloaker Ayar Paneli</h1>
<?php if($message):?><p><?=htmlspecialchars($message)?></p><?php endif;?>
<form method="post" action="?password=<?=urlencode($config['panel_password'])?>">
<label>Yönlendirme URL:<br>
<input type="url" name="redirect_url" required value="<?=htmlspecialchars($config['redirect_url'])?>"></label><br>
<label><input type="checkbox" name="redirect_enabled" <?= $config['redirect_enabled']?'checked':'' ?>>Cloaker Aktif</label><br>
<label>Panel Şifresi:<br><input type="text" name="panel_password" required value="<?=htmlspecialchars($config['panel_password'])?>"></label><br>
<label>Ülkeler (CSV):<br><input type="text" name="allowed_countries" value="<?=htmlspecialchars($config['allowed_countries'])?>"></label><br>
<label>Diller (CSV):<br><input type="text" name="allowed_languages" value="<?=htmlspecialchars($config['allowed_languages'])?>"></label><br>
<label>Cihazlar (CSV):<br><input type="text" name="allowed_devices" value="<?=htmlspecialchars($config['allowed_devices'])?>"></label><br>
<label>Platformlar (CSV):<br><input type="text" name="allowed_platforms" value="<?=htmlspecialchars($config['allowed_platforms'])?>"></label><br>
<button type="submit">Kaydet</button>
<a href="?password=<?=urlencode($config['panel_password'])?>&download=csv">CSV İndir</a>
<a href="?password=<?=urlencode($config['panel_password'])?>&reset=1">Tıklamaları Sıfırla</a>
</form></div>
<script src="assets/js/scripts.bundle.js"></script>
<script src="assets/js/widgets.bundle.js"></script></body></html>