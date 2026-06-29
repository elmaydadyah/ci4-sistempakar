<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi SiPASTI</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/landing.css?v=' . filemtime(FCPATH . 'assets/css/landing.css')) ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/images/logo/logo_puskesmas.png') ?>">
</head>
<body class="consult-page">
    <?= $this->include('layout/landing/navbar') ?>

    <main class="landing-shell consult-shell <?= !empty($hasil) ? 'has-result' : '' ?>">
        <img class="consult-bg-image" src="<?= base_url('assets/images/landing/foto.png') ?>" alt="" aria-hidden="true">

        <section class="consult-hero" aria-label="Form konsultasi stunting">
            <?php if (empty($hasil)): ?>
                <div class="consult-heading">
                    <span class="consult-kicker">Cek Tumbuh Kembang</span>
                    <h1>Cek kondisi anak dengan mudah.</h1>
                    <p>Isi data yang biasa ada di buku KIA atau catatan posyandu. Hasil ini adalah pemeriksaan awal, bukan pengganti diagnosis dokter.</p>
                    <div class="parent-steps" aria-label="Langkah konsultasi">
                        <span><b>1</b> Isi data anak</span>
                        <span><b>2</b> Masukkan ukuran tubuh</span>
                        <span><b>3</b> Lihat saran awal</span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="consult-layout wide <?= !empty($hasil) ? 'result-only' : '' ?>">
                <?php if (!empty($hasil)): ?>
                <aside class="result-panel" aria-label="Hasil konsultasi">
                        <?php $diagnosa = $hasil['diagnosa']; ?>
                        <?php
                            $kelas = $diagnosa['kelas'] ?? 'H1';
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
                            $statusTone = match ($kelas) {
                                'H1' => 'danger',
                                'H2' => 'warning',
                                default => 'success',
                            };
                            $confidencePercent = $diagnosa['posterior_persen'] ?? $hasil['persentase'] ?? 0;
                            $risikoObesitas = (bool) ($hasil['risiko_obesitas'] ?? false);
                            if (!$risikoObesitas) {
                                foreach (($hasil['zscore'] ?? []) as $zscoreItem) {
                                    $labelZscore = (string) ($zscoreItem['label'] ?? '');
                                    $kategoriZscore = (string) ($zscoreItem['kategori'] ?? '');

                                    if (($labelZscore === 'BB/U' && $kategoriZscore === 'Risiko berat badan lebih')
                                        || ($labelZscore === 'BB/TB' && in_array($kategoriZscore, ['Berisiko gizi lebih', 'Gizi lebih'], true))) {
                                        $risikoObesitas = true;
                                        break;
                                    }
                                }
                            }
                            $interpretationText = match ($kelas) {
                                'H1' => 'Risiko stunting tinggi berarti hasil pengukuran tubuh dan jawaban pada konsultasi menunjukkan anak perlu segera diperiksa lebih lanjut oleh tenaga kesehatan.',
                                'H2' => 'Risiko stunting sedang berarti ada beberapa kondisi yang perlu diperhatikan. Pantau pertumbuhan anak secara rutin dan perbaiki asupan gizinya.',
                                default => 'Risiko stunting rendah berarti hasil saat ini belum menunjukkan kekhawatiran yang kuat. Tetap pantau pertumbuhan anak secara rutin.',
                            };
                            $obesityWarningText = '';
                            if ($risikoObesitas) {
                                $obesityWarningText = 'Namun, anak Anda memiliki risiko obesitas. Segera lakukan pemeriksaan ke puskesmas atau posyandu terdekat.';
                            }
                        ?>
                        <div class="parent-result-head">
                            <div>
                                <span class="result-label">Kesimpulan awal</span>
                                <h2><?= esc($friendlyLabel) ?></h2>
                                <div class="result-meta parent-identity-meta">
                                    <span><?= esc($hasil['nama']) ?></span>
                                    <span><?= esc((string) $hasil['umur']) ?> bulan</span>
                                    <span><?= esc((string) $hasil['jumlah_gejala']) ?> kondisi diperhatikan</span>
                                    <span>Persentase <?= esc(number_format((float) $confidencePercent, 2, '.', '')) ?>%</span>
                                </div>
                            </div>
                        </div>

                        <div class="parent-result-message-box <?= esc($statusTone, 'attr') ?>">
                            <p class="parent-result-message"><?= esc($friendlyText) ?></p>
                            <p class="parent-result-message parent-result-interpretation"><?= esc($interpretationText) ?></p>
                            <?php if ($obesityWarningText !== ''): ?>
                                <p class="parent-result-message parent-result-interpretation"><?= esc($obesityWarningText) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="solution-box parent-advice">
                            <b>Langkah berikutnya</b>
                            <p><?= esc($hasil['rekomendasi']) ?></p>
                        </div>

                        <div class="parent-measure-grid" aria-label="Ringkasan ukuran anak">
                            <?php foreach ($hasil['zscore'] as $item): ?>
                                <article>
                                    <span><?= esc($item['label']) ?></span>
                                    <strong><?= esc($item['kategori']) ?></strong>
                                </article>
                            <?php endforeach; ?>
                        </div>
                        <div class="zscore-source-note">
                            <b>Sumber perhitungan Z-Score</b>
                            <p>Hasil Z-Score pada konsultasi ini dihitung berdasarkan tabel standar antropometri anak yang mengacu pada Peraturan Menteri Kesehatan Republik Indonesia Nomor 2 Tahun 2020.</p>
                        </div>

                        <?php
                            $gejalaTerbaca = is_array($hasil['gejala_terbaca'] ?? null) ? $hasil['gejala_terbaca'] : ($hasil['gejala'] ?? []);
                            $kodePenyebabKehamilan = ['G15', 'G16', 'G17', 'G18', 'G19'];
                            $gejalaAnak = is_array($hasil['gejala_anak'] ?? null)
                                ? $hasil['gejala_anak']
                                : array_values(array_filter($gejalaTerbaca, static fn ($item) => !in_array((string) ($item['kode'] ?? ''), $kodePenyebabKehamilan, true)));
                            $penyebabTerbaca = is_array($hasil['penyebab_terbaca'] ?? null)
                                ? $hasil['penyebab_terbaca']
                                : array_values(array_filter($gejalaTerbaca, static fn ($item) => in_array((string) ($item['kode'] ?? ''), $kodePenyebabKehamilan, true)));
                        ?>
                        <div class="parent-symptom-detail" aria-label="Detail gejala yang dipilih">
                            <div class="parent-symptom-detail-head">
                                <div>
                                    <b>Detail tanda/gejala dan penyebab terbaca</b>
                                    <p>Data berikut dipisahkan antara kondisi anak dan riwayat ibu saat hamil.</p>
                                </div>
                                <span><?= esc((string) count($gejalaTerbaca)) ?> data</span>
                            </div>

                            <div class="parent-symptom-columns">
                                <div class="parent-symptom-column">
                                    <div class="parent-symptom-column-head">
                                        <b>Gejala terbaca</b>
                                        <span><?= esc((string) count($gejalaAnak)) ?> gejala</span>
                                    </div>
                                    <?php if (!empty($gejalaAnak)): ?>
                                        <div class="parent-symptom-list">
                                            <?php foreach ($gejalaAnak as $gejalaItem): ?>
                                                <article>
                                                    <span><?= esc($gejalaItem['kode'] ?? '-') ?></span>
                                                    <div>
                                                        <strong><?= esc($gejalaItem['nama'] ?? $gejalaItem['indikator'] ?? '-') ?></strong>
                                                        <small>
                                                            <?= esc($gejalaItem['indikator'] ?? 'Gejala') ?>
                                                            <?php if (!empty($gejalaItem['kategori'])): ?>
                                                                - <?= esc($gejalaItem['kategori']) ?>
                                                            <?php endif; ?>
                                                            <?php if (array_key_exists('zscore', $gejalaItem) && $gejalaItem['zscore'] !== null && $gejalaItem['zscore'] !== ''): ?>
                                                                (Z-Score <?= esc((string) $gejalaItem['zscore']) ?>)
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                </article>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="parent-symptom-empty">Belum ada gejala kondisi anak yang masuk perhitungan.</p>
                                    <?php endif; ?>
                                </div>

                                <div class="parent-symptom-column">
                                    <div class="parent-symptom-column-head">
                                        <b>Penyebab terbaca</b>
                                        <span><?= esc((string) count($penyebabTerbaca)) ?> penyebab</span>
                                    </div>
                                    <?php if (!empty($penyebabTerbaca)): ?>
                                        <div class="parent-symptom-list">
                                            <?php foreach ($penyebabTerbaca as $gejalaItem): ?>
                                                <article>
                                                    <span><?= esc($gejalaItem['kode'] ?? '-') ?></span>
                                                    <div>
                                                        <strong><?= esc($gejalaItem['nama'] ?? $gejalaItem['indikator'] ?? '-') ?></strong>
                                                        <small>
                                                            Riwayat ibu saat hamil
                                                            <?php if (!empty($gejalaItem['kategori'])): ?>
                                                                - <?= esc($gejalaItem['kategori']) ?>
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                </article>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="parent-symptom-empty">Tidak ada penyebab dari riwayat ibu hamil yang terbaca.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($hasil['id_hasil_diagnosa'])): ?>
                            <div class="report-download-box">
                                <div>
                                    <b>Laporan hasil konsultasi</b>
                                    <p>Unduh atau cetak laporan ringkas untuk orang tua.</p>
                                </div>
                                <a href="<?= base_url('konsultasi/laporan/' . $hasil['id_hasil_diagnosa']); ?>" target="_blank" rel="noopener">Download laporan</a>
                            </div>
                            <div class="report-download-box">
                                <div>
                                    <b>Konsultasi baru</b>
                                    <p>Mulai pemeriksaan untuk anak berikutnya dengan form kosong.</p>
                                </div>
                                <a href="<?= base_url('konsultasi'); ?>">Mulai baru</a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($riwayat)): ?>
                            <div class="consult-history-box">
                                <b>Riwayat di browser ini</b>
                                <?php foreach ($riwayat as $item): ?>
                                    <a href="<?= base_url('konsultasi?hasil=' . ($item['id_hasil_diagnosa'] ?? 0)); ?>">
                                        <span><?= esc($item['nama'] ?? '-') ?>, <?= esc((string) ($item['umur'] ?? 0)) ?> bulan</span>
                                        <small><?= esc(($item['kelas_hasil'] ?? '-') . ' - ' . number_format((float) ($item['persentase'] ?? 0), 2, '.', '') . '%'); ?></small>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                </aside>
                <?php endif; ?>

                <?php if (empty($hasil)): ?>
                <form class="consult-form" action="<?= base_url('/konsultasi') ?>" method="post">
                    <?= csrf_field() ?>

                    <?php if (!empty($errors)): ?>
                        <div class="consult-alert">
                            <?php foreach ($errors as $error): ?>
                                <p><?= esc($error) ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

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
                            <input type="text" id="nik-anak" name="nik" value="<?= esc($old['nik'] ?? '', 'attr') ?>" inputmode="numeric" pattern="[0-9]{16}" minlength="16" maxlength="16" placeholder="Opsional, isi 16 angka jika sudah ada">
                            <small class="nik-hint">Boleh dikosongkan jika NIK anak belum tersedia.</small>
                            <small class="nik-error" id="nik-anak-error">NIK Harus berisikan 16 angka</small>
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
                            <input type="date" id="tanggal-lahir-anak" name="tanggal_lahir" value="<?= esc($old['tanggal_lahir'] ?? '', 'attr') ?>" required>
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
                            <input type="text" name="nama_ortu" value="<?= esc($old['nama_ortu'] ?? '', 'attr') ?>" placeholder="Nama orang tua" required>
                            <small>Untuk catatan admin.</small>
                        </label>
                    </div>

                    <div class="form-section-title">
                        <div>
                            <span class="section-label">Bagian 2</span>
                            <h2>Alamat anak</h2>
                            <p>Isi alamat domisili agar catatan konsultasi lebih lengkap untuk admin puskesmas.</p>
                        </div>
                    </div>

                    <div class="identity-grid">
                        <label>
                            <span>RT</span>
                            <input type="text" name="rt" value="<?= esc($old['rt'] ?? '', 'attr') ?>" inputmode="numeric" pattern="[0-9]+" placeholder="001" required>
                            <small>Isi angka RT.</small>
                        </label>
                        <label>
                            <span>RW</span>
                            <input type="text" name="rw" value="<?= esc($old['rw'] ?? '', 'attr') ?>" inputmode="numeric" pattern="[0-9]+" placeholder="004" required>
                            <small>Isi angka RW.</small>
                        </label>
                        <label>
                            <span>Desa/Kel</span>
                            <?php $kelurahanOptions = ['Cileungsi', 'Cileungsi Kidul', 'Cipenjo', 'Cipeucang', 'Dayeuh', 'Gandoang', 'Jatisari', 'Limus Nunggal', 'Mampir', 'Mekarsari', 'Pasir Angin', 'Situsari']; ?>
                            <?php $selectedKelurahan = $old['kelurahan'] ?? $old['desa'] ?? ''; ?>
                            <select name="kelurahan" required>
                                <option value="">Pilih desa/kel</option>
                                <?php foreach ($kelurahanOptions as $kelurahan): ?>
                                    <option value="<?= esc($kelurahan, 'attr') ?>" <?= $selectedKelurahan === $kelurahan ? 'selected' : '' ?>><?= esc($kelurahan) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small>Pilih desa/kel domisili anak.</small>
                            </label>
                            <label>
                                <span>Kecamatan</span>
                                <input type="text" name="kecamatan" value="CILEUNGSI" readonly required>
                                <small>Kecamatan sudah ditetapkan otomatis.</small>
                            </label>
                            <label class="identity-full">
                                <span>Alamat lengkap</span>
                                <textarea name="alamat" rows="3" placeholder="Contoh: Kp. Cileungsi Kidul No. 12" required><?= esc($old['alamat'] ?? '') ?></textarea>
                                <small>Nama jalan, kampung, nomor rumah, atau patokan.</small>
                            </label>
                    </div>

                    <div class="form-section-title">
                        <div>
                            <span class="section-label">Bagian 3</span>
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
                                <input type="number" name="lingkar_lengan" value="<?= esc($old['lingkar_lengan'] ?? '', 'attr') ?>" min="0" step="0.01" placeholder="13.5" required>
                                <b>cm</b>
                            </div>
                            <small>Biasanya diukur dengan pita LILA.</small>
                        </label>
                        <label>
                            <span>Lingkar kepala</span>
                            <div class="input-with-unit">
                                <input type="number" name="lingkar_kepala" value="<?= esc($old['lingkar_kepala'] ?? '', 'attr') ?>" min="0" step="0.01" placeholder="46" required>
                                <b>cm</b>
                            </div>
                            <small>Gunakan sentimeter. Contoh: 46.</small>
                        </label>
                    </div>

                    <div class="form-section-title">
                        <div>
                            <span class="section-label">Bagian 4</span>
                            <h2>Pertanyaan gejala</h2>
                            <p>Jawab sesuai kondisi yang terlihat pada anak dan riwayat ibu saat hamil.</p>
                        </div>
                    </div>

                    <?php $jawabanGejala = is_array($old['jawaban_gejala'] ?? null) ? $old['jawaban_gejala'] : []; ?>
                    <div class="symptom-question-list">
                        <?php if (!empty($tb_gejala)): ?>
                            <?php foreach ($tb_gejala as $gejala): ?>
                                <?php
                                    $idGejala = (int) ($gejala['id_gejala'] ?? 0);
                                    $jawaban = $jawabanGejala[$idGejala] ?? null;
                                ?>
                                <article class="symptom-question"
                                    data-kode="<?= esc($gejala['kode_gejala'] ?? '', 'attr') ?>"
                                    data-min-umur="<?= esc((string) ($gejala['umur_min'] ?? 0), 'attr') ?>"
                                    data-max-umur="<?= esc((string) ($gejala['umur_max'] ?? 60), 'attr') ?>">
                                    <div>
                                        <span><?= esc($gejala['kode_gejala'] ?? ('G' . $idGejala)) ?></span>
                                        <p class="symptom-question-text"><?= esc($gejala['pertanyaan_gejala'] ?? $gejala['nama_gejala'] ?? '') ?></p>
                                    </div>
                                    <div class="symptom-answer" role="radiogroup" aria-label="<?= esc($gejala['pertanyaan_gejala'] ?? $gejala['nama_gejala'] ?? '', 'attr') ?>">
                                        <label>
                                            <input type="radio" name="jawaban_gejala[<?= esc((string) $idGejala, 'attr') ?>]" value="ya" <?= $jawaban === 'ya' ? 'checked' : '' ?> required>
                                            <b>Ya</b>
                                        </label>
                                        <label>
                                            <input type="radio" name="jawaban_gejala[<?= esc((string) $idGejala, 'attr') ?>]" value="tidak" <?= $jawaban === 'tidak' ? 'checked' : '' ?>>
                                            <b>Tidak</b>
                                        </label>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="friendly-panel mini">
                                <strong>Data gejala belum tersedia</strong>
                                <p>Tambahkan data gejala dari menu admin agar pertanyaan bisa tampil di sini.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="consult-flow-note">
                        <span>Yang akan dilakukan SiPASTI</span>
                        <p>SiPASTI membandingkan berat dan tinggi anak dengan data pertumbuhan, lalu memberi tanda apakah anak terlihat aman, perlu dipantau, atau perlu pemeriksaan lanjutan.</p>
                    </div>

                    <button class="btn-submit-consult" type="submit">Lihat Saran untuk Anak</button>
                </form>

                <?php if (!empty($riwayat)): ?>
                    <aside class="result-panel consult-history-panel" aria-label="Riwayat konsultasi">
                        <span class="result-label">Riwayat di browser ini</span>
                        <h2>Hasil sebelumnya</h2>
                        <p>Riwayat ini tersimpan tanpa login selama ibu memakai browser yang sama.</p>
                        <div class="consult-history-box compact-history">
                            <?php foreach ($riwayat as $item): ?>
                                <a href="<?= base_url('konsultasi?hasil=' . ($item['id_hasil_diagnosa'] ?? 0)); ?>">
                                    <span><?= esc($item['nama'] ?? '-') ?>, <?= esc((string) ($item['umur'] ?? 0)) ?> bulan</span>
                                    <small><?= esc(($item['kelas_hasil'] ?? '-') . ' - ' . number_format((float) ($item['persentase'] ?? 0), 2, '.', '') . '%'); ?></small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </aside>
                <?php endif; ?>
                <?php endif; ?>
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

            var symptomQuestions = Array.prototype.slice.call(document.querySelectorAll('.symptom-question'));

            function g06QuestionForAge(age) {
                if (age >= 4 && age <= 5) {
                    return 'Apakah anak belum mampu menahan kepala dengan stabil saat digendong atau belum mampu mengangkat tubuh saat tengkurap?';
                }

                if (age >= 6 && age <= 8) {
                    return 'Apakah anak belum mampu berguling atau belum mampu duduk dengan bantuan/tumpuan tangan?';
                }

                if (age >= 9 && age <= 11) {
                    return 'Apakah anak belum mampu duduk tanpa bantuan atau belum mulai belajar merangkak/berdiri dengan bantuan?';
                }

                if (age >= 12 && age <= 14) {
                    return 'Apakah anak belum mampu berdiri dengan berpegangan atau berjalan sambil berpegangan pada benda?';
                }

                if (age >= 15 && age <= 17) {
                    return 'Apakah anak belum mampu berjalan beberapa langkah sendiri?';
                }

                return 'Apakah anak belum mampu berjalan tanpa bantuan atau mengalami kesulitan gerak sesuai usianya?';
            }

            function updateSymptomQuestionsByAge() {
                if (!symptomQuestions.length) {
                    return;
                }

                var age = parseInt(ageInput.value, 10);
                var hasAge = !Number.isNaN(age);

                symptomQuestions.forEach(function (question) {
                    var minAge = parseInt(question.getAttribute('data-min-umur') || '0', 10);
                    var maxAge = parseInt(question.getAttribute('data-max-umur') || '60', 10);
                    var isApplicable = !hasAge || (age >= minAge && age <= maxAge);
                    var inputs = question.querySelectorAll('input[type="radio"]');

                    question.hidden = !isApplicable;
                    inputs.forEach(function (input) {
                        input.required = isApplicable;
                        input.disabled = !isApplicable;
                        if (!isApplicable) {
                            input.checked = false;
                        }
                    });

                    if (isApplicable && hasAge && question.getAttribute('data-kode') === 'G06') {
                        var questionText = question.querySelector('.symptom-question-text');
                        if (questionText) {
                            questionText.textContent = g06QuestionForAge(age);
                        }
                    }
                });
            }

            birthDateInput.addEventListener('change', updateSymptomQuestionsByAge);
            updateSymptomQuestionsByAge();

            var nikInput = document.getElementById('nik-anak');
            var nikError = document.getElementById('nik-anak-error');
            var nikHint = document.querySelector('.nik-hint');

            if (nikInput && nikError) {
                function validateNik() {
                    nikInput.value = nikInput.value.replace(/\D/g, '').slice(0, 16);
                    var hasError = nikInput.value.length > 0 && nikInput.value.length !== 16;
                    nikInput.classList.toggle('is-invalid', hasError);
                    nikError.classList.toggle('is-visible', hasError);
                    if (nikHint) {
                        nikHint.classList.toggle('is-hidden', hasError);
                    }
                    nikInput.setCustomValidity(hasError ? 'NIK Harus berisikan 16 angka' : '');
                }

                nikInput.addEventListener('input', validateNik);
                nikInput.addEventListener('blur', validateNik);
                validateNik();
            }
        });
    </script>
</body>
</html>
