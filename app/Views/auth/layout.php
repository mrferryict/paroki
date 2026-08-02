<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> — Paroki</title>
    <?= view('partials/site_head') ?>
    <?= $this->renderSection('pageStyles') ?>
</head>
<body class="min-h-screen bg-ivory font-sans text-stone-800 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <?= $this->renderSection('main') ?>
    </main>
    <?= $this->renderSection('pageScripts') ?>
</body>
</html>
