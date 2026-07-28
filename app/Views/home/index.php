<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?= view('partials/hero') ?>
<?= view('partials/profil') ?>
<?= view('partials/jadwal', ['jadwalList' => $jadwalList ?? [], 'jenisJadwalLabels' => $jenisJadwalLabels ?? []]) ?>
<?= view('partials/layanan', ['sakramenFormOptions' => $sakramenFormOptions ?? []]) ?>
<?= view('partials/berita') ?>
<?= view('partials/katekese') ?>
<?= view('partials/kontak') ?>
<?= view('partials/footer') ?>
<?= $this->endSection() ?>
