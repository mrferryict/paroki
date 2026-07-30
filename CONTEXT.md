# CONTEXT.md — Situs Profil Paroki (Paroki Hati Kudus Yesus)

> File ini adalah CONTEXT.md sesuai §0 `.cursorrules` v5.2. Berisi hal-hal *spesifik proyek ini* —
> skema, modul, keputusan struktur. Untuk aturan cara menulis kode (arsitektur, layering, keamanan),
> rujuk `.cursorrules` di root yang sama.

## 1. Ringkasan Proyek

Website profil paroki Katolik. Ada dua sisi:
- **Publik** — beranda, profil paroki, jadwal misa, sakramen & pelayanan, berita & kegiatan,
  katekese & renungan, formulir pendaftaran layanan (di section Layanan), halaman unduhan dokumen.
- **Admin** (`/admin/*`, di belakang CodeIgniter Shield) — CRUD seluruh konten di atas.

Referensi tampilan & interaksi front-end: `paroki-landing.html` (prototipe statis HTML5 + Tailwind +
Alpine.js + HTMX yang sudah dibuat). Proyek ini **mengonversi** prototipe tersebut menjadi aplikasi
CodeIgniter 4 dengan data dinamis dari database — palet warna, tipografi (Cormorant Garamond + Work
Sans), dan set ikon SVG dari prototipe **dipakai ulang**, bukan didesain ulang.

## 2. Pengecualian / Non-Goals dari `.cursorrules`

- **Proyek ini BUKAN multi-tenant.** Satu instalasi = satu paroki. **Jangan** terapkan §5.3
  (`TenantContext`, scoping hierarki cabang/region) — tidak relevan di sini. Jangan buat helper
  scoping yang tidak dipakai.
- **§5.5 Domain Events belum diperlukan.** Skala proyek masih kecil (satu paroki, bukan multi-modul
  lintas layanan). Jangan bangun event bus di fase awal.
- **Peran (role) dimulai dari satu grup saja**: `admin` (akses penuh ke seluruh panel admin). Jangan
  bangun sistem role granular (mis. `komsos`, `sekretariat`) sebelum benar-benar dibutuhkan — sesuai
  §1.1 "jangan build untuk kebutuhan hipotetis".

## 3. Otentikasi (Shield)

- Satu grup: `admin`.
- Semua route `/admin/*` wajib login (`session` auth filter Shield) + izin grup `admin`.
- Login: `/cp`. Logout **wajib** ikuti §4.3 `.cursorrules` (exempt dari CSRF, di luar filter
  session, idempotent — jangan sampai idle session bikin logout gagal dengan error 403).

## 4. Modul & Skema Data

Semua tabel referensi/master pakai `$useSoftDeletes = true` kecuali dinyatakan lain (§6 Model rule).

### 4.1 `wilayah`
`id, nama, ketua_nama, ketua_kontak_cipher, ketua_kontak_hash, created_at, updated_at, deleted_at`
- `ketua_nama` / `ketua_kontak_*` di UI admin & publik ditampilkan sebagai **Koordinator** (kolom DB tetap `ketua_*`).
- `ketua_kontak_*` = **PII** (nomor telepon koordinator wilayah) → wajib cipher + HMAC hash per §6.
- Relasi: satu Wilayah punya banyak `lingkungan`.

### 4.2 `lingkungan`
`id, wilayah_id (FK), nama, ketua_nama, ketua_kontak_cipher (nullable), ketua_kontak_hash (nullable), created_at, updated_at, deleted_at`

### 4.3 `dewan_paroki_bidang` (4 Bidang DPH)
`id, kode (enum: liturgi|diakonia|koinonia|kerygma), nama_tampilan, deskripsi, icon, urutan`
- Tabel referensi tetap (4 baris) — **tidak perlu paginasi** (§6 Performance, pengecualian tabel kecil).

### 4.3a `dewan_paroki_penjabat` (Penjabat per bidang DPH)
`id, bidang_id (FK → dewan_paroki_bidang), nama, whatsapp_cipher, whatsapp_hash, urutan, created_at, updated_at, deleted_at`
- Satu bidang DPH boleh punya **lebih dari satu** penjabat.
- `nama` plaintext; `whatsapp_*` **wajib** cipher + HMAC hash (PII).
- Admin: kelola di `/admin/dewan-paroki` — tabel expandable per bidang, tombol **+ Penjabat**.
- Publik: nama penjabat ditampilkan di section Profil (DPH); nomor WA **tidak** ditampilkan di halaman publik.

