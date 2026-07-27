<div class="rounded-lg border border-red-200 bg-red-50 px-4 py-4">
    <p class="mb-2 font-medium text-red-900">Gagal mengirim formulir</p>
    <ul class="list-disc pl-5 text-sm text-red-800">
        <?php foreach ($errors as $error): ?>
            <li><?= esc(is_array($error) ? implode(' ', $error) : (string) $error) ?></li>
        <?php endforeach ?>
    </ul>
</div>
