<?php
$page_title = "Tambah Anggota";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
        <section>
            <h2>Tambah Anggota</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form id="form-tambah" method="post" action="proses_tambah.php">
                <p>
                    <label for="nama">Nama</label><br>
                    <input type="text" id="nama" name="nama" required>
                </p>
                <p>
                    <label for="no_anggota">No. Anggota</label><br>
                    <input type="text" id="no_anggota" name="no_anggota" required>
                </p>
                <p>
                    <label for="alamat">Alamat</label><br>
                    <input type="text" id="alamat" name="alamat">
                </p>
                <p>
                    <label for="no_hp">No. HP</label><br>
                    <input type="text" id="no_hp" name="no_hp">
                </p>
                <p>
                    <button type="submit">Simpan</button>
                </p>
            </form>
        </section>

//no 2 
<?php include __DIR__ . '/../includes/footer.php'; ?>
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = trim($_POST['nama'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');

    // 1. Validasi Field Wajib
    if (empty($nama) || empty($email)) {
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => 'Nama dan Email wajib diisi!'
        ];
        header('Location: tambah.php');
        exit;
    }

    // 2. Validasi Panjang Nama
    if (strlen($nama) < 3) {
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => 'Nama minimal harus terdiri dari 3 karakter.'
        ];
        header('Location: tambah.php');
        exit;
    }

    // 3. Validasi Format Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => 'Format email tidak valid.'
        ];
        header('Location: tambah.php');
        exit;
    }

    // 4. Validasi Angka untuk Nomor Telepon (Opsional)
    if (!empty($telepon) && !is_numeric($telepon)) {
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => 'Nomor telepon hanya boleh berisi angka.'
        ];
        header('Location: tambah.php');
        exit;
    }

    // Jika validasi lolos, simpan ke session
    $_SESSION['anggota'][] = [
        'nama'    => $nama,
        'email'   => $email,
        'telepon' => $telepon
    ];

    $_SESSION['flash'] = [
        'type'    => 'success',
        'message' => 'Anggota berhasil ditambahkan!'
    ];

    header('Location: list.php');
    exit;
}