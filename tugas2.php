<?php
declare(strict_types=1);

/**
 * Tugas 2: Penerapan Konsep Pewarisan Sederhana (Class Pegawai)
 * Mata Kuliah: Pemrograman Berorientasi Objek (Praktikum 06)
 * Mahasiswa  : Muhamad Iqbal Kurniawan
 */

// =============================================================================
// 1. SUPERCLASS / PARENT CLASS: Pegawai
// =============================================================================
abstract class Pegawai {
    // Enkapsulasi properti identitas umum pegawai (private)
    private string $id;
    private string $nama;

    /**
     * Konstruktor Parent Pegawai
     */
    public function __construct(string $id, string $nama) {
        $idBersih   = trim($id);
        $namaBersih = trim($nama);

        if ($idBersih === '') {
            throw new InvalidArgumentException("ID pegawai wajib diisi (tidak boleh kosong).");
        }
        if ($namaBersih === '') {
            throw new InvalidArgumentException("Nama pegawai wajib diisi (tidak boleh kosong).");
        }

        $this->id   = $idBersih;
        $this->nama = $namaBersih;
    }

    // Accessor untuk membaca ID Pegawai
    public function getId(): string {
        return $this->id;
    }

    // Accessor untuk membaca Nama Pegawai
    public function getNama(): string {
        return $this->nama;
    }

    /**
     * Method abstrak yang di-override oleh tiap subclass dengan logika hitung gaji yang spesifik.
     */
    abstract public function hitungGaji(): int;

    /**
     * Mendapatkan kategori status kepegawaian
     */
    abstract public function getJenisPegawai(): string;
}

// =============================================================================
// 2. SUBCLASS 1: PegawaiTetap (Mewarisi Pegawai)
// =============================================================================
class PegawaiTetap extends Pegawai {
    // Properti khusus untuk pegawai bertipe tetap
    private int $gajiPokok;
    private int $tunjanganMakan;

    /**
     * Konstruktor PegawaiTetap
     * Mengalirkan id dan nama ke parent::__construct()
     */
    public function __construct(string $id, string $nama, int $gajiPokok, int $tunjanganMakan = 500000) {
        // Delegasi inisialisasi identitas ke konstruktor superclass
        parent::__construct($id, $nama);

        // Validasi batasan nilai agar tidak negatif
        if ($gajiPokok < 0) {
            throw new InvalidArgumentException("Nilai gaji pokok tidak boleh negatif (input: {$gajiPokok}).");
        }
        if ($tunjanganMakan < 0) {
            throw new InvalidArgumentException("Nilai tunjangan makan tidak boleh negatif (input: {$tunjanganMakan}).");
        }

        $this->gajiPokok      = $gajiPokok;
        $this->tunjanganMakan = $tunjanganMakan;
    }

    public function getGajiPokok(): int {
        return $this->gajiPokok;
    }

    public function getTunjanganMakan(): int {
        return $this->tunjanganMakan;
    }

    /**
     * Override hitungGaji() untuk PegawaiTetap:
     * Rumus: Gaji Pokok + Tunjangan Makan
     */
    public function hitungGaji(): int {
        return $this->gajiPokok + $this->tunjanganMakan;
    }

    public function getJenisPegawai(): string {
        return 'Pegawai Tetap';
    }
}

// =============================================================================
// 3. SUBCLASS 2: PegawaiKontrak (Mewarisi Pegawai)
// =============================================================================
class PegawaiKontrak extends Pegawai {
    // Properti khusus untuk pegawai dengan sistem kontrak
    private int $upahPerJam;
    private int $jamKerja;

    /**
     * Konstruktor PegawaiKontrak
     * Mengalirkan id dan nama ke parent::__construct()
     */
    public function __construct(string $id, string $nama, int $upahPerJam, int $jamKerja = 160) {
        // Panggil konstruktor parent
        parent::__construct($id, $nama);

        // Validasi input non-negatif
        if ($upahPerJam < 0) {
            throw new InvalidArgumentException("Besaran upah per jam tidak boleh negatif (input: {$upahPerJam}).");
        }
        if ($jamKerja < 0) {
            throw new InvalidArgumentException("Jumlah jam kerja tidak boleh negatif (input: {$jamKerja}).");
        }

        $this->upahPerJam = $upahPerJam;
        $this->jamKerja   = $jamKerja;
    }

