<?php
$profileNama = session()->get('nama') ?? 'Admin';
$stats = $dashboardStats ?? [];
$nutritionStatus = $nutritionStatus ?? [];
$recentNutritionRows = $recentNutritionRows ?? [];
$diagnosisDailyChart = $diagnosisDailyChart ?? ['labels' => [], 'values' => []];
$recentDiagnosisRows = $recentDiagnosisRows ?? [];

$totalStatusGizi = (int) ($stats['status_gizi'] ?? 0);
$riskCount = (int) ($nutritionStatus['gizi_kurang'] ?? 0) + (int) ($nutritionStatus['pendek'] ?? 0);
$riskPercent = $totalStatusGizi > 0 ? min(100, round(($riskCount / $totalStatusGizi) * 100)) : 0;
$diagnosisTotal = (int) ($stats['hasil_diagnosa'] ?? 0);
$gejalaTotal = (int) ($stats['gejala'] ?? 0);
$kasusTotal = (int) ($stats['kasus'] ?? 0);

$summaryCards = [
  [
    'title' => 'Data Anak',
    'subtitle' => 'Status gizi tersimpan',
    'value' => $totalStatusGizi,
    'percent' => $totalStatusGizi > 0 ? 100 : 0,
    'tone' => 'primary',
    'href' => base_url('adminstatusgizi'),
    'icon' => 'ti-user',
  ],
  [
    'title' => 'Hasil Diagnosa',
    'subtitle' => 'Konsultasi tersimpan',
    'value' => $diagnosisTotal,
    'percent' => min(100, $diagnosisTotal * 10),
    'tone' => 'danger',
    'href' => base_url('adminkonsultasi'),
    'icon' => 'ti-pulse',
  ],
  [
    'title' => 'Data Gejala',
    'subtitle' => 'Basis pengetahuan',
    'value' => $gejalaTotal,
    'percent' => min(100, $gejalaTotal * 8),
    'tone' => 'muted',
    'href' => base_url('admingejala'),
    'icon' => 'ti-clipboard',
  ],
  [
    'title' => 'Data Penyakit',
    'subtitle' => 'Kasus rujukan',
    'value' => $kasusTotal,
    'percent' => min(100, $kasusTotal * 16),
    'tone' => 'success',
    'href' => base_url('adminpenyakit'),
    'icon' => 'ti-heart',
  ],
];
?>

