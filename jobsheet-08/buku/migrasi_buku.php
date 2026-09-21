<?php
date_default_timezone_set('Asia/Jakarta');

// Call database connection
require_once __DIR__ . '/../includes/koneksi.php';

// Daftar kemungkinan lokasi file buku.json (Jobsheet-06 §3 / data/)
$possible_paths = [
    __DIR__ . '/../data/buku.json',                             // jobsheet-08/data/buku.json
    __DIR__ . '/../../data/buku.json',                            // root/data/buku.json
    __DIR__ . '/../../jobsheet-06/data/buku.json',               // jobsheet-06/data/buku.json
    __DIR__ . '/../../jobsheet-06/buku.json'                     // jobsheet-06/buku.json
];

$json_path = null;
foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        $json_path = $path;
        break;
    }
}

// 1. Jika file JSON belum ditemukan di lokasi manapun
if (!$json_path) {
    echo "<h3 style='color:red;'>Error: File buku.json tidak ditemukan!</h3>";
    echo "<p>Sistem sudah mencari di beberapa lokasi berikut:</p><ul>";
    foreach ($possible_paths as $p) {
        echo "<li><code>" . htmlspecialchars(realpath($p) ?: $p) . "</code></li>";
    }
    echo "</ul>";
    echo "<p><strong>Solusi:</strong> Silakan buat folder <code>data</code> di dalam <code>jobsheet-08</code> lalu taruh file <code>buku.json</code> di sana.</p>";
    exit;
}

// 2. Baca isi file JSON
$json_data = file_get_contents($json_path);
$buku_list = json_decode($json_data, true);

if (!is_array($buku_list) || empty($buku_list)) {
    die("<h3 style='color:red;'>Error: File JSON di '{$json_path}' kosong atau formatnya tidak valid!</h3>");
}

echo "<h3>Memulai Proses Migrasi Data Buku dari Jobsheet-06...</h3>";
echo "<p>Membaca file dari: <code>" . htmlspecialchars($json_path) . "</code></p><hr>";

$berhasil = 0;
$gagal = 0;

// 3. Prepare Query INSERT
$sql = "INSERT INTO buku (judul, pengarang, tahun, stok, tanggal_ditambahkan) 
        VALUES (:judul, :pengarang, :tahun, :stok, :tanggal_ditambahkan)";

$stmt = $pdo->prepare($sql);

// 4. Looping & INSERT ke PostgreSQL
foreach ($buku_list as $buku) {
    try {
        $judul     = trim($buku['judul'] ?? '');
        $pengarang = trim($buku['pengarang'] ?? '');
        $tahun     = isset($buku['tahun']) ? (int)$buku['tahun'] : null;
        $stok      = isset($buku['stok']) ? (int)$buku['stok'] : 0;
        
        // Ambil tanggal jika ada, atau gunakan default tanggal saat ini
        $tanggal   = !empty($buku['tanggal_ditambahkan']) 
                      ? $buku['tanggal_ditambahkan'] 
                      : date('Y-m-d H:i:s');

        $stmt->execute([
            ':judul'               => $judul,
            ':pengarang'           => $pengarang,
            ':tahun'               => $tahun,
            ':stok'                => $stok,
            ':tanggal_ditambahkan' => $tanggal
        ]);

        $berhasil++;
        echo "✅ Berhasil mengimpor: <strong>" . htmlspecialchars($judul) . "</strong><br>";

    } catch (PDOException $e) {
        $gagal++;
        echo "❌ <span style='color:red;'>Gagal mengimpor '" . htmlspecialchars($buku['judul'] ?? 'Buku') . "': " . $e->getMessage() . "</span><br>";
    }
}

echo "<hr>";
echo "<h4>Proses Migrasi Selesai!</h4>";
echo "Total Data Berhasil Diimpor: <strong>{$berhasil}</strong><br>";
echo "Total Data Gagal: <strong>{$gagal}</strong><br>";
echo "<br><a href='list.php'>👉 Lihat Daftar Buku</a>";