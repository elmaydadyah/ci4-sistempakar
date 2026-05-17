<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold">Standar Antropometri</h3>
                <h6 class="font-weight-normal mb-0 text-muted">Referensi median dan SD untuk perhitungan Z-Score BB/U, TB/U, dan BB/TB.</h6>
            </div>
        </div>

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
                        <p class="text-muted">Menampilkan data standar per indikator. Edit nilai median dan SD sesuai rujukan yang dipakai.</p>

                        <div class="btn-group mb-3" role="group" aria-label="Filter indikator standar antropometri">
                            <?php foreach ($indikator_options ?? ['TB/U', 'BB/U', 'BB/TB'] as $indikator): ?>
                                <?php $isActive = ($indikator_aktif ?? 'TB/U') === $indikator; ?>
                                <a
                                    class="btn btn-sm <?= $isActive ? 'btn-primary' : 'btn-outline-primary'; ?>"
                                    href="<?= base_url('adminstandar') . '?indikator=' . urlencode($indikator); ?>">
                                    <?= esc($indikator); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Indikator</th>
                                        <th>JK</th>
                                        <th>Umur</th>
                                        <th>Tinggi</th>
                                        <th>Median</th>
                                        <th>SD</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = (((int) ($page ?? 1) - 1) * (int) ($per_page ?? 10)) + 1; ?>
                                    <?php foreach ($tb_standar ?? [] as $row): ?>
                                        <tr>
                                            <form action="<?= base_url('admin/updateStandar/' . $row['id_standar']); ?>" method="post">
                                                <?= csrf_field() ?>
                                                <td><?= esc((string) $no++); ?></td>
                                                <td><?= esc($row['indikator']); ?></td>
                                                <td><?= esc($row['jenis_kelamin']); ?></td>
                                                <td><?= $row['umur_bulan'] !== null ? esc((string) $row['umur_bulan']) . ' bulan' : '-'; ?></td>
                                                <td><?= $row['tinggi_cm'] !== null ? esc((string) $row['tinggi_cm']) . ' cm' : '-'; ?></td>
                                                <td><input class="form-control form-control-sm" type="number" step="0.01" name="median" value="<?= esc((string) $row['median'], 'attr'); ?>" required></td>
                                                <td><input class="form-control form-control-sm" type="number" step="0.01" min="0.01" name="sd" value="<?= esc((string) $row['sd'], 'attr'); ?>" required></td>
                                                <input type="hidden" name="sumber" value="<?= esc($row['sumber'] ?? '', 'attr'); ?>">
                                                <input type="hidden" name="catatan" value="<?= esc($row['catatan'] ?? '', 'attr'); ?>">
                                                <td><button class="btn btn-primary btn-sm" type="submit">Simpan</button></td>
                                            </form>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($tb_standar)): ?>
                                        <tr><td colspan="8" class="text-center text-muted py-4">Data standar belum tersedia.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php
                            $page = (int) ($page ?? 1);
                            $perPage = (int) ($per_page ?? 10);
                            $totalRows = (int) ($total_rows ?? 0);
                            $totalPages = (int) ($total_pages ?? 1);
                            $start = $totalRows > 0 ? (($page - 1) * $perPage) + 1 : 0;
                            $end = min($page * $perPage, $totalRows);
                            $paginationUrl = static fn ($targetPage) => base_url('adminstandar') . '?indikator=' . urlencode((string) ($indikator_aktif ?? 'TB/U')) . '&page=' . $targetPage;
                            $firstPage = max(1, $page - 2);
                            $lastPage = min($totalPages, $page + 2);
                        ?>

                        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mt-3">
                            <div class="text-muted mb-2 mb-lg-0">
                                Menampilkan <?= esc((string) $start); ?> sampai <?= esc((string) $end); ?> dari <?= esc((string) $totalRows); ?> data
                            </div>

                            <nav aria-label="Pagination standar antropometri">
                                <ul class="pagination mb-0">
                                    <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="<?= $page <= 1 ? '#' : esc($paginationUrl($page - 1), 'attr'); ?>">Sebelumnya</a>
                                    </li>

                                    <?php if ($firstPage > 1): ?>
                                        <li class="page-item"><a class="page-link" href="<?= esc($paginationUrl(1), 'attr'); ?>">1</a></li>
                                        <?php if ($firstPage > 2): ?>
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php for ($i = $firstPage; $i <= $lastPage; $i++): ?>
                                        <li class="page-item <?= $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="<?= esc($paginationUrl($i), 'attr'); ?>"><?= esc((string) $i); ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($lastPage < $totalPages): ?>
                                        <?php if ($lastPage < $totalPages - 1): ?>
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                        <?php endif; ?>
                                        <li class="page-item"><a class="page-link" href="<?= esc($paginationUrl($totalPages), 'attr'); ?>"><?= esc((string) $totalPages); ?></a></li>
                                    <?php endif; ?>

                                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="<?= $page >= $totalPages ? '#' : esc($paginationUrl($page + 1), 'attr'); ?>">Berikutnya</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->include('layout/dashboard/footer') ?>
