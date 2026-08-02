# USERS_DEMO.md — Akun Demo Panel Admin

Akun berikut dibuat oleh `DemoUsersSeeder` untuk keperluan **demo dan pengembangan**.
Jangan gunakan password ini di production.

## Cara menjalankan seeder

```bash
php spark migrate
php spark db:seed DemoUsersSeeder
```

Seeder **idempotent** — aman dijalankan ulang; tidak membuat duplikat jika email sudah ada.

---

## Akun demo

| Peran | Grup Shield | Username | Email | Password |
|-------|-------------|----------|-------|----------|
| **Super Admin (owner)** | `superadmin` | `superadmin` | `superadmin@parokistmikaelgombong.or.id` | `SuperAdmin2026!` |
| **Editor konten** | `editor` | `editor` | `editor@parokistmikaelgombong.or.id` | `Editor2026!` |

**URL login:** `/cp`

---

## Perbedaan akses

| Fitur | Super Admin | Editor |
|-------|:-----------:|:------:|
| Berita, Katekese, Galeri, Unduhan, Hero | ✅ | ✅ |
| Wilayah, DPH, Jadwal, Pendaftaran, dll. | ✅ | ✅ |
| **Pengaturan Situs** (logo, nama paroki, copyright) | ✅ | ❌ |

Editor yang membuka `/admin/pengaturan` langsung akan dialihkan ke dashboard dengan pesan tidak cukup izin.

---

## Reset password manual

```bash
php spark shield:user password -e superadmin@parokistmikaelgombong.or.id
php spark shield:user password -e editor@parokistmikaelgombong.or.id
```
