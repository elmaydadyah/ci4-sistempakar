<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<?php
$nextRuleCode = $next_rule_code ?? 'RB001';
$totalRules = is_array($tb_rule_based ?? null) ? count($tb_rule_based) : 0;
$activeRules = is_array($tb_rule_based ?? null) ? count(array_filter($tb_rule_based, static fn ($rule) => (int) ($rule['aktif'] ?? 0) === 1)) : 0;
$inactiveRules = max(0, $totalRules - $activeRules);
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row align-items-center">
                    <div class="col-12 col-xl-8 mb-2 mb-xl-0">
                        <h3 class="font-weight-bold">Rule Based Z-Score</h3>
                        <h6 class="font-weight-normal mb-0 text-muted">Kelola relasi hipotesis dan gejala untuk basis aturan sistem pakar.</h6>
                    </div>
                    <div class="col-12 col-xl-4 text-xl-right">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#createRuleModal">
                            Tambah Rule
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')); ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1">Total Rule</p>
                        <h3 class="font-weight-bold mb-0"><?= esc((string) $totalRules); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1">Rule Aktif</p>
                        <h3 class="font-weight-bold mb-0"><?= esc((string) $activeRules); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1">Rule Nonaktif</p>
                        <h3 class="font-weight-bold mb-0"><?= esc((string) $inactiveRules); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tabel Rule Based</h4>
                        <p class="text-muted mb-3">Data ini mengikuti format Rule Base.xlsx: satu hipotesis dapat memiliki banyak kode gejala.</p>

                        <div class="table-responsive">
                            <table class="table table-hover admin-data-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Kode</th>
                                        <th>Nama Rule</th>
                                        <th>Hipotesis</th>
                                        <th>Gejala</th>
                                        <th>Status</th>
                                        <th>Urutan</th>
                                        <th class="admin-no-sort">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($tb_rule_based) && is_array($tb_rule_based)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tb_rule_based as $rule): ?>
                                            <tr>
                                                <td><?= esc((string) $no++); ?></td>
                                                <td><strong><?= esc($rule['kode_rule']); ?></strong></td>
                                                <td>
                                                    <?= esc($rule['nama_rule']); ?>
                                                    <?php if (!empty($rule['catatan'])): ?>
                                                        <div class="text-muted small"><?= esc($rule['catatan']); ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= esc(($rule['kode_hipotesis'] ?? '-') . (!empty($rule['risiko_stunting']) ? ' - ' . $rule['risiko_stunting'] : '')); ?></td>
                                                <td><?= esc(($rule['kode_gejala'] ?? '-') . (!empty($rule['nama_gejala']) ? ' - ' . $rule['nama_gejala'] : '')); ?></td>
                                                <td>
                                                    <span class="badge <?= (int) ($rule['aktif'] ?? 0) === 1 ? 'badge-success' : 'badge-secondary'; ?>">
                                                        <?= (int) ($rule['aktif'] ?? 0) === 1 ? 'Aktif' : 'Nonaktif'; ?>
                                                    </span>
                                                </td>
                                                <td><?= esc((string) ($rule['urutan'] ?? 0)); ?></td>
                                                <td>
                                                    <div class="admin-table-actions">
                                                        <button class="btn btn-primary btn-sm"
                                                            type="button"
                                                            data-toggle="modal"
                                                            data-target="#editRuleModal"
                                                            data-id="<?= esc($rule['id_rule'], 'attr'); ?>"
                                                            data-kode="<?= esc($rule['kode_rule'], 'attr'); ?>"
                                                            data-nama="<?= esc($rule['nama_rule'], 'attr'); ?>"
                                                            data-hipotesis="<?= esc($rule['kode_hipotesis'] ?? '', 'attr'); ?>"
                                                            data-gejala="<?= esc($rule['kode_gejala'] ?? '', 'attr'); ?>"
                                                            data-aktif="<?= esc((string) ($rule['aktif'] ?? 0), 'attr'); ?>"
                                                            data-urutan="<?= esc((string) ($rule['urutan'] ?? 0), 'attr'); ?>"
                                                            data-catatan="<?= esc($rule['catatan'] ?? '', 'attr'); ?>">
                                                            Edit
                                                        </button>
                                                        <a href="<?= base_url('admin/deleteRuleBased/' . $rule['id_rule']); ?>"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus rule ini?');">Delete</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr class="admin-empty-row">
                                            <td colspan="8" class="text-center text-muted py-4">Data rule based belum tersedia.</td>
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

    <div class="modal fade" id="createRuleModal" tabindex="-1" role="dialog" aria-labelledby="createRuleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form method="post" action="<?= base_url('admin/createRuleBased'); ?>">
                <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createRuleModalLabel">Tambah Rule Based</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?= $this->include('admin/referensi/partials/form_rule_based') ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editRuleModal" tabindex="-1" role="dialog" aria-labelledby="editRuleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form method="post" id="editRuleForm">
                <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRuleModalLabel">Edit Rule Based</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?= $this->include('admin/referensi/partials/form_rule_based') ?>
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
            $('#editRuleModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                var id = button.data('id');

                $('#editRuleForm').attr('action', '<?= base_url('admin/updateRuleBased'); ?>/' + id);
                modal.find('[name="kode_rule"]').val(button.data('kode'));
                modal.find('[name="nama_rule"]').val(button.data('nama'));
                modal.find('[name="kode_hipotesis"]').val(button.data('hipotesis'));
                modal.find('[name="kode_gejala"]').val(button.data('gejala'));
                modal.find('[name="aktif"]').prop('checked', String(button.data('aktif')) === '1');
                modal.find('[name="urutan"]').val(button.data('urutan'));
                modal.find('[name="catatan"]').val(button.data('catatan'));
            });

            $('#createRuleModal').on('show.bs.modal', function () {
                var modal = $(this);
                modal.find('form')[0].reset();
                modal.find('[name="kode_rule"]').val(<?= json_encode($nextRuleCode); ?>);
                modal.find('[name="aktif"]').prop('checked', true);
                modal.find('.rule-preview').text('Pilih hipotesis dan gejala untuk melihat ringkasan rule.');
            });

            $('.modal').on('input change', '[name="kode_hipotesis"], [name="kode_gejala"], [name="nama_rule"]', function () {
                updateRulePreview($(this).closest('.modal'));
            });

            $('#editRuleModal, #createRuleModal').on('shown.bs.modal', function () {
                updateRulePreview($(this));
            });

            function updateRulePreview(modal) {
                var hipotesis = modal.find('[name="kode_hipotesis"]').val() || 'Hipotesis';
                var gejala = modal.find('[name="kode_gejala"]').val() || 'Gejala';
                modal.find('.rule-preview').text('IF hipotesis ' + hipotesis + ' THEN memakai gejala ' + gejala);
            }
        });
    </script>

    <?= $this->include('layout/dashboard/footer') ?>
