        <section class="konseling-section" id="konseling">
            <div class="section-heading">
                <div>
                    <span class="section-eyebrow">Konseling</span>
                    <h2>Masukkan data anak untuk memulai konseling</h2>
                </div>
            </div>

            <?php if (session()->getFlashdata('konseling_success')): ?>
                <div class="landing-alert landing-alert-success">
                    <?= esc(session()->getFlashdata('konseling_success')); ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('konseling_errors')): ?>
                <div class="landing-alert landing-alert-error">
                    <?php foreach ((array) session()->getFlashdata('konseling_errors') as $error): ?>
                        <p><?= esc($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php $old = session()->getFlashdata('konseling_old') ?? []; ?>

            <form class="konseling-form" action="<?= base_url('/konsultasi') ?>" method="post">
                <?= csrf_field() ?>

                <label>
                    <span>Nama Anak</span>
                    <input type="text" name="nama" value="<?= esc($old['nama'] ?? $old['nama_anak'] ?? '', 'attr'); ?>" placeholder="Contoh: Aisyah" required>
                </label>

                <label>
                    <span>NIK</span>
                    <input type="text" name="nik" value="<?= esc($old['nik'] ?? '', 'attr'); ?>" inputmode="numeric" placeholder="Nomor induk kependudukan">
                </label>

                <label>
                    <span>Jenis Kelamin</span>
                    <select name="jenis_kelamin" required>
                        <option value="">Pilih jenis kelamin</option>
                        <option value="L" <?= ($old['jenis_kelamin'] ?? '') === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="P" <?= ($old['jenis_kelamin'] ?? '') === 'P' ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </label>

                <label>
                    <span>Tanggal Lahir</span>
                    <input type="date" name="tanggal_lahir" value="<?= esc($old['tanggal_lahir'] ?? '', 'attr'); ?>">
                </label>

                <label>
                    <span>Umur</span>
                    <div class="landing-input-unit">
                        <input type="number" name="umur" min="0" max="60" value="<?= esc($old['umur'] ?? $old['umur_bulan'] ?? '', 'attr'); ?>" placeholder="24" required>
                        <b>bulan</b>
                    </div>
                </label>

                <label>
                    <span>Berat Badan</span>
                    <div class="landing-input-unit">
                        <input type="number" name="berat_badan" min="0" step="0.01" value="<?= esc($old['berat_badan'] ?? '', 'attr'); ?>" placeholder="12.5">
                        <b>kg</b>
                    </div>
                </label>

                <label>
                    <span>Tinggi Badan</span>
                    <div class="landing-input-unit">
                        <input type="number" name="tinggi_badan" min="0" step="0.01" value="<?= esc($old['tinggi_badan'] ?? '', 'attr'); ?>" placeholder="86">
                        <b>cm</b>
                    </div>
                </label>

                <label>
                    <span>Nama Orang Tua</span>
                    <input type="text" name="nama_ortu" value="<?= esc($old['nama_ortu'] ?? '', 'attr'); ?>" placeholder="Nama ayah/ibu">
                </label>

                <label>
                    <span>Lingkar Lengan Atas</span>
                    <div class="landing-input-unit">
                        <input type="number" name="lingkar_lengan" min="0" step="0.01" value="<?= esc($old['lingkar_lengan'] ?? '', 'attr'); ?>" placeholder="13.5">
                        <b>cm</b>
                    </div>
                </label>

                <label>
                    <span>Lingkar Kepala</span>
                    <div class="landing-input-unit">
                        <input type="number" name="lingkar_kepala" min="0" step="0.01" value="<?= esc($old['lingkar_kepala'] ?? '', 'attr'); ?>" placeholder="46">
                        <b>cm</b>
                    </div>
                </label>

                <label>
                    <span>Tempat Tinggal</span>
                    <input type="text" name="tempat_tinggal" value="<?= esc($old['tempat_tinggal'] ?? '', 'attr'); ?>" placeholder="Desa/kelurahan">
                </label>

                <label class="konseling-full">
                    <span>Alamat</span>
                    <textarea name="alamat" rows="4" placeholder="Alamat domisili anak"><?= esc($old['alamat'] ?? ''); ?></textarea>
                </label>

                <label class="konseling-full">
                    <span>Riwayat Kehamilan Ibu</span>
                    <textarea name="riwayat_kehamilan" rows="3" placeholder="Riwayat anemia, KEK, prematur, atau catatan lain"><?= esc($old['riwayat_kehamilan'] ?? ''); ?></textarea>
                </label>

                <label class="konseling-full">
                    <span>Pola Makan</span>
                    <textarea name="pola_makan" rows="3" placeholder="Frekuensi makan, ASI/MPASI, lauk, sayur, buah"><?= esc($old['pola_makan'] ?? ''); ?></textarea>
                </label>

                <div class="konseling-form-actions">
                    <button class="btn-primary-landing" type="submit">Hitung Risiko</button>
                    <a class="btn-ghost-landing" href="<?= base_url('') ?>">Batal</a>
                </div>
            </form>
        </section>
