<!DOCTYPE html>
<html lang="id">
<head>
    <?php
        $isDownloadMode = !empty($download_mode);
        $logoKabBogor = base_url('assets/images/logo/logokabbogor.png');
        $logoPuskesmas = base_url('assets/images/logo/logo_puskesmas.png');

        if ($isDownloadMode) {
            $logoKabBogorPath = FCPATH . 'assets/images/logo/logokabbogor.png';
            $logoPuskesmasPath = FCPATH . 'assets/images/logo/logo_puskesmas.png';
            $logoKabBogor = is_file($logoKabBogorPath) ? 'data:image/png;base64,' . base64_encode((string) file_get_contents($logoKabBogorPath)) : $logoKabBogor;
            $logoPuskesmas = is_file($logoPuskesmasPath) ? 'data:image/png;base64,' . base64_encode((string) file_get_contents($logoPuskesmasPath)) : $logoPuskesmas;
        }
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Konsultasi - <?= esc($hasil['nama'] ?? 'SiPASTI') ?></title>
    <link rel="shortcut icon" href="<?= base_url('assets/images/logo/logo_puskesmas.png') ?>">
    <?php if ($isDownloadMode): ?>
        <style><?= file_get_contents(FCPATH . 'assets/css/laporan.css') ?></style>
    <?php else: ?>
        <link rel="stylesheet" href="<?= base_url('assets/css/laporan.css?v=' . filemtime(FCPATH . 'assets/css/laporan.css')) ?>">
    <?php endif; ?>
