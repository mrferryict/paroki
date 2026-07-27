<?php

declare(strict_types=1);

/**
 * SVG icon map — keys match kolom `icon` di database (seeders / admin).
 * Dipakai oleh Alpine via window.PAROKI_ICONS.
 */
$iconAttrs = 'class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"';

$icons = [
    'default' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><circle cx="12" cy="12" r="9"/><path d="M12 8v4l2 2"/></svg>',
    'liturgi' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><path d="M12 3v18M8 7h8M9 21h6"/><path d="M7 11h10"/></svg>',
    'diakonia' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><path d="M12 21s-6-4.5-6-9a6 6 0 0 1 12 0c0 4.5-6 9-6 9z"/><path d="M12 11v2"/></svg>',
    'koinonia' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><circle cx="9" cy="8" r="3"/><circle cx="15" cy="8" r="3"/><path d="M4 20c0-3 2.5-5 5-5M15 15c2.5 0 5 2 5 5"/></svg>',
    'kerygma' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><path d="M4 19V5l16 7-16 7z"/></svg>',
    'baptis' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><path d="M12 3c-2 4-6 5-6 10a6 6 0 0 0 12 0c0-5-4-6-6-10z"/></svg>',
    'komuni-pertama' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><circle cx="12" cy="12" r="9"/><path d="M8 12h8M12 8v8"/></svg>',
    'krisma' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><path d="M12 2l2 6h6l-5 4 2 6-5-4-5 4 2-6-5-4h6z"/></svg>',
    'tobat' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><path d="M12 3v4M8 7h8"/><path d="M6 21h12l-2-10H8l-2 10z"/></svg>',
    'perkawinan' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><path d="M7 11a5 5 0 0 1 10 0"/><path d="M5 21h14M12 11v10"/></svg>',
    'pengurapan' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><path d="M12 3v3M9 6h6"/><path d="M8 21h8l-1-8H9l-1 8z"/></svg>',
    'misdinar' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><path d="M12 2v20M8 6h8M6 10h12"/></svg>',
    'konsultasi-psikologi' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><circle cx="12" cy="8" r="3"/><path d="M5 21c0-4 3-7 7-7s7 3 7 7"/><path d="M16 11l2 2"/></svg>',
    'konsultasi-hukum' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><path d="M12 3l7 4v6c0 4-3 7-7 8-4-1-7-4-7-8V7l7-4z"/></svg>',
    'administrasi' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><path d="M6 4h12v16H6z"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>',
    'download' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><path d="M12 3v12M8 11l4 4 4-4"/><path d="M4 19h16"/></svg>',
    'calendar' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',
    'clock' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
    'map-pin' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><path d="M12 21s6-5.5 6-11a6 6 0 0 0-12 0c0 5.5 6 11 6 11z"/><circle cx="12" cy="10" r="2"/></svg>',
    'phone' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><path d="M6 4h4l2 5-2 1a11 11 0 0 0 5 5l1-2 5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 4 6a2 2 0 0 1 2-2z"/></svg>',
    'mail' => '<svg viewBox="0 0 24 24" ' . $iconAttrs . '><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>',
];
?>
<script>
window.PAROKI_ICONS = <?= json_encode($icons, JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