### 4.4 `sakramen_jenis` (Layanan Paroki — satu tabel untuk semua jenis layanan formulir)
`id, kode (enum), grup (enum: sakramen|konsultasi|administrasi|petugas), nama, deskripsi, icon, urutan, is_active, deleted_at`

**Hierarki menu publik "Layanan":**
| Grup | Item |
| --- | --- |
| **Sakramen** (7) | baptis, komuni_pertama, krisma, tobat, perkawinan, pengurapan_orang_sakit, imamat |
| **Konsultasi** | konsultasi_hukum, konsultasi_psikologi |
| **Administrasi** | administrasi (Sekretariat) |
| **Petugas** | misdinar, pemazmur, prodiakon, organis |

- Tabel referensi kecil — tidak perlu paginasi.
- Mapping `kode` → `grup` didefinisikan di `App\Enums\SakramenJenisKode` (single source of truth).
- Admin route tetap `/admin/sakramen-jenis` (nama tabel); label UI: "Layanan Paroki".

### 4.5 `pendaftaran` (submission dari Formulir & Dokumen — **berisi PII**)
`id, nama_lengkap, whatsapp_cipher, whatsapp_hash, sakramen_jenis_id (FK, nullable), pesan (nullable), status (enum: baru|diproses|selesai|ditolak), created_at, updated_at`
- `nama_lengkap` boleh plaintext (§6: "Names/PIC labels may remain plaintext").
- `whatsapp_*` **wajib** cipher + HMAC hash.
- **List/index admin dilarang menampilkan nomor WA mentah** — hanya nama, jenis layanan, status,
  tanggal. WA hanya boleh didekripsi di halaman **detail** (authorized reveal), sesuai §6.

### 4.6 `berita` (Berita & Kegiatan)
`id, judul, slug (unique), kategori (enum: pengumuman|kegiatan_paroki|pelayanan_sosial|kegiatan_wilayah|liturgi), tags (nullable, comma-separated slug), ringkasan, konten, gambar_utama, status (enum: draft|terbit), tanggal_terbit (nullable), view_count, created_at, updated_at, deleted_at`
- Bisa tumbuh tanpa batas → **wajib paginasi** (§6 Performance).
- Halaman publik hanya menampilkan baris `status = terbit`; filter `?kategori=` dan `?tag=`.
- `gambar_utama` disimpan relatif terhadap `public/` (mis. `uploads/berita/{random}`) — bukan path absolut.

### 4.7 `artikel_kategori` (Katekese & Renungan — kategori dinamis)
`id, slug (unique), label, urutan, is_active, created_at, updated_at, deleted_at`
- Admin CRUD: `/admin/katekese-kategori` (menu sidebar **Kategori Katekese**).
- Kategori awal (seed): `artikel_iman`, `renungan_harian`, `orang_kudus`, `mutiara_biblika` — bisa ditambah/dinonaktifkan tanpa ubah kode.
- Nonaktifkan (`is_active = 0`) alih-alih hapus jika masih ada artikel terbit dengan slug kategori tersebut.

### 4.8 `artikel` (Katekese & Renungan — satu tabel untuk semua kategori)
`id, judul, slug (unique), kategori (VARCHAR slug → artikel_kategori.slug), konten, status (enum: draft|terbit), tanggal_terbit, view_count, created_at, updated_at, deleted_at`
- Wajib paginasi per kategori.
- Halaman publik hanya menampilkan baris `status = terbit`.
- URL detail: `/katekese/{kategori}/{slug}` — segment `kategori` **harus cocok** dengan kolom `kategori` di DB (validasi di `ArtikelService::findPublishedByKategoriAndSlug`).

### 4.9 `dokumen_kategori` (Unduhan — kategori dinamis)
`id, slug (unique), label, urutan, is_active, created_at, updated_at, deleted_at`
- Admin CRUD: `/admin/unduhan-kategori` (menu sidebar **Kategori Unduhan**).
- Kategori awal (seed): `formulir`, `warta_paroki`, `majalah`, `dokumen` — bisa ditambah/dinonaktifkan tanpa ubah kode.
- Hapus diblokir jika masih ada baris `dokumen` dengan slug kategori tersebut; nonaktifkan (`is_active = 0`) sebagai alternatif.
- Kategori nonaktif tidak tampil di situs publik `/unduhan` dan tidak bisa dipilih untuk unduhan baru.

