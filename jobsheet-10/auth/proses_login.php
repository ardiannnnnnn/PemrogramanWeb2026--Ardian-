<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../includes/koneksi.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// NOMOR 3: Batasi percobaan login gagal per username (disimpan di $_SESSION)
$maxGagal  = 3;   
$lamaKunci = 30;  
$kunciUser = mb_strtolower($username);
$info = $_SESSION['gagal_login'][$kunciUser] ?? ['jumlah' => 0, 'sampai' => 0];

if ($info['sampai'] > 0 && $info['sampai'] <= time()) {
    $info = ['jumlah' => 0, 'sampai' => 0];
    unset($_SESSION['gagal_login'][$kunciUser]);
}

if ($info['sampai'] > time()) {
    $sisa = $info['sampai'] - time();
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => "Terlalu banyak percobaan gagal untuk username ini. Coba lagi dalam $sisa detik."];
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    unset($_SESSION['gagal_login'][$kunciUser]);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];

    // NOMOR 2: Ingat Saya
    if (!empty($_POST['ingat'])) {
        $token = bin2hex(random_bytes(32));          
        $hash  = hash('sha256', $token);            
        $upd = $pdo->prepare("UPDATE users SET remember_token = :t WHERE id = :id");
        $upd->execute(['t' => $hash, 'id' => $user['id']]);

        setcookie('ingat_saya', $user['id'] . ':' . $token, [
            'expires'  => time() + 60 * 60 * 24 * 30, 
            'path'     => '/',
            'httponly' => true,                        
            'samesite' => 'Lax',
        ]);
    }

    header('Location: ../index.php');
    exit;
}

// NOMOR 3: Login gagal, tambah hitungan (username yang tidak terdaftar juga dihitung)
$info['jumlah']++;
if ($info['jumlah'] >= $maxGagal) {
    $info['sampai'] = time() + $lamaKunci;
    $pesan = "Login gagal $maxGagal kali berturut-turut. Username ini diblokir sementara selama $lamaKunci detik.";
} else {
    $sisa = $maxGagal - $info['jumlah'];
    $pesan = "Username atau password salah. Sisa percobaan: $sisa.";
}
$_SESSION['gagal_login'][$kunciUser] = $info;

$_SESSION['flash'] = ['type' => 'error', 'pesan' => $pesan];
header('Location: login.php');
exit;