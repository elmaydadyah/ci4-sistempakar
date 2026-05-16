<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi StuntCare</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/landing.css?v=' . filemtime(FCPATH . 'assets/css/landing.css')) ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/images/logo/logo.png') ?>">
</head>
<body>
    <?= $this->include('layout/landing/navbar') ?>

    <main class="landing-shell consult-shell">
        <img class="consult-bg-image" src="<?= base_url('assets/images/landing/foto.png') ?>" alt="" aria-hidden="true">

        <section class="consult-hero" aria-label="Form konsultasi stunting">
            <div class="consult-heading">
                <span class="consult-kicker">Cek Tumbuh Kembang</span>
                <h1>Ceritakan kondisi anak, nanti StuntCare bantu membaca hasilnya.</h1>
                <p>Isi data yang biasa ada di buku KIA atau catatan posyandu. Hasil ini adalah pemeriksaan awal, bukan pengganti diagnosis dokter.</p>
                <div class="parent-steps" aria-label="Langkah konsultasi">
                    <span><b>1</b> Isi data anak</span>
                    <span><b>2</b> Masukkan ukuran tubuh</span>
                    <span><b>3</b> Lihat saran awal</span>
                </div>
            </div>

            <div class="consult-layout wide">
                <form class="consult-form" action="<?= base_url('/konsultasi') ?>" method="post">
                    <?= csrf_field() ?>

                    <?php if (!empty($errors)): ?>
                        <div class="consult-alert">
                            <?php foreach ($errors as $error): ?>
                                <p><?= esc($error) ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="friendly-panel">
                        <strong>Sebelum mulai</strong>
                        <p>Siapkan umur anak dalam bulan, berat badan, dan tinggi badan terakhir. Kalau belum tahu lingkar lengan atau lingkar kepala, boleh dikosongkan.</p>
                    </div>

                    <div class="form-section-title mt-0">
                        <div>
                            <span class="section-label">Bagian 1</span>
                            <h2>Data anak</h2>
                            <p>Isi identitas dasar anak agar hasil konsultasi mudah dicatat kembali.</p>
                        </div>
                    </div>

                    <div class="identity-grid">
                        <label>
                            <span>Nama anak</span>
                            <input type="text" name="nama" value="<?= esc($old['nama'] ?? '', 'attr') ?>" placeholder="Contoh: Aisyah" required>
                            <small>Nama anak yang akan diperiksa.</small>
                        </label>
                        <label>
                            <span>NIK anak</span>
                            <input type="text" name="nik" value="<?= esc($old['nik'] ?? '', 'attr') ?>" inputmode="numeric" placeholder="Boleh dikosongkan">
                            <small>Isi jika tersedia di KK/KIA.</small>
                        </label>
                        <label>
                            <span>Jenis Kelamin</span>
                            <select name="jenis_kelamin" required>
                                <option value="">Pilih jenis kelamin</option>
                                <option value="L" <?= ($old['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= ($old['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                            <small>Dipakai untuk membandingkan pertumbuhan anak.</small>
                        </label>
                        <label>
                            <span>Tanggal Lahir</span>
                            <input type="date" id="tanggal-lahir-anak" name="tanggal_lahir" value="<?= esc($old['tanggal_lahir'] ?? '', 'attr') ?>">
                            <small>Isi tanggal lahir, umur akan terhitung otomatis.</small>
                        </label>
                        <label>
                            <span>Umur anak sekarang</span>
                            <div class="input-with-unit">
                                <input type="number" id="umur-anak-bulan" name="umur" value="<?= esc($old['umur'] ?? '', 'attr') ?>" min="0" max="60" placeholder="Otomatis" required readonly>
                                <b>bulan</b>
                            </div>
                            <small>Tidak perlu diisi manual.</small>
                        </label>
                        <label>
                            <span>Nama ibu/ayah</span>
                            <input type="text" name="nama_ortu" value="<?= esc($old['nama_ortu'] ?? '', 'attr') ?>" placeholder="Nama orang tua">
                            <small>Untuk catatan admin.</small>
                        </label>
                    </div>

                    <div class="form-section-title">
                        <div>
                            <span class="section-label">Bagian 2</span>
                            <h2>Ukuran tubuh terakhir</h2>
                            <p>Gunakan hasil timbang dan ukur paling baru dari rumah, posyandu, puskesmas, atau buku KIA.</p>
                        </div>
                    </div>

                    <div class="identity-grid">
                        <label>
                            <span>Berat badan terakhir</span>
                            <div class="input-with-unit">
                                <input type="number" name="berat_badan" value="<?= esc($old['berat_badan'] ?? '', 'attr') ?>" min="0" step="0.01" placeholder="12.5" required>
                                <b>kg</b>
                            </div>
                            <small>Gunakan kilogram. Contoh: 10.5.</small>
                        </label>
                        <label>
                            <span>Tinggi badan terakhir</span>
                            <div class="input-with-unit">
                                <input type="number" name="tinggi_badan" value="<?= esc($old['tinggi_badan'] ?? '', 'attr') ?>" min="0" step="0.01" placeholder="86" required>
                                <b>cm</b>
                            </div>
                            <small>Gunakan sentimeter. Contoh: 86.</small>
                        </label>
                        <label>
                            <span>Lingkar lengan atas</span>
                            <div class="input-with-unit">
                                <input type="number" name="lingkar_lengan" value="<?= esc($old['lingkar_lengan'] ?? '', 'attr') ?>" min="0" step="0.01" placeholder="13.5">
                                <b>cm</b>
                            </div>
                            <small>Opsional. Biasanya diukur dengan pita LILA.</small>
                        </label>
                        <label>
                            <span>Lingkar kepala</span>
                            <div class="input-with-unit">
                                <input type="number" name="lingkar_kepala" value="<?= esc($old['lingkar_kepala'] ?? '', 'attr') ?>" min="0" step="0.01" placeholder="46">
                                <b>cm</b>
                            </div>
                            <small>Opsional, isi jika ada catatannya.</small>
                        </label>
                    </div>

                    <div class="form-section-title">
                        <div>
                            <span class="section-label">Bagian 3</span>
                            <h2>Kebiasaan dan lingkungan</h2>
                            <p>Bagian ini membantu admin melihat faktor pendukung, bukan untuk menghakimi pola asuh.</p>
                        </div>
                    </div>

                    <div class="identity-grid">
                        <label>
                            <span>Wilayah tempat tinggal</span>
                            <input type="text" name="tempat_tinggal" value="<?= esc($old['tempat_tinggal'] ?? '', 'attr') ?>" placeholder="Desa/kelurahan atau wilayah">
                            <small>Contoh: Desa Limusnunggal.</small>
                        </label>
                        <label class="consult-full">
                            <span>Alamat</span>
                            <textarea name="alamat" rows="3" placeholder="Tulis alamat singkat atau patokan rumah"><?= esc($old['alamat'] ?? '') ?></textarea>
                        </label>
                        <label class="consult-full">
                            <span>Catatan saat ibu hamil</span>
                            <textarea name="riwayat_kehamilan" rows="3" placeholder="Contoh: anemia, kurang energi, lahir prematur, atau tulis normal jika tidak ada catatan"><?= esc($old['riwayat_kehamilan'] ?? '') ?></textarea>
                            <small>Boleh dikosongkan kalau tidak ingat.</small>
                        </label>
                        <label class="consult-full">
                            <span>Kebiasaan makan anak</span>
                            <textarea name="pola_makan" rows="3" placeholder="Contoh: makan 3 kali sehari, masih ASI, lauk telur/ikan, jarang sayur, atau catatan lain"><?= esc($old['pola_makan'] ?? '') ?></textarea>
                            <small>Tulis dengan bahasa sehari-hari saja.</small>
                        </label>
                    </div>

                    <div class="consult-flow-note">
                        <span>Yang akan dilakukan StuntCare</span>
                        <p>StuntCare membandingkan berat dan tinggi anak dengan data pertumbuhan, lalu memberi tanda apakah anak terlihat aman, perlu dipantau, atau perlu pemeriksaan lanjutan.</p>
                    </div>

                    <button class="btn-submit-consult" type="submit">Lihat Saran untuk Anak</button>
                </form>

                <aside class="result-panel" aria-label="Hasil konsultasi">
                    <?php if (!empty($hasil)): ?>
                        <?php $diagnosa = $hasil['diagnosa']; ?>
                        <?php
                            $kelas = $diagnosa['kelas'] ?? 'H1';
                            $friendlyLabel = match ($kelas) {
                                'H3' => 'Perlu segera diperiksa',
                                'H2' => 'Perlu dipantau lebih dekat',
                                default => 'Pertumbuhan terlihat cukup baik',
                            };
                            $friendlyText = match ($kelas) {
                                'H3' => 'Hasil awal menunjukkan tanda risiko tinggi. Sebaiknya anak dibawa ke posyandu, puskesmas, atau tenaga kesehatan untuk pemeriksaan lanjutan.',
                                'H2' => 'Ada tanda yang perlu diperhatikan. Pantau makan, berat, dan tinggi anak, lalu konsultasikan saat posyandu atau puskesmas.',
                                default => 'Hasil awal belum menunjukkan tanda risiko tinggi. Tetap lanjutkan pemantauan rutin dan pola makan bergizi.',
                            };
                        ?>
                        <span class="result-label">Saran awal untuk orang tua</span>
                        <h2><?= esc($hasil['nama']) ?></h2>
                        <div class="result-meta">
                            <span><?= esc((string) $hasil['umur']) ?> bulan</span>
                            <span><?= esc((string) $hasil['jumlah_gejala']) ?> tanda pertumbuhan terbaca</span>
                        </div>

                        <div class="score-ring friendly-score <?= $kelas === 'H3' ? 'danger-ring' : ($kelas === 'H2' ? 'warning-ring' : '') ?>">
                            <strong><?= esc((string) ($diagnosa['posterior_persen'] ?? 0)) ?>%</strong>
                            <small>tingkat kecocokan</small>
                        </div>
                        <h3><?= esc($friendlyLabel) ?></h3>
                        <p><?= esc($friendlyText) ?></p>

                        <div class="solution-box parent-advice">
                            <b>Langkah berikutnya</b>
                            <p><?= esc($hasil['rekomendasi']) ?></p>
                        </div>

                        <details class="technical-details">
                            <summary>Lihat rincian perhitungan sistem</summary>

                            <div class="zscore-list">
                                <?php foreach ($hasil['zscore'] as $item): ?>
                                    <article>
                                        <b><?= esc($item['label']) ?></b>
                                        <strong><?= $item['nilai'] !== null ? esc((string) $item['nilai']) : '-' ?></strong>
                                        <span><?= esc($item['kategori']) ?></span>
                                    </article>
                                <?php endforeach; ?>
                            </div>

                            <h3 class="result-subtitle">Tanda yang dibaca sistem</h3>
                            <div class="consult-steps">
                                <?php foreach ($hasil['gejala'] as $gejala): ?>
                                    <span><?= esc($gejala['nama']) ?></span>
                                <?php endforeach; ?>
                            </div>

                            <h3 class="result-subtitle">Kelas sistem</h3>
                            <div class="posterior-list">
                                <?php foreach ($hasil['alternatif'] as $alt): ?>
                                    <article>
                                        <span><?= esc($alt['kelas'] . ' - ' . $alt['label']) ?></span>
                                        <strong><?= esc((string) $alt['posterior_persen']) ?>%</strong>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </details>
                    <?php else: ?>
                        <span class="result-label">Bantuan pengisian</span>
                        <h2>Tenang, isi pelan-pelan saja.</h2>
                        <p>Bagian yang wajib hanya nama anak, jenis kelamin, umur, berat badan, dan tinggi badan. Kolom lain membantu catatan admin jika tersedia.</p>
                        <div class="consult-steps">
                            <span>Umur pakai bulan</span>
                            <span>Berat pakai kg</span>
                            <span>Tinggi pakai cm</span>
                            <span>Hasil awal saja</span>
                        </div>
                        <div class="friendly-panel mini">
                            <strong>Contoh</strong>
                            <p>Anak umur 2 tahun ditulis 24 bulan. Berat 10,5 kg bisa ditulis 10.5.</p>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </section>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var birthDateInput = document.getElementById('tanggal-lahir-anak');
            var ageInput = document.getElementById('umur-anak-bulan');

            if (!birthDateInput || !ageInput) {
                return;
            }

            function calculateAgeInMonths() {
                if (!birthDateInput.value) {
                    ageInput.value = '';
                    return;
                }

                var birthDate = new Date(birthDateInput.value + 'T00:00:00');
                var today = new Date();

                if (Number.isNaN(birthDate.getTime()) || birthDate > today) {
                    ageInput.value = '';
                    return;
                }

                var months = (today.getFullYear() - birthDate.getFullYear()) * 12;
                months += today.getMonth() - birthDate.getMonth();

                if (today.getDate() < birthDate.getDate()) {
                    months -= 1;
                }

                ageInput.value = Math.max(0, Math.min(60, months));
            }

            birthDateInput.addEventListener('change', calculateAgeInMonths);
            calculateAgeInMonths();
        });
    </script>
</body>
</html>
