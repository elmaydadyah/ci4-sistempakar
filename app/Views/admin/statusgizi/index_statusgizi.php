<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<?php
$detailGroups = [
    'Data Anak' => [
        'No Urut' => 'no_urut',
        'NIK' => 'nik',
        'Nama' => 'nama',
        'Jenis Kelamin' => 'jk',
        'Tanggal Lahir' => 'tgl_lahir',
        'BB Lahir' => 'bb_lahir',
        'TB Lahir' => 'tb_lahir',
        'Nama Orang Tua' => 'nama_ortu',
    ],
    'Alamat dan Posyandu' => [
        'Provinsi' => 'prov',
        'Kab/Kota' => 'kab_kota',
        'Kecamatan' => 'kec',
        'Puskesmas' => 'puskesmas',
        'Desa/Kelurahan' => 'desa_kel',
        'Posyandu' => 'posyandu',
        'RT' => 'rt',
        'RW' => 'rw',
        'Alamat' => 'alamat',
    ],
    'Data Pengukuran' => [
        'Total Pengukuran' => 'total_pengukuran',
        'Usia Saat Ukur' => 'usia_saat_ukur',
        'Tanggal Pengukuran' => 'tanggal_pengukuran',
        'Berat' => 'berat',
        'Tinggi' => 'tinggi',
        'Cara Ukur' => 'cara_ukur',
        'LiLA' => 'lila',
    ],
    'Status Gizi' => [
        'BB/U' => 'bb_u',
        'ZS BB/U' => 'zs_bb_u',
        'TB/U' => 'tb_u',
        'ZS TB/U' => 'zs_tb_u',
        'BB/TB' => 'bb_tb',
        'ZS BB/TB' => 'zs_bb_tb',
        'Naik Berat Badan' => 'naik_berat_badan',
    ],
    'Layanan dan Import' => [
        'Jumlah Vit A' => 'jml_vit_a',
        'KPSP' => 'kpsp',
        'KIA' => 'kia',
        'Kelas Ibu Balita' => 'kelas_ibu_balita',
        'MBG' => 'mbg',
        'Detail' => 'detail',
        'File Upload' => 'uploaded_file',
        'Dibuat' => 'created_at',
        'Diupdate' => 'updated_at',
    ],
];
$adminRole = match (strtolower(trim((string) (session()->get('role') ?? 'admin1')))) {
    'admin2' => 'admin2',
    'admin3', '1', 'user' => 'admin3',
    default => 'admin1',
};
$canUploadStatusGizi = in_array($adminRole, ['admin1', 'admin2'], true);
?>

