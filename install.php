<?php
// UlinKaCiamis - Auto Installer
// Akses sekali: http://localhost/ulinkaciamis/install.php
// Hapus file ini setelah instalasi!

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ulinkaciamis');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conn->connect_error) {
    exit('<p style="color:red">Koneksi MySQL gagal: '.$conn->connect_error.'</p>');
}
$conn->set_charset('utf8mb4');

// Check if already installed
$db_check = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".DB_NAME."'");
if ($db_check && $db_check->num_rows > 0) {
    $conn->select_db(DB_NAME);
    $table_check = $conn->query("SHOW TABLES LIKE 'destinasi'");
    if ($table_check && $table_check->num_rows > 0) {
        $count = $conn->query('SELECT COUNT(*) AS total FROM destinasi')->fetch_assoc()['total'];
        exit('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>UlinKa Ciamis - Terinstal</title><style>body{font-family:sans-serif;background:#080c18;color:#f1f5f9;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}.box{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:40px;max-width:500px;text-align:center}.btn{display:inline-block;padding:12px 24px;background:#10B981;color:#fff;border-radius:100px;text-decoration:none;font-weight:700;margin:10px}</style></head><body><div class="box"><h1>✅ Sudah Terinstal</h1><p>Database <code>ulinkaciamis</code> sudah ada dengan <strong>'.$count.' destinasi</strong>.</p><a href="/ulinkaciamis/" class="btn">🗺️ Buka Peta</a><a href="/ulinkaciamis/admin/login.php" class="btn" style="background:#3B82F6">🔐 Login Admin</a></div></body></html>');
    }
}

$conn->query('CREATE DATABASE IF NOT EXISTS '.DB_NAME.' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$conn->select_db(DB_NAME);

$sql = file_get_contents(__DIR__.'/database.sql');
$queries = array_filter(array_map('trim', explode(';', $sql)));

$errors = [];
$success = 0;
foreach ($queries as $q) {
    if (empty($q) || strpos($q, '--') === 0) {
        continue;
    }
    try {
        if (! $conn->query($q)) {
            $errors[] = $conn->error.' | Query: '.substr($q, 0, 100);
        } else {
            $success++;
        }
    } catch (Exception $ex) {
        $errors[] = $ex->getMessage().' | Query: '.substr($q, 0, 100);
    }
}

// Hash default password "password"
$hash = password_hash('password', PASSWORD_DEFAULT);
$conn->select_db(DB_NAME);
$conn->query("UPDATE admin_users SET password='$hash' WHERE username IN ('superadmin','perangkat')");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Installer - UlinKa Ciamis</title>
<style>
body{font-family:sans-serif;background:#080c18;color:#f1f5f9;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}
.box{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:40px;max-width:600px;width:90%}
h1{font-size:22px;margin-bottom:8px}
.sub{color:#94A3B8;margin-bottom:24px}
.ok{color:#10B981;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.2);padding:12px 16px;border-radius:10px;margin-bottom:12px}
.err{color:#FCA5A5;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);padding:8px 12px;border-radius:8px;margin-bottom:6px;font-size:13px}
.btn{display:inline-block;padding:12px 24px;background:linear-gradient(135deg,#10B981,#0d9668);color:#fff;border-radius:100px;text-decoration:none;font-weight:700;margin-top:16px}
table{width:100%;border-collapse:collapse;margin:16px 0;font-size:13px}
td,th{padding:8px 12px;border:1px solid rgba(255,255,255,.08);text-align:left}
th{background:rgba(255,255,255,.04);color:#94A3B8}
code{background:rgba(255,255,255,.08);padding:2px 6px;border-radius:4px;font-size:12px}
</style>
</head>
<body>
<div class="box">
  <h1>🗺️ UlinKa Ciamis — Installer</h1>
  <p class="sub">Setup database otomatis</p>

  <?php if (empty($errors)) { ?>
    <div class="ok">✅ Database berhasil dibuat! (<?= $success ?> query berhasil)</div>
    <table>
      <tr><th>Info</th><th>Nilai</th></tr>
      <tr><td>Database</td><td><code>ulinkaciamis</code></td></tr>
      <tr><td>Login Superadmin</td><td><code>superadmin</code> / <code>password</code></td></tr>
      <tr><td>Login Perangkat Desa</td><td><code>perangkat</code> / <code>password</code></td></tr>
    </table>
    <p style="color:#F59E0B;font-size:13px">⚠️ Hapus file <code>install.php</code> setelah ini untuk keamanan!</p>
    <a href="/ulinkaciamis/" class="btn">🗺️ Buka UlinKa Ciamis</a>
    <a href="/ulinkaciamis/admin/login.php" class="btn" style="margin-left:10px">🔐 Login Admin</a>
  <?php } else { ?>
    <div class="ok">✅ <?= $success ?> query berhasil</div>
    <?php foreach ($errors as $e) { ?>
      <div class="err">⚠️ <?= htmlspecialchars($e) ?></div>
    <?php } ?>
    <p style="color:#94A3B8;margin-top:16px;font-size:13px">Beberapa error mungkin karena tabel sudah ada (normal).</p>
    <a href="/ulinkaciamis/" class="btn">🗺️ Coba Buka Peta</a>
  <?php } ?>
</div>
</body>
</html>
