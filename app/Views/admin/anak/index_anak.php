<?php
$filter = is_array($filter ?? null) ? $filter : ['anak' => 0, 'kelurahan' => '', 'tanggal_mulai' => '', 'tanggal_selesai' => ''];
$adminRole = match (strtolower(trim((string) (session()->get('role') ?? 'admin1')))) {
    'admin2' => 'admin2',
    'admin3', '1', 'user' => 'admin3',
    default => 'admin1',
};
$canManageAnak = in_array($adminRole, ['admin1', 'admin2'], true);
?>
<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')); ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="admin-table-toolbar">
                            <div>
                                <h4 class="card-title mb-1">Filter Data Anak</h4>
                                <p class="text-muted mb-0">Saring data berdasarkan kelurahan dan rentang tanggal input.</p>
                            </div>
                        </div>
                        <form class="admin-table-toolbar-actions align-items-end flex-wrap" method="get" action="<?= base_url('adminanak'); ?>">
                            <?php if (!empty($filter['anak'])): ?>
                                <input type="hidden" name="anak" value="<?= esc((string) $filter['anak'], 'attr'); ?>">
                            <?php endif; ?>
                            <div class="admin-filter-control">
                                <label for="anakKelurahanFilter">Kelurahan</label>
                                <select id="anakKelurahanFilter" name="kelurahan" class="form-control form-control-sm">
                                    <option value="">Semua Kelurahan</option>
                                    <?php foreach (($kelurahan_options ?? []) as $kelurahan): ?>
                                        <option value="<?= esc($kelurahan, 'attr'); ?>" <?= ($filter['kelurahan'] ?? '') === $kelurahan ? 'selected' : ''; ?>><?= esc($kelurahan); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="admin-filter-control">
                                <label for="anakTanggalMulai">Dari</label>
                                <input type="date" id="anakTanggalMulai" name="tanggal_mulai" class="form-control form-control-sm" value="<?= esc($filter['tanggal_mulai'] ?? '', 'attr'); ?>">
                            </div>
                            <div class="admin-filter-control">
                                <label for="anakTanggalSelesai">Sampai</label>
                                <input type="date" id="anakTanggalSelesai" name="tanggal_selesai" class="form-control form-control-sm" value="<?= esc($filter['tanggal_selesai'] ?? '', 'attr'); ?>">
                            </div>
                            <div class="mt-2 w-100 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm mr-2">Terapkan</button>
                                <a class="btn btn-light btn-sm" href="<?= base_url('adminanak'); ?>">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="admin-table-toolbar">
                            <h4 class="card-title mb-0">Tabel Anak</h4>
                        </div>
                        <?php if (!empty($filter['anak'])): ?>
                            <div class="alert alert-info py-2 mb-3">
                                Menampilkan detail data anak dari hasil diagnosa. <a class="admin-inline-link" href="<?= base_url('adminanak'); ?>">Lihat semua data anak</a>
                            </div>
                        <?php endif; ?>
                        <div class="table-responsive">
                            <table class="table table-hover admin-data-table" id="adminAnakTable">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Anak</th>
                                        <th>NIK</th>
                                        <th>JK</th>
                                        <th>Umur</th>
                                        <th>BB</th>
                                        <th>TB</th>
                                        <th>Z-Score</th>
                                        <th>Orang Tua</th>
                                        <th>RT/RW</th>
                                        <th>Kelurahan</th>
                                        <th>Kecamatan</th>
                                        <th>Alamat</th>
                                        <th>Tanggal Pemeriksaan</th>
                                        <?php if ($canManageAnak): ?>
                                            <th class="admin-no-sort">Aksi</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($tb_anak) && is_array($tb_anak)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tb_anak as $anak): ?>
                                            <?php
                                                $jenisKelamin = (string) ($anak['jenis_kelamin'] ?? $anak['jk_anak'] ?? '');
                                                $umurBulan = (string) ($anak['umur_bulan'] ?? $anak['umur_anak'] ?? '');
                                                $beratBadan = (string) ($anak['berat_badan'] ?? $anak['berat_anak'] ?? '');
                                                $tinggiBadan = (string) ($anak['tinggi_badan'] ?? $anak['tinggi_anak'] ?? '');
                                            ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td>
                                                    <strong><?= esc($anak['nama_anak'] ?? '-'); ?></strong>
                                                    <div class="text-muted small"><?= esc($anak['tanggal_lahir'] ?? '-'); ?></div>
                                                </td>
                                                <td><?= esc($anak['nik'] ?? '-'); ?></td>
                                                <td><?= esc($jenisKelamin ?: '-'); ?></td>
                                                <td><?= esc($umurBulan !== '' ? $umurBulan : '-'); ?> bulan</td>
                                                <td><?= esc($beratBadan !== '' ? $beratBadan : '-'); ?> kg</td>
                                                <td><?= esc($tinggiBadan !== '' ? $tinggiBadan : '-'); ?> cm</td>
                                                <td>
                                                    <div class="small">BB/U: <?= esc($anak['kategori_bb_u'] ?? '-'); ?> <?= isset($anak['zs_bb_u']) ? '(' . esc((string) $anak['zs_bb_u']) . ')' : ''; ?></div>
                                                    <div class="small">TB/U: <?= esc($anak['kategori_tb_u'] ?? '-'); ?> <?= isset($anak['zs_tb_u']) ? '(' . esc((string) $anak['zs_tb_u']) . ')' : ''; ?></div>
                                                    <div class="small">BB/TB: <?= esc($anak['kategori_bb_tb'] ?? '-'); ?> <?= isset($anak['zs_bb_tb']) ? '(' . esc((string) $anak['zs_bb_tb']) . ')' : ''; ?></div>
                                                </td>
                                                <td><?= esc($anak['nama_ortu'] ?? '-'); ?></td>
                                                <td><?= esc(($anak['rt'] ?? '-') . ' / ' . ($anak['rw'] ?? '-')); ?></td>
                                                <td><?= esc($anak['kelurahan'] ?? '-'); ?></td>
                                                <td><?= esc($anak['kecamatan'] ?? '-'); ?></td>
                                                <td><div class="admin-table-text"><?= esc($anak['alamat'] ?? '-'); ?></div></td>
                                                <td><?= esc($anak['created_at'] ?? '-'); ?></td>
                                                <?php if ($canManageAnak): ?>
                                                    <td>
                                                        <div class="admin-table-actions">
                                                            <button class="btn btn-primary btn-sm"
                                                                type="button"
                                                                data-toggle="modal"
                                                                data-target="#editAnakModal"
                                                                data-id="<?= esc((string) ($anak['id_anak'] ?? ''), 'attr'); ?>"
                                                                data-nama="<?= esc($anak['nama_anak'] ?? '', 'attr'); ?>"
                                                                data-nik="<?= esc($anak['nik'] ?? '', 'attr'); ?>"
                                                                data-jk="<?= esc($jenisKelamin, 'attr'); ?>"
                                                                data-tanggal-lahir="<?= esc($anak['tanggal_lahir'] ?? '', 'attr'); ?>"
                                                                data-umur="<?= esc($umurBulan, 'attr'); ?>"
                                                                data-berat="<?= esc($beratBadan, 'attr'); ?>"
                                                                data-tinggi="<?= esc($tinggiBadan, 'attr'); ?>"
                                                                data-lila="<?= esc((string) ($anak['lingkar_lengan'] ?? ''), 'attr'); ?>"
                                                                data-lingkar-kepala="<?= esc((string) ($anak['lingkar_kepala'] ?? ''), 'attr'); ?>"
                                                                data-ortu="<?= esc($anak['nama_ortu'] ?? '', 'attr'); ?>"
                                                                data-rt="<?= esc($anak['rt'] ?? '', 'attr'); ?>"
                                                                data-rw="<?= esc($anak['rw'] ?? '', 'attr'); ?>"
                                                                data-kelurahan="<?= esc($anak['kelurahan'] ?? $anak['desa'] ?? '', 'attr'); ?>"
                                                                data-kecamatan="<?= esc($anak['kecamatan'] ?? '', 'attr'); ?>"
                                                                data-alamat="<?= esc($anak['alamat'] ?? '', 'attr'); ?>"
                                                                data-tempat-tinggal="<?= esc($anak['tempat_tinggal'] ?? '', 'attr'); ?>"
                                                                data-riwayat="<?= esc($anak['riwayat_kehamilan'] ?? '', 'attr'); ?>"
                                                                data-pola="<?= esc($anak['pola_makan'] ?? '', 'attr'); ?>">
                                                                Edit
                                                            </button>
                                                            <a href="<?= base_url('admin/deleteAnak/' . ($anak['id_anak'] ?? 0)); ?>"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data anak ini?');">Hapus</a>
                                                        </div>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr class="admin-empty-row">
                                            <td colspan="<?= $canManageAnak ? '16' : '15'; ?>" class="text-center text-muted py-4">Belum ada data anak dari form konseling.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editAnakModal" tabindex="-1" role="dialog" aria-labelledby="editAnakModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form method="post" id="editAnakForm">
                <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAnakModalLabel">Edit Data Anak</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Nama Anak</label>
                                <input type="text" class="form-control" name="nama_anak" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>NIK</label>
                                <input type="text" class="form-control" name="nik">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Jenis Kelamin</label>
                                <select class="form-control" name="jenis_kelamin" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Tanggal Lahir</label>
                                <input type="date" class="form-control" name="tanggal_lahir">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Umur (bulan)</label>
                                <input type="number" class="form-control" name="umur_bulan" min="0" max="60" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>BB (kg)</label>
                                <input type="number" class="form-control" name="berat_badan" step="0.01" min="0">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>TB (cm)</label>
                                <input type="number" class="form-control" name="tinggi_badan" step="0.01" min="0">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Lingkar Lengan</label>
                                <input type="number" class="form-control" name="lingkar_lengan" step="0.01" min="0">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Lingkar Kepala</label>
                                <input type="number" class="form-control" name="lingkar_kepala" step="0.01" min="0">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Nama Orang Tua</label>
                                <input type="text" class="form-control" name="nama_ortu">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>RT</label>
                                <input type="text" class="form-control" name="rt">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>RW</label>
                                <input type="text" class="form-control" name="rw">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Kelurahan/Desa</label>
                                <input type="text" class="form-control" name="kelurahan">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Kecamatan</label>
                                <input type="text" class="form-control" name="kecamatan">
                            </div>
                            <div class="col-12 form-group">
                                <label>Alamat</label>
                                <textarea class="form-control" name="alamat" rows="2"></textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Riwayat Kehamilan</label>
                                <textarea class="form-control" name="riwayat_kehamilan" rows="2"></textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Pola Makan</label>
                                <textarea class="form-control" name="pola_makan" rows="2"></textarea>
                            </div>
                            <div class="col-12 form-group">
                                <label>Tempat Tinggal</label>
                                <input type="text" class="form-control" name="tempat_tinggal">
                            </div>
                        </div>
                        <small class="text-muted">Catatan: perubahan ukuran tubuh di sini tidak menghitung ulang z-score hasil diagnosa lama.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#editAnakModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                var id = button.data('id');

                $('#editAnakForm').attr('action', '<?= base_url('admin/updateAnak'); ?>/' + id);
                modal.find('[name="nama_anak"]').val(button.data('nama'));
                modal.find('[name="nik"]').val(button.data('nik'));
                modal.find('[name="jenis_kelamin"]').val(button.data('jk'));
                modal.find('[name="tanggal_lahir"]').val(button.data('tanggal-lahir'));
                modal.find('[name="umur_bulan"]').val(button.data('umur'));
                modal.find('[name="berat_badan"]').val(button.data('berat'));
                modal.find('[name="tinggi_badan"]').val(button.data('tinggi'));
                modal.find('[name="lingkar_lengan"]').val(button.data('lila'));
                modal.find('[name="lingkar_kepala"]').val(button.data('lingkar-kepala'));
                modal.find('[name="nama_ortu"]').val(button.data('ortu'));
                modal.find('[name="rt"]').val(button.data('rt'));
                modal.find('[name="rw"]').val(button.data('rw'));
                modal.find('[name="kelurahan"]').val(button.data('kelurahan'));
                modal.find('[name="kecamatan"]').val(button.data('kecamatan'));
                modal.find('[name="alamat"]').val(button.data('alamat'));
                modal.find('[name="tempat_tinggal"]').val(button.data('tempat-tinggal'));
                modal.find('[name="riwayat_kehamilan"]').val(button.data('riwayat'));
                modal.find('[name="pola_makan"]').val(button.data('pola'));
            });
        });
    </script>

    <?= $this->include('layout/dashboard/footer') ?>
