// Mengambil & menampilkan Daftar Buku secara asinkron dari data/buku.json
async function muatDaftarBuku() {
    const tbody = document.querySelector(".table-responsive table tbody");
    const loading = document.getElementById("loading-indicator");
    if (!tbody) return;

    loading.style.display = "block";
    tbody.innerHTML = "";

    try {
        // OPSIONAL 5
        await new Promise((resolve) => setTimeout(resolve, 3000));

        // OPSIONAL 2
        const daftarBuku = await muatDaftar("buku.json", ["judul", "pengarang", "tahun", "stok", "kategori"]);

        daftarBuku.forEach(function (buku) {
            const tr = document.createElement("tr");
            tr.innerHTML =
                "<td>" + buku[0] + "</td>" +
                "<td>" + buku[1] + "</td>" +
                "<td>" + buku[2] + "</td>" +
                "<td>" + buku[3] + "</td>" +
                "<td>" + buku[4] + "</td>" +
                "<td>" +
                "<button type=\"button\">Edit</button> " +
                "<button type=\"button\" class=\"btn-hapus\">Hapus</button>" +
                "</td>";
            tbody.appendChild(tr);
        });
    } catch (err) {
        tbody.innerHTML =
            "<tr><td colspan=\"5\">Gagal memuat data: " + err.message + "</td></tr>";
    } finally {
        loading.style.display = "none";
    }
}

// OPSIONAL 1: Tombol Muat Ulang memanggil ulang muatDaftarBuku().
document.addEventListener("DOMContentLoaded", function () {
    muatDaftarBuku();

    const btnMuatUlang = document.getElementById("btn-muat-ulang");
    if (btnMuatUlang) {
        btnMuatUlang.addEventListener("click", muatDaftarBuku);
    }
});