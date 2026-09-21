<?php
$page_title = "Daftar Buku";
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/koneksi.php';

// 1. Ambil keyword dari URL (metode GET)
$keyword = trim($_GET['q'] ?? '');

// 2. Buat Query Dasar
$sql = "SELECT * FROM buku";
$params = [];

// 3. Tambahkan klausa WHERE ILIKE jika ada kata kunci pencarian
if ($keyword !== '') {
    $sql .= " WHERE judul ILIKE :keyword";
    $params[':keyword'] = '%' . $keyword . '%';
}

$sql .= " ORDER BY id DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $daftar_buku = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Gagal mengambil data buku: " . $e->getMessage();
}
?>

<section>
    <h2>Daftar Buku</h2>

    <!-- Form Pencarian Server-Side -->
    <form method="get" action="list.php" style="margin-bottom: 20px;">
        <input 
            type="text" 
            name="q" 
            placeholder="Cari berdasarkan judul..." 
            value="<?= htmlspecialchars($keyword); ?>"
        >
        <button type="submit">Cari</button>
        <?php if ($keyword !== ''): ?>
            <a href="list.php" style="margin-left: 10px;">Reset</a>
        <?php endif; ?>
    </form>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Judul</th>
                <th>Pengarang</th>
                <th>Tahun</th>
                <th>Stok</th>
                <th>Tanggal Ditambahkan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($daftar_buku)): ?>
                <?php foreach ($daftar_buku as $buku): ?>
                    <tr>
                        <td><?= htmlspecialchars($buku['id']); ?></td>
                        <td><?= htmlspecialchars($buku['judul']); ?></td>
                        <td><?= htmlspecialchars($buku['pengarang']); ?></td>
                        <td><?= htmlspecialchars($buku['tahun']); ?></td>
                        <td><?= htmlspecialchars($buku['stok']); ?></td>
                        <td>
                            <?= !empty($buku['tanggal_ditambahkan']) 
                                ? date('d-m-Y H:i', strtotime($buku['tanggal_ditambahkan'])) 
                                : '-'; ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $buku['id']; ?>">Edit</a> | 
                            <a href="hapus.php?id=<?= $buku['id']; ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">Buku tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>