<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi StuntCare</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/landing.css?v=3') ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/images/logo/logo.png') ?>">
</head>
<body>
    <main class="landing-shell consult-shell">
        <img class="consult-bg-image" src="<?= base_url('assets/images/landing/foto.png') ?>" alt="" aria-hidden="true">

        <?= $this->include('layout/landing/navbar') ?>

        <section class="consult-hero" aria-label="Form konsultasi stunting">
            <div class="consult-heading">
                <span class="consult-kicker">Konsultasi Awal</span>
                <h1>Isi data balita dan pilih gejala yang terlihat.</h1>
                <p>Hasil ini hanya rekomendasi awal dari sistem pakar. Pemeriksaan langsung tetap diperlukan untuk keputusan medis.</p>
            </div>

            <div class="consult-layout">
                <form class="consult-form" action="<?= base_url('/konsultasi') ?>" method="post">
                    <?= csrf_field() ?>

                    <?php if (!empty($errors)): ?>
                        <div class="consult-alert">
                            <?php foreach ($errors as $error): ?>
                                <p><?= esc($error) ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="identity-grid">
                        <label>
                            <span>Nama Balita</span>
                            <input type="text" name="nama" value="<?= esc($old['nama'] ?? '', 'attr') ?>" placeholder="Contoh: Andi" required>
                        </label>
                        <label>
                            <span>Umur</span>
                            <div class="input-with-unit">
                                <input type="number" name="umur" value="<?= esc($old['umur'] ?? '', 'attr') ?>" min="0" max="60" placeholder="24" required>
                                <b>bulan</b>
                            </div>
                        </label>
                    </div>

                    <div class="symptom-section">
                        <div>
                            <span class="section-label">Gejala</span>
                            <h2>Pilih kondisi yang sesuai</h2>
                        </div>
                        <small><?= count($gejala ?? []) ?> gejala tersedia</small>
                    </div>

                    <div class="symptom-grid">
                        <?php if (!empty($gejala)): ?>
                            <?php foreach ($gejala as $item): ?>
                                <?php $idGejala = (string) $item['id_gejala']; ?>
                                <label class="symptom-card">
                                    <input
                                        type="checkbox"
                                        name="gejala[]"
                                        value="<?= esc($idGejala, 'attr') ?>"
                                        <?= in_array($idGejala, $old['gejala'] ?? [], true) ? 'checked' : '' ?>
                                    >
                                    <span></span>
                                    <b><?= esc($item['nama_gejala']) ?></b>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">Data gejala belum tersedia.</div>
                        <?php endif; ?>
                    </div>

                    <button class="btn-submit-consult" type="submit">Lihat Hasil Konsultasi</button>
                </form>

                <aside class="result-panel" aria-label="Hasil konsultasi">
                    <?php if (!empty($hasil)): ?>
                        <?php $diagnosa = $hasil['diagnosa']; ?>
                        <span class="result-label">Hasil sementara</span>
                        <h2><?= esc($hasil['nama']) ?></h2>
                        <div class="result-meta">
                            <span><?= esc((string) $hasil['umur']) ?> bulan</span>
                            <span><?= esc((string) $hasil['jumlah_gejala']) ?> gejala dipilih</span>
                        </div>

                        <?php if (!empty($diagnosa) && ($diagnosa['persentase'] ?? 0) > 0): ?>
                            <div class="score-ring">
                                <strong><?= esc((string) $diagnosa['persentase']) ?>%</strong>
                                <small>Kecocokan</small>
                            </div>
                            <h3><?= esc($diagnosa['kasus']['nama_kasus'] ?? 'Diagnosa belum tersedia') ?></h3>
                            <p><?= esc($diagnosa['kasus']['deskripsi'] ?? 'Data deskripsi belum tersedia.') ?></p>
                            <div class="solution-box">
                                <b>Saran awal</b>
                                <p><?= esc($diagnosa['kasus']['solusi'] ?? 'Konsultasikan hasil ini dengan petugas kesehatan.') ?></p>
                            </div>
                        <?php else: ?>
                            <div class="score-ring muted-ring">
                                <strong>0%</strong>
                                <small>Kecocokan</small>
                            </div>
                            <h3>Belum ada kecocokan aturan</h3>
                            <p>Gejala sudah tercatat, tetapi basis aturan belum cukup untuk menentukan kemungkinan kasus.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="result-label">Ringkasan</span>
                        <h2>Hasil akan muncul di sini.</h2>
                        <p>Isi nama, umur, dan gejala yang sesuai untuk melihat analisis awal dari sistem pakar.</p>
                        <div class="consult-steps">
                            <span>1. Data balita</span>
                            <span>2. Pilih gejala</span>
                            <span>3. Lihat hasil</span>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </section>
    </main>
</body>
</html>