</head>
<body class="<?= $isDownloadMode ? 'pdf-download' : '' ?>">
    <?php
        $diagnosa = $hasil['diagnosa'] ?? [];
        $kelas = $diagnosa['kelas'] ?? 'H1';
        $statusTone = match ($kelas) {
            'H1' => 'danger',
            'H2' => 'warning',
            default => 'success',
        };
        $friendlyLabel = match ($kelas) {
            'H1' => 'Risiko stunting tinggi',
            'H2' => 'Risiko stunting sedang',
            default => 'Risiko stunting rendah',
        };
        $friendlyText = match ($kelas) {
            'H1' => 'Ada tanda risiko tinggi. Segera lakukan pemeriksaan lanjutan ke posyandu atau puskesmas.',
            'H2' => 'Ada tanda risiko sedang. Perbaiki kualitas makan anak dan lakukan pemantauan lanjutan di posyandu atau puskesmas.',
            default => 'Ada tanda risiko rendah yang perlu diperhatikan dan dipantau secara berkala.',
        };
        $interpretationText = match ($kelas) {
            'H1' => 'Risiko stunting tinggi berarti hasil pengukuran dan tanda yang terbaca lebih banyak mengarah pada kemungkinan stunting, sehingga anak perlu segera diperiksa lebih lanjut oleh tenaga kesehatan.',
            'H2' => 'Risiko stunting sedang berarti terdapat beberapa tanda yang perlu diwaspadai, tetapi belum sekuat kategori tinggi. Anak perlu dipantau pertumbuhannya dan diperbaiki asupan gizinya.',
            default => 'Risiko stunting rendah berarti anak tidak terindikasi memiliki risiko stunting tinggi. Jika nilai keyakinan sistem berada di bawah 70%, hasil ini dibaca sebagai tidak terindikasi risiko stunting, namun pemantauan rutin tetap diperlukan.',
        };
    ?>

    <?php if (!$isDownloadMode): ?>
        <div class="report-toolbar">
            <button type="button" onclick="window.print()">Simpan PDF / Cetak</button>
            <a href="<?= base_url('konsultasi/laporan/download/' . ($hasil['id_hasil_diagnosa'] ?? 0)) ?>">Download Laporan</a>
            <a href="<?= base_url('konsultasi?hasil=' . ($hasil['id_hasil_diagnosa'] ?? '')) ?>">Kembali ke hasil</a>
        </div>
    <?php endif; ?>

    <main class="report-page">
        <?php if ($isDownloadMode): ?>
            <table class="pdf-report-header">
                <tr>
                    <td class="pdf-logo-cell"><img src="<?= esc($logoKabBogor, 'attr') ?>" alt="Logo Kabupaten Bogor"></td>
                    <td class="pdf-header-copy">
                        <h1>PUSKESMAS KECAMATAN CILEUNGSI</h1>
                        <div>Jl. Camat Enjan No. 1, Cileungsi, Bogor 16820</div>
                        <div>Telp. (021) 8230348 | puskesmascileungsi@yahoo.co.id</div>
                    </td>
                    <td class="pdf-logo-cell"><img src="<?= esc($logoPuskesmas, 'attr') ?>" alt="Logo Puskesmas"></td>
                </tr>
            </table>
        <?php else: ?>
            <header class="report-header">
                <img src="<?= esc($logoKabBogor, 'attr') ?>" alt="Logo Kabupaten Bogor">
                <div class="header-copy">
                    <h1>PUSKESMAS KECAMATAN CILEUNGSI</h1>
                    <div class="header-contact">
                        <span>Jl. Camat Enjan No. 1, Cileungsi, Bogor 16820</span>
                        <span>Telp. (021) 8230348 | puskesmascileungsi@yahoo.co.id</span>
                    </div>
                </div>
                <img src="<?= esc($logoPuskesmas, 'attr') ?>" alt="Logo Puskesmas">
            </header>
        <?php endif; ?>

        <div class="report-document-title">LAPORAN HASIL DIAGNOSA</div>

        <?php if ($isDownloadMode): ?>
            <table class="pdf-report-meta">
                <tr>
                    <td>
                        <span>Nama Anak</span>
                        <strong><?= esc($hasil['nama'] ?? '-') ?></strong>
                    </td>
                    <td>
                        <span>Umur</span>
                        <strong><?= esc((string) ($hasil['umur'] ?? 0)) ?> bulan</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>Jenis Kelamin</span>
                        <strong><?= esc(($row['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : (($row['jenis_kelamin'] ?? '') === 'P' ? 'Perempuan' : '-')) ?></strong>
                    </td>
                    <td>
                        <span>Tanggal Laporan</span>
                        <strong><?= esc($tanggal_cetak ?? date('d/m/Y H:i')) ?></strong>
                    </td>
                </tr>
            </table>
        <?php else: ?>
            <section class="report-meta">
                <div class="meta-item">
                    <span>Nama Anak</span>
                    <strong><?= esc($hasil['nama'] ?? '-') ?></strong>
                </div>
                <div class="meta-item">
                    <span>Umur</span>
                    <strong><?= esc((string) ($hasil['umur'] ?? 0)) ?> bulan</strong>
                </div>
                <div class="meta-item">
                    <span>Jenis Kelamin</span>
                    <strong><?= esc(($row['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : (($row['jenis_kelamin'] ?? '') === 'P' ? 'Perempuan' : '-')) ?></strong>
                </div>
                <div class="meta-item">
                    <span>Tanggal Laporan</span>
                    <strong><?= esc($tanggal_cetak ?? date('d/m/Y H:i')) ?></strong>
                </div>
            </section>
        <?php endif; ?>

        <section class="summary-card">
            <span>Kesimpulan awal</span>
            <h2><?= esc($friendlyLabel) ?></h2>
            <div class="summary-badges">
                <span class="status-badge <?= esc($statusTone, 'attr') ?>"><?= esc($diagnosa['kelas'] ?? '-') ?> - <?= esc($diagnosa['label'] ?? '-') ?></span>
                <span class="confidence">Keyakinan sistem <?= esc((string) ($diagnosa['posterior_persen'] ?? $hasil['persentase'] ?? 0)) ?>%</span>
            </div>
            <p><?= esc($friendlyText) ?></p>
            <p><?= esc($interpretationText) ?></p>
        </section>

        <h3 class="section-title">Ringkasan Ukuran Anak</h3>
        <?php if ($isDownloadMode): ?>
            <table class="pdf-measure-grid">
                <tr>
                    <?php foreach ($hasil['zscore'] as $item): ?>
                        <td>
                            <span><?= esc($item['label']) ?></span>
                            <strong><?= esc($item['kategori']) ?></strong>
                            <small>Z-Score: <?= $item['nilai'] !== null ? esc((string) $item['nilai']) : '-' ?></small>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </table>
        <?php else: ?>
            <section class="measure-grid">
                <?php foreach ($hasil['zscore'] as $item): ?>
                    <article class="section-box">
                        <span><?= esc($item['label']) ?></span>
                        <strong><?= esc($item['kategori']) ?></strong>
                        <small>Z-Score: <?= $item['nilai'] !== null ? esc((string) $item['nilai']) : '-' ?></small>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
        <section class="zscore-source-note">
            <strong>Sumber perhitungan Z-Score</strong>
            <p>Hasil Z-Score pada laporan ini dihitung berdasarkan tabel standar antropometri anak yang mengacu pada Peraturan Menteri Kesehatan Republik Indonesia Nomor 2 Tahun 2020.</p>
        </section>

        <h3 class="section-title">Rekomendasi</h3>
        <section class="advice-box">
            <?= esc($hasil['rekomendasi'] ?? '-') ?>
        </section>

        <?php if ($isDownloadMode): ?>
            <table class="pdf-signature">
                <tr>
                    <td class="note">
                        Catatan: Laporan ini adalah hasil skrining awal dari sistem dan bukan pengganti diagnosis dokter. Jika orang tua merasa khawatir atau anak menunjukkan keluhan, lakukan pemeriksaan langsung ke tenaga kesehatan.
                    </td>
                    <td class="signature-box">
                        <div><?= esc($tanggal_cetak ?? date('d/m/Y H:i')) ?></div>
                        <div class="signature-line">SiPASTI</div>
                    </td>
                </tr>
            </table>
        <?php else: ?>
            <section class="signature">
                <div class="note">
                    Catatan: Laporan ini adalah hasil skrining awal dari sistem dan bukan pengganti diagnosis dokter. Jika orang tua merasa khawatir atau anak menunjukkan keluhan, lakukan pemeriksaan langsung ke tenaga kesehatan.
                </div>
                <div class="signature-box">
                    <div><?= esc($tanggal_cetak ?? date('d/m/Y H:i')) ?></div>
                    <div class="signature-line">SiPASTI</div>
                </div>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
