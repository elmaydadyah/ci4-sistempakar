<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Konsultasi - <?= esc($hasil['nama'] ?? 'StuntCare') ?></title>
    <link rel="shortcut icon" href="<?= base_url('assets/images/logo/logo.png') ?>">
    <style>
        :root {
            --ink: #20243b;
            --muted: #6f778d;
            --line: #e4e8f2;
            --primary: #4b49ac;
            --soft: #f6f8fc;
            --success: #35b98f;
            --warning: #d7941f;
            --danger: #df5c73;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #eef1f7;
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
        }

        .report-toolbar {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 18px;
        }

        .report-toolbar button,
        .report-toolbar a {
            display: inline-flex;
            align-items: center;
            min-height: 38px;
            padding: 0 15px;
            border: 0;
            border-radius: 8px;
            background: var(--primary);
            color: #ffffff;
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
        }

        .report-toolbar a {
            background: #ffffff;
            color: var(--primary);
            border: 1px solid var(--line);
        }

        .report-page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 24px;
            padding: 18mm;
            background: #ffffff;
            box-shadow: 0 18px 50px rgba(32, 36, 59, .14);
        }

        .report-header {
            display: grid;
            grid-template-columns: 72px 1fr;
            gap: 16px;
            align-items: center;
            padding-bottom: 16px;
            border-bottom: 3px solid var(--primary);
        }

        .report-header img {
            width: 68px;
            height: 68px;
            object-fit: contain;
        }

        .report-header h1 {
            margin: 0;
            color: var(--primary);
            font-size: 24px;
            line-height: 1.2;
        }

        .report-header p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .report-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin: 18px 0;
        }

        .meta-item,
        .summary-card,
        .section-box {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
        }

        .meta-item {
            padding: 10px 12px;
        }

        .meta-item span,
        .summary-card span {
            display: block;
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .meta-item strong,
        .summary-card strong {
            display: block;
            margin-top: 5px;
            color: var(--ink);
            font-size: 14px;
        }

        .summary-card {
            padding: 16px;
            background: var(--soft);
        }

        .summary-card h2 {
            margin: 6px 0 8px;
            color: var(--ink);
            font-size: 22px;
        }

        .status-badge {
            display: inline-flex;
            min-height: 30px;
            align-items: center;
            padding: 0 11px;
            border-radius: 999px;
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
        }

        .status-badge.success { background: var(--success); }
        .status-badge.warning { background: var(--warning); }
        .status-badge.danger { background: var(--danger); }

        .confidence {
            display: inline-flex;
            margin-left: 8px;
            min-height: 30px;
            align-items: center;
            padding: 0 11px;
            border-radius: 999px;
            color: var(--primary);
            background: #eef0ff;
            font-size: 12px;
            font-weight: 900;
        }

        .summary-card p {
            margin: 12px 0 0;
            color: #4e566b;
            font-size: 13px;
            line-height: 1.65;
        }

        .section-title {
            margin: 22px 0 10px;
            color: var(--primary);
            font-size: 15px;
            font-weight: 900;
        }

        .measure-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .section-box {
            padding: 12px;
        }

        .section-box span {
            display: block;
            color: var(--muted);
            font-size: 11px;
            font-weight: 900;
        }

        .section-box strong {
            display: block;
            margin-top: 6px;
            color: var(--ink);
            font-size: 15px;
        }

        .section-box small {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 11px;
        }

        .advice-box {
            padding: 14px 16px;
            border-radius: 8px;
            background: #effbf7;
            color: #315e53;
            font-size: 13px;
            line-height: 1.7;
        }

        .symptom-list {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .symptom-list span {
            display: inline-flex;
            min-height: 28px;
            align-items: center;
            padding: 0 9px;
            border-radius: 999px;
            background: #f5f7ff;
            color: #30375f;
            font-size: 11px;
            font-weight: 800;
        }

        .note {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid var(--line);
            color: var(--muted);
            font-size: 11px;
            line-height: 1.6;
        }

        .signature {
            display: grid;
            grid-template-columns: 1fr 220px;
            gap: 20px;
            margin-top: 24px;
        }

        .signature-box {
            text-align: center;
            color: var(--ink);
            font-size: 12px;
        }

        .signature-line {
            margin-top: 58px;
            border-top: 1px solid var(--ink);
            padding-top: 6px;
            font-weight: 800;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .report-toolbar {
                display: none;
            }

            .report-page {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 12mm;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <?php
        $diagnosa = $hasil['diagnosa'] ?? [];
        $kelas = $diagnosa['kelas'] ?? 'H1';
        $statusTone = match ($kelas) {
            'H1' => 'danger',
            'H2' => 'warning',
            default => 'success',
        };
        $friendlyLabel = match ($kelas) {
            'H1' => 'Perlu pemeriksaan lanjutan',
            'H2' => 'Risiko stunting rendah',
            default => 'Tidak memiliki risiko stunting',
        };
        $friendlyText = match ($kelas) {
            'H1' => 'Ada beberapa tanda risiko tinggi yang perlu segera dikonsultasikan kepada tenaga kesehatan.',
            'H2' => 'Ada tanda risiko rendah yang perlu diperhatikan dan dipantau secara berkala.',
            default => 'Hasil awal menunjukkan anak tidak memiliki risiko stunting, namun pemantauan rutin tetap penting.',
        };
    ?>

    <div class="report-toolbar">
        <button type="button" onclick="window.print()">Simpan PDF / Cetak</button>
        <a href="<?= base_url('konsultasi?hasil=' . ($hasil['id_hasil_diagnosa'] ?? '')) ?>">Kembali ke hasil</a>
    </div>

    <main class="report-page">
        <header class="report-header">
            <img src="<?= base_url('assets/images/logo/logo.png') ?>" alt="Logo StuntCare">
            <div>
                <h1>Laporan Hasil Konsultasi StuntCare</h1>
                <p>Skrining awal tumbuh kembang dan risiko stunting anak. Laporan ini membantu orang tua membawa ringkasan hasil ke posyandu, puskesmas, atau tenaga kesehatan.</p>
            </div>
        </header>

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

        <section class="summary-card">
            <span>Kesimpulan awal</span>
            <h2><?= esc($friendlyLabel) ?></h2>
            <div>
                <span class="status-badge <?= esc($statusTone, 'attr') ?>"><?= esc($diagnosa['kelas'] ?? '-') ?> - <?= esc($diagnosa['label'] ?? '-') ?></span>
                <span class="confidence">Keyakinan sistem <?= esc((string) ($diagnosa['posterior_persen'] ?? $hasil['persentase'] ?? 0)) ?>%</span>
            </div>
            <p><?= esc($friendlyText) ?></p>
        </section>

        <h3 class="section-title">Ringkasan Ukuran Anak</h3>
        <section class="measure-grid">
            <?php foreach ($hasil['zscore'] as $item): ?>
                <article class="section-box">
                    <span><?= esc($item['label']) ?></span>
                    <strong><?= esc($item['kategori']) ?></strong>
                    <small>Z-Score: <?= $item['nilai'] !== null ? esc((string) $item['nilai']) : '-' ?></small>
                </article>
            <?php endforeach; ?>
        </section>

        <h3 class="section-title">Rekomendasi</h3>
        <section class="advice-box">
            <?= esc($hasil['rekomendasi'] ?? '-') ?>
        </section>

        <h3 class="section-title">Tanda yang Terbaca Sistem</h3>
        <section class="symptom-list">
            <?php foreach (($hasil['gejala_terbaca'] ?? []) as $gejala): ?>
                <span><?= esc($gejala['nama'] ?? '-') ?></span>
            <?php endforeach; ?>
            <?php if (empty($hasil['gejala_terbaca'])): ?>
                <span>Tidak ada tanda khusus yang tersimpan.</span>
            <?php endif; ?>
        </section>

        <section class="signature">
            <div class="note">
                Catatan: Laporan ini adalah hasil skrining awal dari sistem dan bukan pengganti diagnosis dokter. Jika orang tua merasa khawatir atau anak menunjukkan keluhan, lakukan pemeriksaan langsung ke tenaga kesehatan.
            </div>
            <div class="signature-box">
                <div><?= esc($tanggal_cetak ?? date('d/m/Y H:i')) ?></div>
                <div class="signature-line">StuntCare</div>
            </div>
        </section>
    </main>
</body>
</html>
