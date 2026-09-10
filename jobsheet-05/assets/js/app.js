// ===== Hamburger menu (JS-driven, menggantikan checkbox hack) =====
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");
    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}

function initHapusConfirm() {
    document.querySelectorAll(".btn-hapus").forEach(function (btn) {
        btn.addEventListener("click", function () {
            const row = btn.closest("tr");
            const nama = row ? row.querySelector("td")?.textContent : "data ini";
            const yakin = confirm("Yakin ingin menghapus \"" + nama + "\"?");
            if (yakin && row) {
                row.remove();
                updateCounter();
            }
        });
    });
}
//jobsheet 5 no 4
function updateCounter() {
    const table = document.querySelector(".table-responsive table");
    if (!table) return;

    const rows = table.querySelectorAll("tbody tr");
    const visibleRows = Array.from(rows).filter(function (row) {
        return row.style.display !== "none";
    });

    const counterElem = document.querySelector("#counter-info");
    if (counterElem) {
        counterElem.textContent =
            "Menampilkan " + visibleRows.length + " dari " + rows.length + " buku";
    }
}

// ===== Filter/pencarian tabel real-time (kolom Judul saja) =====
function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(".table-responsive table");
    if (!input || !table) return;

    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr");
        rows.forEach(function (row) {
            const judul = row.querySelector("td")?.textContent.toLowerCase() || "";
            row.style.display = judul.includes(keyword) ? "" : "none";
        });
        updateCounter();
    });
}

// ===== Validasi form (client-side) =====
function tampilkanError(input, pesan) {
    hapusError(input);
    const span = document.createElement("span");
    span.className = "error";
    span.textContent = pesan;
    input.insertAdjacentElement("afterend", span);
}

function hapusError(input) {
    const next = input.nextElementSibling;
    if (next && next.classList.contains("error")) {
        next.remove();
    }
}

//jobsheet 5 no 5 
function initValidasiForm() {
    const form = document.getElementById("form-tambah");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;

        // 1. DAFTAR FIELD WAJIB DIISI 
        const requiredFields = ["judul", "pengarang"];

        // 2. PERULANGAN FOREACH 
        requiredFields.forEach(function (fieldName) {
            // Khusus field judul, mendukung name="judul" atau name="nama"
            const input = form.querySelector(`[name='${fieldName}'], [name='nama']`);
            
            if (input) {
                if (input.value.trim() === "") {
                    tampilkanError(input, `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} wajib diisi.`);
                    valid = false;
                } else {
                    hapusError(input);
                }
            }
        });

        // 3. VALIDASI KHUSUS DENGAN ATURAN ANGKA 
        const tahun = form.querySelector("[name='tahun']");
        if (tahun) {
            const nilai = parseInt(tahun.value, 10);
            if (isNaN(nilai) || nilai < 1900 || nilai > 2026) {
                tampilkanError(tahun, "Tahun harus di antara 1900-2026.");
                valid = false;
            } else {
                hapusError(tahun);
            }
        }

        const stok = form.querySelector("[name='stok']");
        if (stok) {
            const nilai = parseInt(stok.value, 10);
            if (isNaN(nilai) || nilai < 0) {
                tampilkanError(stok, "Stok tidak boleh negatif.");
                valid = false;
            } else {
                hapusError(stok);
            }
        }

        // Validasi ISBN jika ada (dari latihan No. 1)
        const isbn = form.querySelector("[name='isbn']");
        if (isbn && isbn.value.trim() !== "") {
            const patternIsbn = /^[0-9-]+$/;
            if (!patternIsbn.test(isbn.value.trim())) {
                tampilkanError(isbn, "ISBN hanya boleh berisi angka dan tanda hubung (-).");
                valid = false;
            } else {
                hapusError(isbn);
            }
        }

        if (!valid) {
            e.preventDefault();
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
    updateCounter();
});