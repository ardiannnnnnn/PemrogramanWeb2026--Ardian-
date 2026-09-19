<?php
// NOMOR 3: Halaman debug sementara (HAPUS setelah selesai latihan)
session_start();

// NOMOR 4: Tombol Reset Data (memanggil session_destroy)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    $_SESSION = [];                       // kosongkan variabel $_SESSION di skrip ini
    if (ini_get('session.use_cookies')) { // hapus cookie sesi di browser
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();                    // hapus data sesi di server
    header('Location: debug_session.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Debug Session</title>
</head>
<body>
    <h1>Isi $_SESSION</h1>
    <pre><?php print_r($_SESSION); ?></pre>

    <!-- NOMOR 4: Tombol Reset Data -->
    <form method="post" onsubmit="return confirm('Kosongkan seluruh $_SESSION?');">
        <button type="submit" name="reset" value="1">Reset Data</button>
    </form>
</body>
</html>