### 4.10 `dokumen` (Dokumen & Materi Unduhan)
`id, nama, file_path, kategori (VARCHAR slug → dokumen_kategori.slug), download_count, created_at, updated_at, deleted_at`
- `file_path` disimpan **di luar `public/`** (§4.5) dan disajikan lewat route unduhan terkontrol —
  jangan expose path asli ke client (§6 Security: no raw resource URL).

- Admin label UI: **Unduhan** (`/admin/dokumen`); kelola kategori di **Kategori Unduhan**.

### 4.11 `galeri_event` + `galeri` (Galeri per acara)
**`galeri_event`:** `id, judul, slug (unique), urutan, view_count, created_at, updated_at, deleted_at`
**`galeri`:** `id, galeri_event_id (FK), jenis (enum: foto|video), file_path (nullable, relatif `public/` untuk foto), youtube_url (nullable), urutan, created_at, updated_at, deleted_at`
- Admin: `/admin/galeri` — kelola event (judul acara), lalu item foto (upload) atau video (URL YouTube saja).
- Upload foto otomatis di-resize maks **1200×900 px** (`App\Libraries\ImageResizer`).
- Publik: `GET /galeri` (indeks) dan `GET /galeri/{slug}` (detail event); `view_count` di-increment saat halaman detail event dibuka.
- Menu navigasi publik: **Galeri** (`site_header`).

### 4.12 `jadwal_misa`
`id, jenis (enum: harian|mingguan|jumat_pertama|khusus), hari_label, jam, catatan (nullable), urutan, is_active`
- Tabel referensi kecil — tidak perlu paginasi.

### 4.13 `hero_slide` (dulunya array JS statis `heroSlides` di prototipe — sekarang dikelola admin)
`id, eyebrow, judul, subjudul, cta1_label, cta1_href, cta2_label, cta2_href, gambar, urutan, is_active`
- `judul` boleh menyimpan `\n` literal untuk 2 baris, ditampilkan dengan `white-space:pre-line`
  seperti di prototipe — jangan diubah jadi 2 kolom terpisah, cukup satu kolom teks.

### 4.14 `site_setting` (singleton, `id = 1`)
`id, logo_path (nullable, relatif `public/uploads/branding/`), created_at, updated_at`
- Dikelola lewat `/admin/pengaturan`; logo ditampilkan di menubar publik (`site_header`).

## 5. Rute (garis besar)

**Publik**
- `GET /` — Beranda (hero + seluruh section, sesuai `paroki-landing.html`); data dari `HomeService::getLandingData()`
- `GET /berita` — arsip berita terbit (paginasi 12/halaman); filter opsional `?kategori=` dan `?tag=`
- `GET /berita/{slug}` — detail berita terbit
- `GET /katekese` — arsip semua artikel terbit
- `GET /katekese/{kategori}` — arsip per kategori (`artikel_iman|renungan_harian|orang_kudus|mutiara_biblika`)
- `GET /katekese/{kategori}/{slug}` — detail artikel terbit
- `GET /galeri` — halaman galeri publik (indeks event)
- `GET /galeri/{slug}` — detail event galeri (increment `view_count`)
- `GET /unduhan` — halaman unduhan publik; filter opsional `?kategori=` (`formulir|warta_paroki|majalah|dokumen`)
- `GET /dokumen/{id}/unduh` — download terkontrol, bukan path publik langsung
- `POST /formulir` — simpan ke `pendaftaran` dari section Layanan beranda; respons HTMX partial (§4.4)

Controller publik: `Home`, `BeritaController`, `KatekeseController`, `GaleriController`, `UnduhanController`, `FormulirController`, `DokumenController`.
Urutan route `katekese`: detail (`/{kategori}/{slug}`) didaftarkan **sebelum** arsip per kategori (`/{kategori}`).