    public function getUpahPerJam(): int {
        return $this->upahPerJam;
    }

    public function getJamKerja(): int {
        return $this->jamKerja;
    }

    /**
     * Override hitungGaji() untuk PegawaiKontrak:
     * Rumus: Upah Per Jam x Total Jam Kerja
     */
    public function hitungGaji(): int {
        return $this->upahPerJam * $this->jamKerja;
    }

    public function getJenisPegawai(): string {
        return 'Pegawai Kontrak';
    }
}

// =============================================================================
// 4. INSTANSIASI OBJEK & PENGUJIAN LOGIKA SISTEM
// =============================================================================

// Instansiasi Objek 1: Pegawai Tetap
$pegawaiTetap = new PegawaiTetap(
    id: 'PT-101',
    nama: 'Muhamad Iqbal Kurniawan',
    gajiPokok: 6500000,
    tunjanganMakan: 750000
);

// Instansiasi Objek 2: Pegawai Kontrak
$pegawaiKontrak = new PegawaiKontrak(
    id: 'PK-202',
    nama: 'Fajar Nugraha Pratama',
    upahPerJam: 45000,
    jamKerja: 160
);

// Array penampung polimorfisme turunan Pegawai
/** @var Pegawai[] $daftarPegawai */
$daftarPegawai = [$pegawaiTetap, $pegawaiKontrak];

// Pengujian Kasus Batas (Exception Handling dengan Try-Catch)
$kasusValidasi = [];

try {
    new PegawaiTetap('PT-999', 'Uji Coba Gagal 1', -3000000);
} catch (InvalidArgumentException $e) {
    $kasusValidasi[] = [
        'skenario' => 'Instansiasi PegawaiTetap dengan Gaji Pokok Negatif (-3.000.000)',
        'status'   => 'BERHASIL DITOLAK (Aman)',
        'pesan'    => $e->getMessage()
    ];
}

try {
    new PegawaiKontrak('PK-999', 'Uji Coba Gagal 2', -50000, 160);
} catch (InvalidArgumentException $e) {
    $kasusValidasi[] = [
        'skenario' => 'Instansiasi PegawaiKontrak dengan Upah Negatif (-50.000)',
        'status'   => 'BERHASIL DITOLAK (Aman)',
        'pesan'    => $e->getMessage()
    ];
}

try {
    new PegawaiKontrak('PK-998', 'Uji Coba Gagal 3', 45000, -20);
} catch (InvalidArgumentException $e) {
    $kasusValidasi[] = [
        'skenario' => 'Instansiasi PegawaiKontrak dengan Jam Kerja Negatif (-20)',
        'status'   => 'BERHASIL DITOLAK (Aman)',
        'pesan'    => $e->getMessage()
    ];
}

