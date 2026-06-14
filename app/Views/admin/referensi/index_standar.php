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
                        <h4 class="card-title">Tabel Standar</h4>
                        <p class="text-muted">Referensi median dan SD untuk perhitungan Z-Score BB/U, TB/U, dan BB/TB. Edit nilai sesuai rujukan yang dipakai.</p>

                        <div class="d-flex flex-wrap align-items-center mb-3" style="gap: .75rem;">
                            <div class="btn-group" role="group" aria-label="Filter indikator standar antropometri">
                                <?php foreach ($indikator_options ?? ['TB/U', 'BB/U', 'BB/TB'] as $indikator): ?>
                                    <?php
                                        $isActive = ($indikator_aktif ?? 'TB/U') === $indikator;
                                        $query = array_filter([
                                            'indikator' => $indikator,
                                            'jenis_kelamin' => $jenis_kelamin_aktif ?? '',
                                        ], static fn ($value) => $value !== '');
                                    ?>
                                    <a
                                        class="btn btn-sm <?= $isActive ? 'btn-primary' : 'btn-outline-primary'; ?>"
                                        href="<?= base_url('adminstandar') . '?' . http_build_query($query); ?>">
                                        <?= esc($indikator); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>

                            <div class="btn-group" role="group" aria-label="Filter jenis kelamin standar antropometri">
                                <?php foreach ($jenis_kelamin_options ?? ['' => 'Semua', 'L' => 'Laki-laki', 'P' => 'Perempuan'] as $kodeJk => $labelJk): ?>
                                    <?php
                                        $isActive = ($jenis_kelamin_aktif ?? '') === (string) $kodeJk;
                                        $query = array_filter([
                                            'indikator' => $indikator_aktif ?? 'TB/U',
                                            'jenis_kelamin' => (string) $kodeJk,
                                        ], static fn ($value) => $value !== '');
                                    ?>
                                    <a
                                        class="btn btn-sm <?= $isActive ? 'btn-info' : 'btn-outline-info'; ?>"
                                        href="<?= base_url('adminstandar') . '?' . http_build_query($query); ?>">
                                        <?= esc($labelJk); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover admin-data-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Indikator</th>
                                        <th>JK</th>
                                        <th>Umur</th>
                                        <th>Tinggi</th>
                                        <th>Median</th>
                                        <th>SD</th>
                                        <th class="admin-no-sort">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($tb_standar ?? [] as $row): ?>
                                        <tr>
                                            <td><?= esc((string) $no++); ?></td>
                                            <td><?= esc($row['indikator']); ?></td>
                                            <td><?= esc($row['jenis_kelamin']); ?></td>
                                            <td><?= $row['umur_bulan'] !== null ? esc((string) $row['umur_bulan']) . ' bulan' : '-'; ?></td>
                                            <td><?= $row['tinggi_cm'] !== null ? esc((string) $row['tinggi_cm']) . ' cm' : '-'; ?></td>
                                            <td><?= esc(number_format((float) $row['median'], 2, '.', '')); ?></td>
                                            <td><?= esc(number_format((float) $row['sd'], 2, '.', '')); ?></td>
                                            <td>
                                                <div class="admin-table-actions">
                                                    <button class="btn btn-primary btn-sm"
                                                        type="button"
                                                        data-toggle="modal"
                                                        data-target="#editStandarModal"
                                                        data-id="<?= esc($row['id_standar'], 'attr'); ?>"
                                                        data-indikator="<?= esc($row['indikator'], 'attr'); ?>"
                                                        data-jk="<?= esc($row['jenis_kelamin'], 'attr'); ?>"
                                                        data-umur="<?= esc((string) ($row['umur_bulan'] ?? ''), 'attr'); ?>"
                                                        data-tinggi="<?= esc((string) ($row['tinggi_cm'] ?? ''), 'attr'); ?>"
                                                        data-median="<?= esc((string) $row['median'], 'attr'); ?>"
                                                        data-sd="<?= esc((string) $row['sd'], 'attr'); ?>"
                                                        data-sumber="<?= esc($row['sumber'] ?? '', 'attr'); ?>"
                                                        data-catatan="<?= esc($row['catatan'] ?? '', 'attr'); ?>">
                                                        Edit
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($tb_standar)): ?>
                                        <tr class="admin-empty-row"><td colspan="8" class="text-center text-muted py-4">Data standar belum tersedia.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editStandarModal" tabindex="-1" role="dialog" aria-labelledby="editStandarModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" id="editStandarForm">
                <?= csrf_field() ?>
                <input type="hidden" name="redirect_to" value="<?= esc((string) current_url(true), 'attr'); ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStandarModalLabel">Edit Standar Antropometri</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit-standar-info">Data Referensi</label>
                            <input type="text" class="form-control" id="edit-standar-info" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit-standar-median">Median</label>
                            <input type="number" class="form-control" id="edit-standar-median" name="median" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-standar-sd">SD</label>
                            <input type="number" class="form-control" id="edit-standar-sd" name="sd" step="0.01" min="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-standar-sumber">Sumber</label>
                            <input type="text" class="form-control" id="edit-standar-sumber" name="sumber">
                        </div>
                        <div class="form-group">
                            <label for="edit-standar-catatan">Catatan</label>
                            <textarea class="form-control" id="edit-standar-catatan" name="catatan" rows="3"></textarea>
                        </div>
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
            $('#editStandarModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var umur = button.data('umur') ? button.data('umur') + ' bulan' : '-';
                var tinggi = button.data('tinggi') ? button.data('tinggi') + ' cm' : '-';
                var info = button.data('indikator') + ' - ' + button.data('jk') + ' - Umur: ' + umur + ' - Tinggi: ' + tinggi;

                $('#editStandarForm').attr('action', '<?= base_url('admin/updateStandar'); ?>/' + id);
                $('#edit-standar-info').val(info);
                $('#edit-standar-median').val(button.data('median'));
                $('#edit-standar-sd').val(button.data('sd'));
                $('#edit-standar-sumber').val(button.data('sumber'));
                $('#edit-standar-catatan').val(button.data('catatan'));
            });
        });
    </script>

    <?= $this->include('layout/dashboard/footer') ?>