**Admin** (prefix `/admin`, di belakang Shield)
- `/admin/wilayah`, `/admin/wilayah/{id}/lingkungan`
- `/admin/dewan-paroki` (+ penjabat per bidang)
- `/admin/sakramen-jenis`
- `/admin/pendaftaran` (list tanpa WA mentah), `/admin/pendaftaran/{id}` (detail + reveal WA)
- `/admin/berita`, `/admin/artikel`, `/admin/katekese-kategori`, `/admin/dokumen` (label **Unduhan**), `/admin/unduhan-kategori`, `/admin/jadwal-misa`, `/admin/galeri`,
  `/admin/hero-slide`, `/admin/pengaturan` (logo menubar) — CRUD / pengaturan masing-masing

## 6. Frontend

- Struktur View:
  - `app/Views/layouts/main.php` — layout **beranda** one-page (Alpine `landingPage()`, semua section)
  - `app/Views/layouts/public.php` — layout **arsip & detail** (berita, katekese, unduhan): header/footer seragam dengan beranda
  - `app/Views/layouts/admin.php` — layout admin
  - `app/Views/partials/site_header.php`, `site_nav_scripts.php` — menubar tetap (Profil, Jadwal, Layanan, Berita, Katekese, Galeri, Unduhan) + CTA WhatsApp & Bagikan
  - `app/Views/partials/*` — section beranda (hero, profil, jadwal, layanan + pendaftaran, berita, katekese, kontak, footer)
  - `app/Views/berita/*`, `app/Views/katekese/*`, `app/Views/galeri/*`, `app/Views/unduhan/*` — halaman arsip/detail konten
  - `app/Views/partials/public_pagination.php` — paginasi halaman publik
- Ganti seluruh array JS statis di prototipe (`heroSlides`, `bidangDPH`, `wilayahList`,
  `layananList`, `layananGrup`, `beritaList`, `katekeseList`) dengan data dari Controller,
  di-passing ke View lalu ke Alpine via `json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT)`.
- Halaman arsip/detail **tidak** memakai Alpine data landing — render server-side PHP; card/list memakai
  `BeritaService::mapForPublicCard()` / `ArtikelService::mapForPublicCard()`.
- Konten `ringkasan`/`konten` berita & artikel dari admin saat ini plain text (textarea) — tampilkan
  dengan `esc()` + `whitespace-pre-line`, bukan raw HTML.
- Form admin (CRUD) pakai partial + HTMX swap (§4.4) — bukan reload halaman penuh.
- Tailwind: Play CDN untuk pengembangan, tapi build produksi **wajib** pindah ke Vite/PostCSS
  (§2 Frontend stack) sebelum go-live — jangan ship Play CDN ke production.
- Palet warna admin saat ini `#722F37` (maroon); prototipe `paroki-landing.html` memakai `#6B1220` —
  penyelarasan pixel-perfect dengan prototipe masih outstanding.

## 7. Catatan PII Khusus Proyek Ini

Field yang **wajib** dienkripsi (§6 PII — mandatory di semua proyek di bawah `.cursorrules` ini):
- `pendaftaran.whatsapp` → `whatsapp_cipher` + `whatsapp_hash`
- `wilayah.ketua_kontak` dan `lingkungan.ketua_kontak` → `*_cipher` + `*_hash`
- `dewan_paroki_penjabat.whatsapp` → `whatsapp_cipher` + `whatsapp_hash`

