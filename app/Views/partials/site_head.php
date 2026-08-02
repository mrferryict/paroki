<?php
$cssFile = FCPATH . 'assets/css/app.css';
$cssVersion = is_file($cssFile) ? (string) filemtime($cssFile) : '1';
?>
<link rel="icon" href="<?= base_url('favicon.svg') ?>" type="image/svg+xml">
<link rel="icon" href="<?= base_url('favicon.ico') ?>" sizes="any">
<link rel="apple-touch-icon" href="<?= base_url('favicon.svg') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/app.css?v=' . $cssVersion) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
