<?= $this->include('layout/header') ?>
<?= $this->include('layout/navbar') ?>
<?= $this->include('layout/sidebar') ?>

<div class="main-panel">
  <div class="content-wrapper">

    <?= $this->renderSection('content') ?>

  </div>
</div>

<?= $this->include('layout/footer') ?>