<div class="main-panel">
  <div class="content-wrapper admin-dashboard">
    <div class="admin-board">
      <div class="admin-main-column">
        <section class="admin-welcome-panel">
          <div>
            <span>Dashboard Admin</span>
            <h3>Halo, <?= esc($profileNama); ?>!</h3>
            <p>Kelola data StuntCare, pantau hasil diagnosa harian, dan lihat status gizi anak dalam satu ringkasan.</p>
            <a href="<?= base_url('adminstatusgizi'); ?>">Kelola data</a>
          </div>
          <div class="admin-live-widget" aria-label="Waktu saat ini">
            <small id="dashboardDate">-</small>
            <strong id="dashboardClock">--:--</strong>
            <span id="dashboardGreeting">Selamat bekerja</span>
          </div>
        </section>

        <section>
          <div class="admin-section-heading">
            <h4>Ringkasan Sistem</h4>
            <a href="<?= base_url('adminstatusgizi'); ?>">lihat semua</a>
          </div>

          <div class="admin-summary-grid">
            <?php foreach ($summaryCards as $card): ?>
              <a class="admin-summary-card" href="<?= esc($card['href'], 'attr'); ?>">
                <div>
                  <strong><?= number_format((int) $card['value'], 0, ',', '.'); ?></strong>
                  <span><?= esc($card['title']); ?></span>
                  <small><?= esc($card['subtitle']); ?></small>
                </div>
                <div class="admin-card-icon admin-card-icon-<?= esc($card['tone'], 'attr'); ?>">
                  <i class="<?= esc($card['icon'], 'attr'); ?>"></i>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        </section>

        <section class="admin-panel admin-chart-panel">
          <div class="admin-section-heading">
            <div>
              <h4>Grafik Hasil Diagnosa</h4>
              <p>Jumlah hasil diagnosa per hari dalam 7 hari terakhir.</p>
            </div>
            <div class="admin-chart-actions" aria-label="Kontrol grafik hasil diagnosa">
              <button type="button" class="is-active" data-chart-type="line">Line</button>
              <button type="button" data-chart-type="bar">Bar</button>
              <span><?= number_format($diagnosisTotal, 0, ',', '.'); ?> total</span>
            </div>
          </div>
          <div class="admin-chart-wrap">
            <canvas id="diagnosisDailyChart"></canvas>
          </div>
        </section>

        <section class="admin-panel">
          <div class="admin-section-heading">
            <div>
              <h4>Pemantauan Status Gizi</h4>
              <p>Lima data status gizi terbaru yang masuk ke sistem.</p>
            </div>
            <div class="admin-heading-actions">
              <input type="search" id="nutritionSearch" class="admin-search-input" placeholder="Cari anak">
              <a href="<?= base_url('adminstatusgizi'); ?>">lihat detail</a>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table admin-progress-table">
              <thead>
                <tr>
                  <th>Nama Anak</th>
                  <th>Lokasi</th>
                  <th>Status</th>
                  <th>Tanggal</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($recentNutritionRows)): ?>
                  <?php foreach ($recentNutritionRows as $row): ?>
                    <tr class="admin-nutrition-row" data-search="<?= esc(strtolower(trim(($row['nama'] ?? '') . ' ' . ($row['posyandu'] ?? '') . ' ' . ($row['bb_tb'] ?? '') . ' ' . ($row['tanggal_pengukuran'] ?? ''))), 'attr'); ?>">
                      <td><?= esc($row['nama'] ?? '-'); ?></td>
                      <td><?= esc($row['posyandu'] ?? '-'); ?></td>
                      <td>
                        <span class="admin-status-dot"></span>
                        <?= esc($row['bb_tb'] ?? '-'); ?>
                      </td>
                      <td><?= esc($row['tanggal_pengukuran'] ?? '-'); ?></td>
                      <td><i class="ti-more-alt"></i></td>
                    </tr>
                  <?php endforeach; ?>
                  <tr id="nutritionNoResult" class="d-none">
                    <td colspan="5" class="text-center text-muted py-4">Data tidak ditemukan.</td>
                  </tr>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada data status gizi.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>

      <aside class="admin-side-column">
        <section class="admin-panel admin-calendar-panel">
          <div class="admin-calendar-head">
            <button type="button" id="calendarPrevMonth" aria-label="Bulan sebelumnya">
              <i class="ti-angle-left"></i>
            </button>
            <strong id="calendarTitle"><?= esc(date('F Y')); ?></strong>
            <button type="button" id="calendarNextMonth" aria-label="Bulan berikutnya">
              <i class="ti-angle-right"></i>
            </button>
          </div>
          <div class="admin-calendar-grid admin-calendar-days">
            <span>MON</span>
            <span>TUE</span>
            <span>WED</span>
            <span>THU</span>
            <span>FRI</span>
            <span>SAT</span>
            <span>SUN</span>
          </div>
          <div class="admin-calendar-grid" id="realtimeCalendarDays"></div>
          <p class="admin-selected-date" id="selectedDateText">Pilih tanggal pada kalender.</p>
        </section>

        <section class="admin-panel admin-diagnosis-panel">
          <div class="admin-section-heading">
            <h4>Diagnosa Terbaru</h4>
            <div class="admin-heading-actions">
              <input type="search" id="diagnosisSearch" class="admin-search-input" placeholder="Cari diagnosa">
              <a href="<?= base_url('adminkonsultasi'); ?>">lihat semua</a>
            </div>
          </div>

          <div class="admin-applicant-list">
            <?php if (!empty($recentDiagnosisRows)): ?>
              <?php foreach ($recentDiagnosisRows as $row): ?>
                <?php $initial = strtoupper(substr((string) ($row['nama'] ?? 'A'), 0, 1)); ?>
                <div class="admin-applicant-item" data-search="<?= esc(strtolower(trim(($row['nama'] ?? '') . ' ' . ($row['nama_kasus'] ?? ''))), 'attr'); ?>">
                  <div class="admin-avatar"><?= esc($initial); ?></div>
                  <div>
                    <strong><?= esc($row['nama'] ?? '-'); ?></strong>
                    <span><?= esc($row['nama_kasus'] ?? 'Belum ada kecocokan'); ?></span>
                  </div>
                  <div class="admin-action-icons">
                    <a href="<?= base_url('adminkonsultasi'); ?>" aria-label="Lihat diagnosa"><i class="ti-eye"></i></a>
                    <a href="<?= base_url('adminkonsultasi'); ?>" aria-label="Analisis diagnosa"><i class="ti-bar-chart"></i></a>
                    <a href="<?= base_url('adminkonsultasi'); ?>" aria-label="Tandai diagnosa"><i class="ti-check"></i></a>
                  </div>
                </div>
              <?php endforeach; ?>
              <div id="diagnosisNoResult" class="admin-empty-state d-none">Diagnosa tidak ditemukan.</div>
            <?php else: ?>
              <div class="admin-empty-state">Belum ada hasil diagnosa tersimpan.</div>
            <?php endif; ?>
          </div>
        </section>

        <section class="admin-panel admin-risk-panel">
          <span>Perlu Perhatian</span>
          <strong><?= esc((string) $riskPercent); ?>%</strong>
          <p><?= number_format($riskCount, 0, ',', '.'); ?> indikator dari data status gizi perlu dipantau.</p>
        </section>
      </aside>
    </div>
  </div>

  <script>
    window.addEventListener('load', function () {
      var calendarTitle = document.getElementById('calendarTitle');
      var calendarDays = document.getElementById('realtimeCalendarDays');
      var calendarPrev = document.getElementById('calendarPrevMonth');
      var calendarNext = document.getElementById('calendarNextMonth');
      var selectedDateText = document.getElementById('selectedDateText');
      var visibleMonth = new Date();
      var selectedDate = new Date();
      visibleMonth.setDate(1);

      function renderLiveClock() {
        var now = new Date();
        var clockElement = document.getElementById('dashboardClock');
        var dateElement = document.getElementById('dashboardDate');
        var greetingElement = document.getElementById('dashboardGreeting');
        var hour = now.getHours();

        if (clockElement) {
          clockElement.textContent = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
          });
        }

        if (dateElement) {
          dateElement.textContent = now.toLocaleDateString('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
          });
        }

        if (greetingElement) {
          greetingElement.textContent = hour < 11 ? 'Selamat pagi' : hour < 15 ? 'Selamat siang' : hour < 18 ? 'Selamat sore' : 'Selamat malam';
        }
      }

      function formatDate(date) {
        return date.toLocaleDateString('id-ID', {
          weekday: 'long',
          day: 'numeric',
          month: 'long',
          year: 'numeric',
        });
      }

      function isSameDate(firstDate, secondDate) {
        return firstDate.getFullYear() === secondDate.getFullYear()
          && firstDate.getMonth() === secondDate.getMonth()
          && firstDate.getDate() === secondDate.getDate();
      }

      function renderRealtimeCalendar() {
        if (!calendarTitle || !calendarDays) {
          return;
        }

        var now = new Date();
        var year = visibleMonth.getFullYear();
        var month = visibleMonth.getMonth();
        var firstDay = new Date(year, month, 1);
        var daysInMonth = new Date(year, month + 1, 0).getDate();
        var firstDayOffset = (firstDay.getDay() + 6) % 7;

        calendarTitle.textContent = firstDay.toLocaleDateString('en-US', {
          month: 'long',
          year: 'numeric',
        });
        calendarDays.innerHTML = '';

        for (var emptyDay = 0; emptyDay < firstDayOffset; emptyDay++) {
          calendarDays.appendChild(document.createElement('span'));
        }

        for (var day = 1; day <= daysInMonth; day++) {
          var dayElement = document.createElement('button');
          var currentDate = new Date(year, month, day);

          dayElement.type = 'button';
          dayElement.textContent = day;
          dayElement.setAttribute('aria-label', formatDate(currentDate));
          dayElement.addEventListener('click', function () {
            selectedDate = new Date(
              parseInt(this.dataset.year, 10),
              parseInt(this.dataset.month, 10),
              parseInt(this.dataset.day, 10)
            );
            if (selectedDateText) {
              selectedDateText.textContent = 'Tanggal dipilih: ' + formatDate(selectedDate);
            }
            renderRealtimeCalendar();
          });
          dayElement.dataset.year = currentDate.getFullYear();
          dayElement.dataset.month = currentDate.getMonth();
          dayElement.dataset.day = currentDate.getDate();

          if (isSameDate(currentDate, now)) {
            dayElement.classList.add('is-today');
            dayElement.setAttribute('aria-current', 'date');
          }
          if (isSameDate(currentDate, selectedDate)) {
            dayElement.classList.add('is-selected');
          }

          calendarDays.appendChild(dayElement);
        }
      }

      if (calendarPrev) {
        calendarPrev.addEventListener('click', function () {
          visibleMonth.setMonth(visibleMonth.getMonth() - 1);
          renderRealtimeCalendar();
        });
      }

      if (calendarNext) {
        calendarNext.addEventListener('click', function () {
          visibleMonth.setMonth(visibleMonth.getMonth() + 1);
          renderRealtimeCalendar();
        });
      }

      renderLiveClock();
      setInterval(renderLiveClock, 30000);

      if (selectedDateText) {
        selectedDateText.textContent = 'Tanggal dipilih: ' + formatDate(selectedDate);
      }

      renderRealtimeCalendar();
      setInterval(function () {
        var now = new Date();
        visibleMonth = new Date(now.getFullYear(), now.getMonth(), 1);
        renderRealtimeCalendar();
      }, 60000);

      var chartElement = document.getElementById('diagnosisDailyChart');
      var diagnosisChart = null;

      if (chartElement && typeof Chart !== 'undefined') {
        diagnosisChart = new Chart(chartElement.getContext('2d'), {
          type: 'line',
          data: {
            labels: <?= json_encode($diagnosisDailyChart['labels'] ?? []); ?>,
            datasets: [{
              label: 'Hasil Diagnosa',
              data: <?= json_encode($diagnosisDailyChart['values'] ?? []); ?>,
              borderColor: '#4b49ac',
              backgroundColor: 'rgba(75, 73, 172, 0.12)',
              pointBackgroundColor: '#4b49ac',
              pointBorderColor: '#ffffff',
              pointBorderWidth: 2,
              pointRadius: 4,
              borderWidth: 3,
              lineTension: 0.35,
              fill: true,
            }],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
              display: false,
            },
            scales: {
              yAxes: [{
                ticks: {
                  beginAtZero: true,
                  precision: 0,
                },
                gridLines: {
                  color: 'rgba(123, 133, 153, 0.12)',
                  drawBorder: false,
                },
              }],
              xAxes: [{
                gridLines: {
                  display: false,
                },
              }],
            },
            tooltips: {
              callbacks: {
                label: function (tooltipItem) {
                  return tooltipItem.yLabel + ' hasil diagnosa';
                },
              },
            },
          },
        });

        document.querySelectorAll('[data-chart-type]').forEach(function (button) {
          button.addEventListener('click', function () {
            var chartType = button.getAttribute('data-chart-type');
            document.querySelectorAll('[data-chart-type]').forEach(function (item) {
              item.classList.toggle('is-active', item === button);
            });
            diagnosisChart.config.type = chartType;
            diagnosisChart.data.datasets[0].fill = chartType === 'line';
            diagnosisChart.data.datasets[0].backgroundColor = chartType === 'line'
              ? 'rgba(75, 73, 172, 0.12)'
              : 'rgba(75, 73, 172, 0.72)';
            diagnosisChart.update();
          });
        });
      }

      function bindDashboardSearch(inputId, itemSelector, emptySelector) {
        var input = document.getElementById(inputId);
        var items = document.querySelectorAll(itemSelector);
        var emptyState = document.querySelector(emptySelector);

        if (!input || !items.length) {
          return;
        }

        input.addEventListener('input', function () {
          var keyword = input.value.trim().toLowerCase();
          var visibleCount = 0;

          items.forEach(function (item) {
            var isVisible = item.getAttribute('data-search').indexOf(keyword) !== -1;
            item.classList.toggle('d-none', !isVisible);
            if (isVisible) {
              visibleCount += 1;
            }
          });

          if (emptyState) {
            emptyState.classList.toggle('d-none', visibleCount > 0);
          }
        });
      }

      bindDashboardSearch('nutritionSearch', '.admin-nutrition-row', '#nutritionNoResult');
      bindDashboardSearch('diagnosisSearch', '.admin-applicant-item', '#diagnosisNoResult');

      var resizeDashboard = function () {
        window.dispatchEvent(new Event('resize'));
        if (diagnosisChart && typeof diagnosisChart.resize === 'function') {
          diagnosisChart.resize();
        }
      };

      document.querySelectorAll('[data-toggle="minimize"]').forEach(function (button) {
        button.addEventListener('click', function () {
          setTimeout(resizeDashboard, 80);
          setTimeout(resizeDashboard, 320);
        });
      });

      if (window.MutationObserver) {
        var bodyObserver = new MutationObserver(function (mutations) {
          mutations.forEach(function (mutation) {
            if (mutation.attributeName === 'class') {
              setTimeout(resizeDashboard, 80);
            }
          });
        });
        bodyObserver.observe(document.body, {
          attributes: true,
          attributeFilter: ['class'],
        });
      }
    });
  </script>
