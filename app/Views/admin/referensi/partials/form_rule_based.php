<?php $nextRuleCode = $nextRuleCode ?? ($next_rule_code ?? 'RB001'); ?>

<div class="admin-empty-state text-left mb-3 rule-preview">
    Pilih hipotesis dan gejala untuk melihat ringkasan rule.
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Kode Rule</label>
            <input type="text" class="form-control" name="kode_rule" value="<?= esc($nextRuleCode, 'attr'); ?>" placeholder="RB001" required>
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label>Nama Rule</label>
            <input type="text" class="form-control" name="nama_rule" placeholder="H1 -> G02">
            <small class="text-muted">Boleh dikosongkan, sistem akan membuat nama dari hipotesis dan gejala.</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Kode Hipotesis</label>
            <select class="form-control" name="kode_hipotesis" required>
                <option value="">Pilih hipotesis</option>
                <?php foreach ($tb_hipotesis ?? [] as $hipotesis): ?>
                    <?php $kode = (string) ($hipotesis['kode_hipotesis'] ?? ''); ?>
                    <option value="<?= esc($kode, 'attr'); ?>">
                        <?= esc($kode . ' - ' . ($hipotesis['risiko_stunting'] ?? '')); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Kode Gejala</label>
            <select class="form-control" name="kode_gejala" required>
                <option value="">Pilih gejala</option>
                <?php foreach ($tb_gejala ?? [] as $gejala): ?>
                    <?php $kode = (string) ($gejala['kode_gejala'] ?? ''); ?>
                    <option value="<?= esc($kode, 'attr'); ?>">
                        <?= esc($kode . ' - ' . ($gejala['nama_gejala'] ?? '')); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Urutan</label>
            <input type="number" class="form-control" name="urutan" min="0" step="1" value="0">
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label>Catatan</label>
            <input type="text" class="form-control" name="catatan" placeholder="Contoh: Import dari Rule Base.xlsx">
        </div>
    </div>
</div>

<div class="form-check">
    <label class="form-check-label">
        <input type="checkbox" class="form-check-input" name="aktif" value="1" checked>
        Rule aktif dipakai sebagai basis aturan
    </label>
</div>
