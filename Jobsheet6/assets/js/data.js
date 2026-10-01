// OPSIONAL 2
async function muatDaftar(namaFile, daftarKunci) {
    const res = await fetch("../data/" + namaFile);
    if (!res.ok) {
        throw new Error("Gagal mengambil data (status " + res.status + ")");
    }

    const data = await res.json();
    return data.map(function (item) {
        return daftarKunci.map(function (kunci) {
            return item[kunci];
        });
    });
}
