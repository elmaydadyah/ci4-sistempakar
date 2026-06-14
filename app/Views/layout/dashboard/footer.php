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
  window.addEventListener('pageshow', function (event) {
    var navEntry = performance.getEntriesByType && performance.getEntriesByType('navigation')[0];
    var restoredFromBackCache = event.persisted || (navEntry && navEntry.type === 'back_forward');

    if (restoredFromBackCache) {
      window.location.reload();
    }
  });
</script>
<script>
  (function () {
    var pendingElement = null;
    var confirmOverlay = document.createElement('div');
    confirmOverlay.className = 'admin-confirm-overlay';
    confirmOverlay.innerHTML = [
      '<div class="admin-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="adminConfirmTitle">',
      '<h5 id="adminConfirmTitle">Konfirmasi Hapus</h5>',
      '<p id="adminConfirmMessage">Apakah Anda yakin ingin menghapus data ini?</p>',
      '<div class="admin-confirm-actions">',
      '<button type="button" class="admin-confirm-button admin-confirm-cancel" data-confirm-cancel>Cancel</button>',
      '<button type="button" class="admin-confirm-button admin-confirm-ok" data-confirm-ok>OK</button>',
      '</div>',
      '</div>'
    ].join('');
    document.body.appendChild(confirmOverlay);

    function getConfirmMessage(onclickValue) {
      var match = String(onclickValue || '').match(/confirm\((['"])(.*?)\1\)/);
      return match ? match[2] : 'Apakah Anda yakin ingin menghapus data ini?';
    }

    function closeConfirm() {
      confirmOverlay.classList.remove('is-visible');
      pendingElement = null;
    }

    document.addEventListener('click', function (event) {
      var target = event.target.closest('[onclick*="confirm("]');
      if (!target || target.dataset.confirmApproved === '1') {
        return;
      }

      event.preventDefault();
      event.stopPropagation();
      event.stopImmediatePropagation();

      pendingElement = target;
      document.getElementById('adminConfirmMessage').textContent = getConfirmMessage(target.getAttribute('onclick'));
      confirmOverlay.classList.add('is-visible');
      confirmOverlay.querySelector('[data-confirm-ok]').focus();
    }, true);

    confirmOverlay.querySelector('[data-confirm-cancel]').addEventListener('click', closeConfirm);
    confirmOverlay.addEventListener('click', function (event) {
      if (event.target === confirmOverlay) {
        closeConfirm();
      }
    });

    confirmOverlay.querySelector('[data-confirm-ok]').addEventListener('click', function () {
      if (!pendingElement) {
        closeConfirm();
        return;
      }

      var approvedElement = pendingElement;
      approvedElement.dataset.confirmApproved = '1';
      closeConfirm();

      if (approvedElement.tagName === 'A' && approvedElement.href) {
        window.location.href = approvedElement.href;
        return;
      }

      if (approvedElement.form) {
        approvedElement.form.submit();
        return;
      }

      approvedElement.click();
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && confirmOverlay.classList.contains('is-visible')) {
        closeConfirm();
      }
    });
  })();
</script>
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

      var dataTable = $table.DataTable({
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

      $table.closest('.dataTables_wrapper').find('div[id$=_filter] input').attr('placeholder', 'Cari');

      var filterSelector = $table.data('filter-select');
      var filterColumn = Number($table.data('filter-column'));
      if (filterSelector && !Number.isNaN(filterColumn)) {
        $(document).on('change', filterSelector, function () {
          var selectedValue = $.fn.dataTable.util.escapeRegex($(this).val() || '');
          dataTable
            .column(filterColumn)
            .search(selectedValue ? '^' + selectedValue + '$' : '', true, false)
            .draw();
        });
      }
    });
  })(jQuery);
</script>
</body>

</html>
