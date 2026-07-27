<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?= view('partials/hero') ?>
<?= view('partials/profil') ?>
<?= view('partials/jadwal', ['jadwalList' => $jadwalList ?? [], 'jenisJadwalLabels' => $jenisJadwalLabels ?? []]) ?>
<?= view('partials/sakramen') ?>
<?= view('partials/berita') ?>
<?= view('partials/katekese') ?>
<?= view('partials/formulir', ['sakramenFormOptions' => $sakramenFormOptions ?? []]) ?>
<?= view('partials/kontak') ?>
<?= view('partials/footer') ?>
<?= $this->endSection() ?>
