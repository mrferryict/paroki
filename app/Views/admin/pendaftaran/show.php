<?php /** @var \App\DTOs\Pendaftaran\PendaftaranDetailDto $detail */ ?>
<?php /** @var list<\App\Enums\PendaftaranStatus> $allowedNextStatuses */ ?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$status = $detail->pendaftaran->status instanceof \App\Enums\PendaftaranStatus
    ? $detail->pendaftaran->status
    : \App\Enums\PendaftaranStatus::from((string) $detail->pendaftaran->status);
?>
<div class="space-y-6">
    <a href="<?= site_url('admin/pendaftaran') ?>" class="text-sm text-maroon hover:underline">&larr; Kembali ke daftar</a>

    <?php if ($message = session()->getFlashdata('success')): ?>
        <div class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"><?= esc($message) ?></div>
    <?php endif ?>
    <?php if ($message = session()->getFlashdata('error')): ?>
        <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><?= esc($message) ?></div>
    <?php endif ?>

    <div class="rounded-lg border border-gold/20 bg-white p-6 shadow-sm">
        <h1 class="font-display text-3xl font-semibold text-maroon"><?= esc($detail->pendaftaran->nama_lengkap) ?></h1>

        <dl class="mt-6 grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">WhatsApp</dt>
                <dd class="mt-1 font-mono text-base text-stone-800"><?= esc($detail->whatsapp) ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Jenis Layanan</dt>
                <dd class="mt-1 text-base text-stone-800"><?= esc($detail->sakramenNama ?? '—') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Status</dt>
                <dd class="mt-1 text-base text-stone-800"><?= esc($statusOptions[$status->value] ?? $status->value) ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Tanggal Kirim</dt>
                <dd class="mt-1 text-base text-stone-800"><?= esc($detail->pendaftaran->created_at->toLocalizedString('d MMMM yyyy HH:mm')) ?></dd>
            </div>
        </dl>

        <?php if ($detail->pendaftaran->pesan): ?>
            <div class="mt-6">
                <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Pesan</dt>
                <dd class="mt-2 whitespace-pre-line rounded border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-700"><?= esc((string) $detail->pendaftaran->pesan) ?></dd>
            </div>
        <?php endif ?>

        <?php if ($allowedNextStatuses !== []): ?>
            <form method="post" action="<?= site_url('admin/pendaftaran/' . $detail->pendaftaran->id . '/status') ?>" class="mt-6 flex flex-wrap items-end gap-3 border-t border-stone-100 pt-6">
                <?= csrf_field() ?>
                <div>
                    <label for="status" class="mb-1 block text-sm font-medium">Ubah Status</label>
                    <select name="status" id="status" required class="rounded border border-stone-300 px-3 py-2 text-sm">
                        <?php foreach ($allowedNextStatuses as $nextStatus): ?>
                            <option value="<?= esc($nextStatus->value) ?>"><?= esc($statusOptions[$nextStatus->value] ?? $nextStatus->value) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <button type="submit" class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90">Simpan Status</button>
            </form>
        <?php endif ?>
    </div>
</div>
<?= $this->endSection() ?>