Field yang **boleh** plaintext: semua kolom `nama`/`nama_lengkap`/`ketua_nama` (§6: "Names/PIC labels
may remain plaintext").

Hosting **wajib** mengaktifkan `ext-sodium` — dokumentasikan ini di README deployment. Kunci enkripsi
(`pii.key`) disimpan di `.env`, tidak pernah di kode.

**Konfigurasi `pii.key` (wajib di setiap environment):**
- Generate: `php -r "echo sodium_bin2base64(random_bytes(32), SODIUM_BASE64_VARIANT_ORIGINAL);"`
- Salin ke `.env`: `pii.key = {base64}`
- Tanpa key ini, `PiiCipher` gagal saat konstruktor — termasuk saat beranda memuat `HomeService` →
  `WilayahService` (meskipun section publik tidak mendekripsi PII).
- **Satu database = satu `pii.key` identik** di semua server yang share DB; backup key di tempat aman
  terpisah dari repo. Key beda = data terenkripsi tidak bisa dibuka.

## 8. Keputusan Teknis Proyek

- **Entity `Berita` / `Artikel`:** kolom `kategori` dan `status` **tidak** di-cast ke backed enum di
  `$casts` — CI4 `DataCaster` belum menangani enum class. Konversi ke `BeritaKategori` /
  `ArtikelKategori` / `PublishStatus` dilakukan di Service via `tryFrom()` / perbandingan string.
- **Format tanggal UI:** `Time::parse($raw, null, 'id_ID')->toLocalizedString('d MMM yyyy')` — jangan
  pakai `setLocale()` (tidak tersedia di CI4 `Time`).
- **Read publik berita/artikel:** `findPublishedPaginated()` memaksa filter `status = terbit`; mapping
  card/detail di Service (`mapForPublicCard`, `mapForPublicDetail`), bukan di View.

## 9. Seeder & Data Contoh

- `BeritaArtikelSeeder` — 3 berita + 4 artikel sample (satu per kategori artikel), semua `terbit`.
  Idempotent: dilewati jika tabel sudah berisi baris. Dipanggil dari `DatabaseSeeder`.
- Jalankan: `php spark db:seed BeritaArtikelSeeder` (atau `php spark db:seed DatabaseSeeder`).

## 10. Urutan Pembangunan Modul (disarankan)

1. Bootstrap proyek + Shield + konfigurasi dasar ✅
2. Seluruh migration (skema di atas) ✅
3. Library `PiiCipher` (Sodium) + test roundtrip — **sebelum** modul yang butuh field terenkripsi ✅
4. Modul referensi kecil dulu (hero_slide, jadwal_misa, sakramen_jenis, dewan_paroki_bidang) ✅
5. Wilayah & Lingkungan (Repository + PiiCipher untuk kontak ketua) ✅
6. Berita, Artikel, Galeri, Dokumen — CRUD admin ✅; view publik berita/katekese + paginasi ✅;
   unduhan dokumen ✅; galeri publik (`/galeri`) ✅
7. Pendaftaran (PII, enkripsi, admin reveal, status workflow) ✅
8. Beranda (`HomeService` + partials) ✅; halaman arsip/detail berita & katekese ✅; HTMX formulir ✅
9. Outstanding: selaraskan partial beranda dengan `paroki-landing.html` (palet/SVG); build Tailwind produksi (Vite); testing menyeluruh +
   checklist §8 `.cursorrules` + persiapan deployment

## 11. Catatan Insiden & Checklist (agar tidak terulang)

Ringkasan bug nyata yang muncul saat pengembangan proyek ini, gejala di browser/log, dan pencegahannya.
Gunakan sebagai checklist sebelum merge/deploy fitur admin HTMX + upload file.

### 11.1 PageCache men-cache form admin HTMX + CSRF basi

| | |
| --- | --- |
| **Gejala** | Klik **Simpan** pada form admin (mis. berita + gambar) — tidak ada reaksi, tidak ada pesan error. |
| **Log** | `SecurityException: The action you requested is not allowed` (403), sering setelah percobaan submit pertama. |
| **Penyebab** | Filter `pagecache` (CI4 default `$required`) meng-cache respons GET partial HTMX (`/admin/berita/new`, dll.) **beserta token CSRF di HTML**. Setelah POST apa pun, `Security.regenerate = true` mengganti token session; form cached masih token lama. |
| **Perbaikan** | `pagecache` dipindah ke `$globals` dengan **`except: admin, admin/*, cp, cp/*, auth/*, logout`**. Kosongkan `writable/cache/` setelah deploy config ini. |
| **Checklist** | Jangan page-cache route admin/auth. Uji: buka form HTMX → submit → harus redirect/success atau validation error terlihat. |

### 11.2 CSRF HTMX + multipart (upload gambar)

| | |
| --- | --- |
| **Gejala** | Submit pertama gagal (500/403); submit berikutnya selalu 403 sampai hard refresh. |
| **Penyebab** | (1) Meta `csrf-token` di layout admin tidak di-update setelah partial swap; header HTMX kirim token stale. (2) Setelah request gagal setelah CSRF verified, token session sudah regenerate tapi form di panel masih token lama. |
| **Perbaikan** | `htmx:configRequest` ambil token dari **input hidden form** (`csrf_test_name`) dulu, baru fallback meta. `htmx:afterSwap` sync meta dari token di partial baru. Handler `htmx:responseError` tampilkan pesan HTTP di panel form. |
| **Checklist** | Setiap layout dengan HTMX POST wajib sync CSRF dari form aktif, bukan hanya meta statis halaman pertama. |

### 11.3 Izin folder upload `public/uploads/*` (HTTP 500)

| | |
| --- | --- |
| **Gejala** | `Permintaan gagal (HTTP 500)` saat upload gambar berita/galeri/hero. |
| **Log** | `ErrorException: mkdir(): Permission denied` di `BeritaService` / service upload lain (`FCPATH/uploads/...`). |
| **Penyebab** | Folder `public/uploads/berita` (dll.) belum ada; proses web (`www-data`) tidak bisa `mkdir` karena parent `public/uploads` milik user dev (`755`, bukan grup www-data). CI4 mengubah **warning** `mkdir()` jadi exception → 500, bukan pesan bisnis. |
| **Perbaikan** | Library `App\Libraries\PublicUploadDirectory::ensure()` (`@mkdir` + pesan jelas). Commit `.gitkeep` di `public/uploads/{berita,galeri,branding,hero}`. Set permission deploy: `chown -R deploy:www-data public/uploads && chmod -R 775 public/uploads`. |
| **Checklist** | Setelah clone/deploy: pastikan subfolder upload ada dan **web server bisa menulis**. Jangan andalkan `mkdir` runtime di production tanpa permission yang benar. |

### 11.4 Batas ukuran upload PHP vs validasi aplikasi

| | |
| --- | --- |
| **Gejala** | Validasi lolos / gagal aneh; file tidak ter-upload tanpa pesan jelas. |
| **Penyebab** | `upload_max_filesize` / `post_max_size` di PHP (contoh WSL: **2M**) lebih kecil dari `max_size` validasi CI4 (dulu 5120 KB). File besar tidak pernah sampai ke aplikasi. |
| **Perbaikan** | Validasi berita/galeri diselaraskan **max 2048 KB (2M)** + teks bantuan di form admin. Naikkan `upload_max_filesize` & `post_max_size` di `php.ini` jika butuh lebih besar — **selaraskan** dengan rule `max_size`. |
| **Checklist** | Saat menambah upload: cek `php -i | grep upload_max` di server target; rule `max_size` ≤ limit PHP. |

### 11.5 Wiring Service / route tidak lengkap setelah fitur baru

| | |
| --- | --- |
| **Gejala** | Halaman admin error 500 atau method/service tidak ditemukan setelah menambah modul (contoh: penjabat DPH). |
| **Penyebab** | Lupa register di `Config/Services.php`, route nested di `Routes.php`, atau inject dependency baru ke constructor Service (mis. `DewanParokiBidangService` butuh `PenjabatModel` + `PiiCipher`). |
| **Checklist** | Setiap modul baru: migration → Model/Entity → Service → **Services.php** → Controller → **Routes.php** → sidebar → smoke test URL admin + publik. Update unit test jika constructor Service berubah (contoh `HomeServiceTest` + `ArtikelKategoriService`). |

### 11.6 Migration belum dijalankan di environment

| | |
| --- | --- |
| **Gejala** | SQL error kolom/tabel tidak ada (`view_count`, `dokumen_kategori`, `galeri_event`, dll.). |
| **Checklist** | Setelah pull: `php spark migrate`. Jangan edit migration yang sudah jalan di staging/production (§4.7 `.cursorrules`) — buat migration baru. |

### 11.7 Respons error form admin hanya partial kecil

| | |
| --- | --- |
| **Gejala** | Setelah error upload, panel form hilang — hanya kotak merah satu baris; user mengira "tidak ada yang terjadi". |
| **Perbaikan** | `formErrorResponse()` di controller admin (contoh berita) **render ulang form lengkap** + banner error, jangan hanya `form_error.php` tanpa form. |
| **Checklist** | Semua admin HTMX POST yang bisa gagal (upload, validasi bisnis) harus mengembalikan form + pesan, bukan fragment error saja. |

### 11.8 Kategori dinamis (Katekese / Unduhan)

| | |
| --- | --- |
| **Pola** | Tabel `*_kategori` + slug VARCHAR di tabel konten; admin CRUD kategori terpisah; hapus diblokir jika masih dipakai; nonaktifkan (`is_active = 0`) untuk sembunyikan di publik. |
| **Checklist** | `kategoriOptions()` publik/admin hanya slug aktif; edit konten lama dengan kategori nonaktif tetap tampil di dropdown admin via `kategoriOptionsForAdmin($currentSlug)`. |
