<?php
$host = "localhost";
$port = "5432";
$db   = "simpus_mini";
$user = "postgres";
$pass = "postgres";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // 1. Log pesan error asli beserta detailnya ke sistem log server, BUKAN ke layar
    error_log("Database connection error: " . $e->getMessage());
    
    // 2. Tampilkan pesan peringatan yang generik dan aman kepada pengguna
    die("Maaf, sedang terjadi gangguan koneksi pada sistem. Silakan coba beberapa saat lagi.");
}
?>