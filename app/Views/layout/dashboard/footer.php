<footer class="footer">
  <div class="d-sm-flex justify-content-center justify-content-sm-between">
    <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright &copy; 2026 StuntCare.</span>
    <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Admin sistem pakar stunting</span>
  </div>
</footer>
</div>
</div>
</div>

<script src="<?= base_url('assets/skydash/vendors/js/vendor.bundle.base.js') ?>"></script>
<script src="<?= base_url('assets/skydash/vendors/chart.js/Chart.min.js') ?>"></script>
<script src="<?= base_url('assets/skydash/vendors/datatables.net/jquery.dataTables.js') ?>"></script>
<script src="<?= base_url('assets/skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.js') ?>"></script>
<script src="<?= base_url('assets/skydash/js/dataTables.select.min.js') ?>"></script>
<script src="<?= base_url('assets/skydash/js/off-canvas.js') ?>"></script>
<script src="<?= base_url('assets/skydash/js/hoverable-collapse.js') ?>"></script>
<script src="<?= base_url('assets/skydash/js/template.js') ?>"></script>
<script src="<?= base_url('assets/skydash/js/Chart.roundedBarCharts.js') ?>"></script>
<script>
  (function ($) {
    if (!window.jQuery || !$.fn.DataTable) {
      return;
    }

    $('.admin-data-table').each(function () {
      var $table = $(this);
      var hasDataRows = $table.find('tbody tr').not('.admin-empty-row').length > 0;

      if (!hasDataRows || $.fn.DataTable.isDataTable(this)) {
        return;
      }

      $table.DataTable({
        pageLength: Number($table.data('page-length')) || 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'Semua']],
        ordering: $table.data('ordering') !== false,
        autoWidth: false,
        responsive: false,
        language: {
          search: 'Cari:',
          lengthMenu: 'Tampilkan _MENU_ data',
          info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
          infoEmpty: 'Tidak ada data',
          infoFiltered: '(difilter dari _MAX_ total data)',
          zeroRecords: 'Data tidak ditemukan',
          emptyTable: 'Data tidak tersedia',
          paginate: {
            first: 'Pertama',
            last: 'Terakhir',
            next: 'Berikutnya',
            previous: 'Sebelumnya'
          }
        },
        columnDefs: [
          { orderable: false, targets: 'admin-no-sort' }
        ]
      });
    });
  })(jQuery);
</script>
</body>

</html>