// Fungsi pembantu format mata uang rupiah
function formatRp(int $angka): string {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Cek jika dijalankan melalui CLI (Terminal)
if (php_sapi_name() === 'cli') {
    echo "====================================================================\n";
    echo "  TUGAS 2: IMPLEMENTASI PEWARISAN KELAS PEGAWAI (PBO)\n";
    echo "  Oleh: Muhamad Iqbal Kurniawan\n";
    echo "====================================================================\n\n";

    foreach ($daftarPegawai as $idx => $p) {
        $no = $idx + 1;
        echo "[Objek {$no}: {$p->getJenisPegawai()}]\n";
        echo "  ID Pegawai   : " . $p->getId() . "\n";
        echo "  Nama Pegawai : " . $p->getNama() . "\n";

        if ($p instanceof PegawaiTetap) {
            echo "  Gaji Pokok   : " . formatRp($p->getGajiPokok()) . "\n";
            echo "  Tunjangan    : " . formatRp($p->getTunjanganMakan()) . "\n";
            echo "  Perhitungan  : Gaji Pokok + Tunjangan\n";
        } elseif ($p instanceof PegawaiKontrak) {
            echo "  Upah/Jam     : " . formatRp($p->getUpahPerJam()) . " / jam\n";
            echo "  Jam Kerja    : " . $p->getJamKerja() . " jam\n";
            echo "  Perhitungan  : Upah/Jam x Jam Kerja\n";
        }

        echo "  Total Gaji   : " . formatRp($p->hitungGaji()) . "\n";
        echo "--------------------------------------------------------------------\n";
    }

    echo "\n[PENGUJIAN VALIDASI NILAI NON-NEGATIF (EXCEPTION HANDLING)]:\n";
    foreach ($kasusValidasi as $k) {
        echo "  - Skenario : " . $k['skenario'] . "\n";
        echo "    Hasil    : " . $k['status'] . " -> " . $k['pesan'] . "\n";
    }
    echo "\nEksekusi script berhasil dan selesai.\n";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas 2 - Implementasi Pewarisan Objek (Class Pegawai)</title>
    <style>
    :root {
        --bg-color: #0b0d10;
        --panel-bg: #14171d;
        --card-bg: #181c24;
        --accent-cyan: #00f2fe;
        --accent-blue: #4facfe;
        --accent-green: #00e676;
        --accent-purple: #9d4edd;
        --accent-red: #ff5252;
        --text-main: #e2e8f0;
        --text-muted: #8a99ad;
        --border-color: #232936;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        background-color: var(--bg-color);
        color: var(--text-main);
        font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        padding: 2rem 1rem;
        min-height: 100vh;
    }

    .container {
        max-width: 960px;
        margin: 0 auto;
    }

    .nav-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--accent-cyan);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        transition: transform 0.2s;
    }

    .nav-back:hover {
        transform: translateX(-4px);
    }

    .header {
        background: linear-gradient(135deg, rgba(20, 23, 29, 0.95), rgba(24, 28, 36, 0.95));
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
    }

    .badge-tugas {
        display: inline-block;
        background: rgba(0, 242, 254, 0.12);
        color: var(--accent-cyan);
        border: 1px solid rgba(0, 242, 254, 0.3);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
    }

    .header h1 {
        font-size: 1.85rem;
        background: linear-gradient(135deg, #ffffff, #a5b4fc);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }

    .header p {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .grid-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .card-pegawai {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.75rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
        transition: all 0.3s ease;
    }

    .card-pegawai:hover {
        transform: translateY(-4px);
        border-color: rgba(0, 242, 254, 0.4);
        box-shadow: 0 16px 36px rgba(0, 242, 254, 0.1);
    }

    .card-pegawai::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    .card-tetap::before {
        background: linear-gradient(90deg, #00e676, #00f2fe);
    }

    .card-kontrak::before {
        background: linear-gradient(90deg, #ff9100, #ff5252);
    }

    .card-tag {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        margin-bottom: 1rem;
        text-transform: uppercase;
    }

    .tag-tetap {
        background: rgba(0, 230, 118, 0.15);
        color: var(--accent-green);
        border: 1px solid rgba(0, 230, 118, 0.3);
    }

    .tag-kontrak {
        background: rgba(255, 145, 0, 0.15);
        color: #ffaa00;
        border: 1px solid rgba(255, 145, 0, 0.3);
    }

    .pegawai-nama {
        font-size: 1.35rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.25rem;
    }

    .pegawai-id {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-family: 'Consolas', monospace;
        margin-bottom: 1.25rem;
    }

    .info-list {
        list-style: none;
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
        padding: 1rem 0;
        margin-bottom: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
    }

    .info-label {
        color: var(--text-muted);
    }

    .info-val {
        font-weight: 600;
        color: #ffffff;
    }

    .total-box {
        background: rgba(0, 0, 0, 0.25);
        border: 1px dashed var(--border-color);
        border-radius: 10px;
        padding: 1rem;
        text-align: right;
    }

    .total-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .total-nominal {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--accent-cyan);
    }

    .section-title {
        font-size: 1.25rem;
        margin-bottom: 1rem;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .validation-card {
        background-color: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .table-val {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.88rem;
    }

    .table-val th {
        text-align: left;
        padding: 0.75rem 1rem;
        background: rgba(0, 0, 0, 0.3);
        color: var(--text-muted);
        font-weight: 600;
        border-bottom: 1px solid var(--border-color);
    }

    .table-val td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .badge-success {
        background: rgba(0, 230, 118, 0.15);
        color: var(--accent-green);
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .code-snippet {
        font-family: 'Consolas', monospace;
        color: #f87171;
        font-size: 0.83rem;
    }

    .footer {
        text-align: center;
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 2rem;
        padding-bottom: 1rem;
    }
    </style>
</head>

<body>

    <div class="container">
        <a href="index.php" class="nav-back">&larr; Kembali ke Portal Praktikum 06</a>

        <div class="header">
            <span class="badge-tugas">Praktikum 06 &bull; Tugas 2 Mandiri</span>
            <h1>Implementasi Pewarisan Objek (Class Pegawai)</h1>
            <p>
                Praktik pemrograman berorientasi objek yang memodelkan relasi pewarisan (<em>is-a</em>)
                menggunakan superclass <code>Pegawai</code> dan dua subclass yaitu <code>PegawaiTetap</code> serta <code>PegawaiKontrak</code>.
                Menerapkan <code>parent::__construct()</code>, pengecekan nilai non-negatif, dan method polimorfis <code>hitungGaji()</code>.
            </p>
        </div>

        <h2 class="section-title">Output Objek Pegawai (Hasil Implementasi Overriding hitungGaji)</h2>
        <div class="grid-cards">
            <!-- Objek 1: Pegawai Tetap -->
            <div class="card-pegawai card-tetap">
                <span class="card-tag tag-tetap"><?= htmlspecialchars($pegawaiTetap->getJenisPegawai()) ?></span>
                <div class="pegawai-nama"><?= htmlspecialchars($pegawaiTetap->getNama()) ?></div>
                <div class="pegawai-id">ID: <?= htmlspecialchars($pegawaiTetap->getId()) ?> &bull; Mewarisi Superclass
                </div>

                <ul class="info-list">
                    <li class="info-item">
                        <span class="info-label">Gaji Pokok:</span>
                        <span class="info-val"><?= formatRp($pegawaiTetap->getGajiPokok()) ?></span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Tunjangan Makan:</span>
                        <span class="info-val"><?= formatRp($pegawaiTetap->getTunjanganMakan()) ?></span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Rumus Perhitungan:</span>
                        <span class="info-val" style="color: var(--accent-green)">Gaji Pokok + Tunjangan</span>
                    </li>
                </ul>

                <div class="total-box">
                    <div class="total-label">Total Gaji Akhir</div>
                    <div class="total-nominal"><?= formatRp($pegawaiTetap->hitungGaji()) ?></div>
                </div>
            </div>

            <!-- Objek 2: Pegawai Kontrak -->
            <div class="card-pegawai card-kontrak">
                <span class="card-tag tag-kontrak"><?= htmlspecialchars($pegawaiKontrak->getJenisPegawai()) ?></span>
                <div class="pegawai-nama"><?= htmlspecialchars($pegawaiKontrak->getNama()) ?></div>
                <div class="pegawai-id">ID: <?= htmlspecialchars($pegawaiKontrak->getId()) ?> &bull; Mewarisi Superclass
                </div>

                <ul class="info-list">
                    <li class="info-item">
                        <span class="info-label">Upah per Jam:</span>
                        <span class="info-val"><?= formatRp($pegawaiKontrak->getUpahPerJam()) ?> / jam</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Total Jam Kerja:</span>
                        <span class="info-val"><?= htmlspecialchars((string)$pegawaiKontrak->getJamKerja()) ?> Jam / Bulan</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Rumus Perhitungan:</span>
                        <span class="info-val" style="color: #ffaa00">Upah/Jam &times; Jam Kerja</span>
                    </li>
                </ul>

                <div class="total-box">
                    <div class="total-label">Total Gaji Akhir</div>
                    <div class="total-nominal" style="color: #ffaa00;"><?= formatRp($pegawaiKontrak->hitungGaji()) ?></div>
                </div>
            </div>
        </div>

        <h2 class="section-title">Verifikasi Penanganan Input: Validasi Angka Non-Negatif</h2>
        <div class="validation-card">
            <table class="table-val">
                <thead>
                    <tr>
                        <th>Skenario Uji Nilai Invalid</th>
                        <th>Status Pertahanan</th>
                        <th>Pesan Tangkapan Exception</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kasusValidasi as $val): ?>
                    <tr>
                        <td style="font-weight: 500; color: #ffffff;"><?= htmlspecialchars($val['skenario']) ?></td>
                        <td><span class="badge-success"><?= htmlspecialchars($val['status']) ?></span></td>
                        <td><span class="code-snippet"><?= htmlspecialchars($val['pesan']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="footer">
            Praktikum PBO 06 &bull; Teknik Informatika &bull; Muhamad Iqbal Kurniawan
        </div>
    </div>

</body>

</html>
