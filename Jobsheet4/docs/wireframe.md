Wireframe
+--------------------------------+
|          SIMPUS-Mini           |
|--------------------------------|
|                                |
|    [ Registrasi Anggota ]      |
|                                |
|    Nama   : [____________]     |
|    Alamat : [____________]     |
|    No Telp: [____________]     |
|                                |
|          [ Daftar ]            |
+--------------------------------+

userflow
[Petugas Login]
      ↓
[Dashboard]
      ↓
[Menu Anggota]
      ↓
[Cari Anggota]
      ↓
[Pilih Anggota]
      ↓
[Cek Tunggakan]
      ↓
[Tampilkan Tunggakan]

identifikkasi edge case
Edge case: Petugas mencoba meminjamkan buku yang stoknya 0.

Penanganan: Sistem tidak menampilkan buku tersebut sebagai pilihan pada form peminjaman atau menolak proses peminjaman dan menampilkan pesan bahwa stok buku habis.