<div class="main-panel">
    <div class="content-wrapper">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success'); ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error'); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php if ($canUploadStatusGizi): ?>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <p class="card-title mb-3">Upload Excel</p>
                        <form action="<?= base_url('/admin/uploadStatusGizi'); ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <label for="file_excel">File .xls</label>
                                <input type="file" class="form-control" id="file_excel" name="file_excel" accept=".xls,.html,.htm" required>
                                <small class="text-muted d-block mt-2">Gunakan file export “Daftar Anak Berdasarkan Status Gizi”.</small>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="ti-upload mr-1"></i> Import ke Database
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="<?= $canUploadStatusGizi ? 'col-md-8' : 'col-md-12'; ?> grid-margin">
                <div class="row">
                    <div class="col-md-4 mb-3 stretch-card">
                        <div class="card card-tale">
                            <div class="card-body">
                                <p class="mb-3">Total Data</p>
                                <p class="fs-30 mb-0"><?= esc($summary['total'] ?? 0); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 stretch-card">
                        <div class="card card-light-danger">
                            <div class="card-body">
                                <p class="mb-3">Gizi Kurang/Buruk</p>
                                <p class="fs-30 mb-0"><?= esc($summary['gizi_kurang'] ?? 0); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 stretch-card">
                        <div class="card card-light-blue">
                            <div class="card-body">
                                <p class="mb-3">Pendek/Sangat Pendek</p>
                                <p class="fs-30 mb-0"><?= esc($summary['pendek'] ?? 0); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="card-title mb-2">Import Terakhir</p>
                        <?php if (!empty($summary['latest_upload'])): ?>
                            <div class="d-flex flex-wrap align-items-center">
                                <span class="badge badge-outline-primary mr-2 mb-2"><?= esc($summary['latest_upload']['uploaded_file']); ?></span>
                                <span class="text-muted mb-2"><?= esc($summary['latest_upload']['created_at']); ?></span>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">Belum ada file yang diimport.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="card-title mb-1">Preview Data Anak</h4>
                                <p class="text-muted mb-0">Menampilkan identitas anak dari 100 data terakhir.</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="statusGiziTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>JK</th>
                                        <th>Tgl Lahir</th>
                                        <th>Orang Tua</th>
                                        <th>Desa/Kel</th>
                                        <th>Posyandu</th>
                                        <th class="admin-no-sort">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($tb_anak_status_gizi) && is_array($tb_anak_status_gizi)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tb_anak_status_gizi as $anak): ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= esc($anak['nik']); ?></td>
                                                <td>
                                                    <strong><?= esc($anak['nama']); ?></strong>
                                                    <div class="text-muted small"><?= esc($anak['usia_saat_ukur']); ?></div>
                                                </td>
                                                <td><?= esc($anak['jk']); ?></td>
                                                <td><?= esc($anak['tgl_lahir']); ?></td>
                                                <td><?= esc($anak['nama_ortu']); ?></td>
                                                <td><?= esc($anak['desa_kel']); ?></td>
                                                <td><?= esc($anak['posyandu']); ?></td>
                                                <td>
                                                    <div class="admin-table-actions">
                                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#detailStatusGiziModal<?= esc($anak['id_status_gizi'], 'attr'); ?>">
                                                        Lihat Detail
                                                    </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($tb_anak_status_gizi) && is_array($tb_anak_status_gizi)): ?>
            <?php foreach ($tb_anak_status_gizi as $anak): ?>
                <div class="modal fade" id="detailStatusGiziModal<?= esc($anak['id_status_gizi'], 'attr'); ?>" tabindex="-1" role="dialog" aria-labelledby="detailStatusGiziModalLabel<?= esc($anak['id_status_gizi'], 'attr'); ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div>
                                    <h5 class="modal-title" id="detailStatusGiziModalLabel<?= esc($anak['id_status_gizi'], 'attr'); ?>">Detail Status Gizi Anak</h5>
                                    <small class="text-muted"><?= esc($anak['nama']); ?><?= !empty($anak['nik']) ? ' - ' . esc($anak['nik']) : ''; ?></small>
                                </div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <?php foreach ($detailGroups as $title => $items): ?>
                                    <div class="border rounded mb-3">
                                        <div class="bg-light px-3 py-2 border-bottom">
                                            <strong><?= esc($title); ?></strong>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless mb-0">
                                                <tbody>
                                                    <?php foreach ($items as $label => $field): ?>
                                                        <tr>
                                                            <th class="text-muted" style="width: 220px;"><?= esc($label); ?></th>
                                                            <td><?= esc(($anak[$field] ?? '') !== '' && ($anak[$field] ?? null) !== null ? $anak[$field] : '-'); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        window.addEventListener('load', function () {
            if (!window.jQuery || !jQuery.fn.DataTable) {
                return;
            }

            jQuery('#statusGiziTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                ordering: true,
                searching: true,
                info: true,
                autoWidth: false,
                columnDefs: [
                    { orderable: false, targets: 'admin-no-sort' }
                ],
                language: {
                    search: 'Cari:',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 data',
                    infoFiltered: '(difilter dari _MAX_ total data)',
                    zeroRecords: 'Data status gizi tidak ditemukan',
                    emptyTable: 'Data status gizi belum tersedia',
                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: 'Berikutnya',
                        previous: 'Sebelumnya',
                    },
                },
            });

            jQuery('#statusGiziTable').closest('.dataTables_wrapper').find('div[id$="_filter"] input').attr('placeholder', 'Cari');
        });
    </script>

    <?= $this->include('layout/dashboard/footer') ?>
