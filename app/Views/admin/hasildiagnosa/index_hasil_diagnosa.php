<?php
$filter = is_array($filter ?? null) ? $filter : ['kelas_hasil' => ''];
$roleAccess = new \App\Libraries\RoleAccess();
$adminRole = $roleAccess->normalizeRole((string) (session()->get('role') ?? 'admin1'));
$canDeleteHasilDiagnosa = $roleAccess->hasPermission($adminRole, 'hasildiagnosa', 'hapus');
$formatGejalaDetail = static function ($json): array {
    $items = json_decode((string) ($json ?? '[]'), true);
    if (!is_array($items)) {
        return [];
    }

    $detail = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $detail[] = [
            'kode' => (string) ($item['kode'] ?? '-'),
            'nama' => (string) ($item['nama'] ?? $item['indikator'] ?? '-'),
            'indikator' => (string) ($item['indikator'] ?? 'Gejala'),
            'kategori' => (string) ($item['kategori'] ?? ''),
            'zscore' => array_key_exists('zscore', $item) ? $item['zscore'] : null,
        ];
    }

    return $detail;
};
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
                                <h4 class="card-title mb-1">Tabel Hasil Diagnosa</h4>
                                <p class="text-muted mb-0">Riwayat hasil konsultasi Z-Score + Theorema Bayes H1/H2/H3.</p>
                            </div>
                            <form class="admin-table-toolbar-actions" method="get" action="<?= base_url('adminhasildiagnosa'); ?>">
                                <div class="admin-filter-control">
                                    <label for="hasilKelasFilter">Kelas Bayes</label>
                                    <select id="hasilKelasFilter" name="kelas_hasil" class="form-control form-control-sm">
                                        <option value="">Semua Kelas</option>
                                        <?php foreach (($kelas_hasil_options ?? []) as $kodeKelas => $labelKelas): ?>
                                            <option value="<?= esc($kodeKelas, 'attr'); ?>" <?= ($filter['kelas_hasil'] ?? '') === $kodeKelas ? 'selected' : ''; ?>><?= esc($labelKelas); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
                                <a class="btn btn-light btn-sm" href="<?= base_url('adminhasildiagnosa'); ?>">Reset</a>
                                <a class="btn btn-primary btn-sm" href="<?= base_url('konsultasi') ?>">Konsultasi Baru</a>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover admin-data-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Anak</th>
                                        <th>NIK</th>
                                        <th>Umur</th>
                                        <th>Z-Score</th>
                                        <th>Kelas Bayes</th>
                                        <th>Posterior</th>
                                        <th>Gejala Terbaca</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($tb_hasil_diagnosa) && is_array($tb_hasil_diagnosa)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tb_hasil_diagnosa as $hasil): ?>
                                            <?php $gejalaDetail = $formatGejalaDetail($hasil['gejala_zscore'] ?? '[]'); ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><strong><?= esc($hasil['nama'] ?? '-'); ?></strong></td>
                                                <td><?= esc($hasil['nik'] ?? '-'); ?></td>
                                                <td><?= esc((string) ($hasil['umur'] ?? '-')); ?> bulan</td>
                                                <td>
                                                    <div class="small">BB/U: <?= esc($hasil['kategori_bb_u'] ?? '-'); ?> <?= isset($hasil['zs_bb_u']) ? '(' . esc((string) $hasil['zs_bb_u']) . ')' : ''; ?></div>
                                                    <div class="small">TB/U: <?= esc($hasil['kategori_tb_u'] ?? '-'); ?> <?= isset($hasil['zs_tb_u']) ? '(' . esc((string) $hasil['zs_tb_u']) . ')' : ''; ?></div>
                                                    <div class="small">BB/TB: <?= esc($hasil['kategori_bb_tb'] ?? '-'); ?> <?= isset($hasil['zs_bb_tb']) ? '(' . esc((string) $hasil['zs_bb_tb']) . ')' : ''; ?></div>
                                                </td>
                                                <td><?= esc($hasil['nama_kasus'] ?? 'Belum ada indikasi'); ?></td>
                                                <td>
                                                    <span class="badge badge-primary"><?= esc((string) ($hasil['persentase'] ?? 0)); ?>%</span>
                                                </td>
                                                <td>
                                                    <div><?= esc((string) ($hasil['jumlah_gejala'] ?? count($gejalaDetail))); ?> gejala</div>
                                                    <button type="button"
                                                        class="btn btn-outline-info btn-sm mt-2 btn-detail-gejala"
                                                        data-toggle="modal"
                                                        data-target="#detailGejalaModal"
                                                        data-nama="<?= esc($hasil['nama'] ?? '-', 'attr'); ?>"
                                                        data-gejala="<?= esc(json_encode($gejalaDetail, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), 'attr'); ?>">
                                                        Detail Gejala
                                                    </button>
                                                </td>
                                                <td><?= esc($hasil['created_at'] ?? '-'); ?></td>
                                                <td>
                                                    <div class="admin-table-actions">
                                                        <?php if (!empty($hasil['id_anak'])): ?>
                                                            <button type="button"
                                                                class="btn btn-outline-primary btn-sm btn-lihat-anak"
                                                                data-toggle="modal"
                                                                data-target="#detailAnakModal"
                                                                data-url="<?= esc(base_url('adminanak?anak=' . $hasil['id_anak']), 'attr'); ?>"
                                                                data-nama="<?= esc($hasil['nama'] ?? '-', 'attr'); ?>"
                                                                data-umur="<?= esc((string) ($hasil['umur'] ?? '-'), 'attr'); ?>"
                                                                data-jk="<?= esc(($hasil['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : (($hasil['jenis_kelamin'] ?? '') === 'P' ? 'Perempuan' : '-'), 'attr'); ?>"
                                                                data-bb="<?= esc((string) ($hasil['berat_badan'] ?? '-'), 'attr'); ?>"
                                                                data-tb="<?= esc((string) ($hasil['tinggi_badan'] ?? '-'), 'attr'); ?>">
                                                                Lihat Anak
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-light btn-sm" disabled>Data Anak</button>
                                                        <?php endif; ?>
                                                        <?php if ($canDeleteHasilDiagnosa): ?>
                                                            <a class="btn btn-danger btn-sm"
                                                                href="<?= base_url('admin/deleteHasilDiagnosa/' . ($hasil['id_hasil_diagnosa'] ?? 0)); ?>"
                                                                onclick="return confirm('Apakah Anda yakin ingin menghapus hasil diagnosa ini? Data anak terkait tidak akan ikut terhapus.');">Hapus</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr class="admin-empty-row">
                                            <td colspan="10" class="text-center text-muted py-4">Belum ada hasil diagnosa tersimpan.</td>
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

    <div class="modal fade" id="detailGejalaModal" tabindex="-1" role="dialog" aria-labelledby="detailGejalaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailGejalaModalLabel">Detail Gejala</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3" id="detailGejalaSubtitle">Gejala yang masuk perhitungan hasil diagnosa.</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 90px;">Kode</th>
                                    <th>Nama Gejala</th>
                                    <th>Indikator</th>
                                    <th>Kategori/Jawaban</th>
                                    <th style="width: 110px;">Z-Score</th>
                                </tr>
                            </thead>
                            <tbody id="detailGejalaBody">
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Pilih hasil diagnosa untuk melihat detail gejala.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailAnakModal" tabindex="-1" role="dialog" aria-labelledby="detailAnakModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailAnakModalLabel">Data Anak</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <th style="width: 160px;">Nama</th>
                                    <td id="detailAnakNama">-</td>
                                </tr>
                                <tr>
                                    <th>Umur</th>
                                    <td id="detailAnakUmur">-</td>
                                </tr>
                                <tr>
                                    <th>Jenis Kelamin</th>
                                    <td id="detailAnakJk">-</td>
                                </tr>
                                <tr>
                                    <th>Berat Badan</th>
                                    <td id="detailAnakBb">-</td>
                                </tr>
                                <tr>
                                    <th>Tinggi Badan</th>
                                    <td id="detailAnakTb">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
                    <a class="btn btn-primary" id="detailAnakLink" href="<?= base_url('adminanak'); ?>">Lihat Detail Anak</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#detailAnakModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                var umur = button.attr('data-umur') || '-';
                var beratBadan = button.attr('data-bb') || '-';
                var tinggiBadan = button.attr('data-tb') || '-';

                modal.find('#detailAnakNama').text(button.attr('data-nama') || '-');
                modal.find('#detailAnakUmur').text(umur !== '-' ? umur + ' bulan' : '-');
                modal.find('#detailAnakJk').text(button.attr('data-jk') || '-');
                modal.find('#detailAnakBb').text(beratBadan !== '-' ? beratBadan + ' kg' : '-');
                modal.find('#detailAnakTb').text(tinggiBadan !== '-' ? tinggiBadan + ' cm' : '-');
                modal.find('#detailAnakLink').attr('href', button.attr('data-url') || '<?= base_url('adminanak'); ?>');
            });

            $('#detailGejalaModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var nama = button.attr('data-nama') || '-';
                var rawGejala = button.attr('data-gejala') || '[]';
                var gejala = [];

                try {
                    gejala = JSON.parse(rawGejala);
                } catch (error) {
                    gejala = [];
                }

                var modal = $(this);
                var tbody = modal.find('#detailGejalaBody').empty();
                modal.find('#detailGejalaSubtitle').text('Gejala yang dipilih/terbaca untuk ' + nama + '.');

                if (!Array.isArray(gejala) || gejala.length === 0) {
                    tbody.append($('<tr>').append(
                        $('<td>').attr('colspan', 5).addClass('text-center text-muted').text('Belum ada gejala yang tersimpan.')
                    ));
                    return;
                }

                gejala.forEach(function (item) {
                    tbody.append(
                        $('<tr>')
                            .append($('<td>').text(item.kode || '-'))
                            .append($('<td>').text(item.nama || '-'))
                            .append($('<td>').text(item.indikator || '-'))
                            .append($('<td>').text(item.kategori || '-'))
                            .append($('<td>').text(item.zscore !== null && item.zscore !== undefined && item.zscore !== '' ? item.zscore : '-'))
                    );
                });
            });
        });
    </script>

    <?= $this->include('layout/dashboard/footer') ?>
