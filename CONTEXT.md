# CONTEXT.md — Situs Profil Paroki (Paroki Hati Kudus Yesus)

> File ini adalah CONTEXT.md sesuai §0 `.cursorrules` v5.2. Berisi hal-hal *spesifik proyek ini* —
> skema, modul, keputusan struktur. Untuk aturan cara menulis kode (arsitektur, layering, keamanan),
> rujuk `.cursorrules` di root yang sama.

## 1. Ringkasan Proyek

Website profil paroki Katolik. Ada dua sisi:
- **Publik** — beranda, profil paroki, jadwal misa, sakramen & pelayanan, berita & kegiatan,
  katekese & renungan, formulir pendaftaran/kontak, unduhan dokumen.
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
- Login: `/login`. Logout **wajib** ikuti §4.3 `.cursorrules` (exempt dari CSRF, di luar filter
  session, idempotent — jangan sampai idle session bikin logout gagal dengan error 403).

## 4. Modul & Skema Data

Semua tabel referensi/master pakai `$useSoftDeletes = true` kecuali dinyatakan lain (§6 Model rule).

### 4.1 `wilayah`
`id, nama, ketua_nama, ketua_kontak_cipher, ketua_kontak_hash, created_at, updated_at, deleted_at`
- `ketua_kontak_*` = **PII** (nomor telepon ketua wilayah) → wajib cipher + HMAC hash per §6.
- Relasi: satu Wilayah punya banyak `lingkungan`.

### 4.2 `lingkungan`
`id, wilayah_id (FK), nama, ketua_nama, ketua_kontak_cipher (nullable), ketua_kontak_hash (nullable), created_at, updated_at, deleted_at`

### 4.3 `dewan_paroki_bidang` (4 Bidang DPH)
`id, kode (enum: liturgi|diakonia|koinonia|kerygma), nama_tampilan, deskripsi, icon, urutan`
- Tabel referensi tetap (4 baris) — **tidak perlu paginasi** (§6 Performance, pengecualian tabel kecil).

### 4.4 `sakramen_jenis`
`id, kode (enum: baptis|komuni_pertama|krisma|tobat|perkawinan|pengurapan_orang_sakit|misdinar|konsultasi_psikologi|konsultasi_hukum|administrasi), nama, deskripsi, icon, urutan, is_active`
- Tabel referensi kecil — tidak perlu paginasi.

### 4.5 `pendaftaran` (submission dari Formulir & Dokumen — **berisi PII**)
`id, nama_lengkap, whatsapp_cipher, whatsapp_hash, sakramen_jenis_id (FK, nullable), pesan (nullable), status (enum: baru|diproses|selesai|ditolak), created_at, updated_at`
- `nama_lengkap` boleh plaintext (§6: "Names/PIC labels may remain plaintext").
- `whatsapp_*` **wajib** cipher + HMAC hash.
- **List/index admin dilarang menampilkan nomor WA mentah** — hanya nama, jenis layanan, status,
  tanggal. WA hanya boleh didekripsi di halaman **detail** (authorized reveal), sesuai §6.

### 4.6 `berita` (Berita & Kegiatan)
`id, judul, slug (unique), kategori (enum: pengumuman|kegiatan_paroki|pelayanan_sosial|kegiatan_wilayah|liturgi), ringkasan, konten, gambar_utama, status (enum: draft|terbit), tanggal_terbit (nullable), created_at, updated_at, deleted_at`
- Bisa tumbuh tanpa batas → **wajib paginasi** (§6 Performance).
- Halaman publik hanya menampilkan baris `status = terbit`.
- `gambar_utama` disimpan relatif terhadap `public/` (mis. `uploads/berita/{random}`) — bukan path absolut.

### 4.7 `artikel` (Katekese & Renungan — satu tabel untuk 4 kategori, bukan 4 tabel terpisah)
`id, judul, slug (unique), kategori (enum: artikel_iman|renungan_harian|orang_kudus|mutiara_biblika), konten, status (enum: draft|terbit), tanggal_terbit, created_at, updated_at, deleted_at`
- Wajib paginasi per kategori.
- Halaman publik hanya menampilkan baris `status = terbit`.
- URL detail: `/katekese/{kategori}/{slug}` — segment `kategori` **harus cocok** dengan kolom `kategori` di DB (validasi di `ArtikelService::findPublishedByKategoriAndSlug`).

### 4.8 `dokumen` (Dokumen & Materi Unduhan)
`id, nama, file_path, kategori, created_at, updated_at, deleted_at`
- `file_path` disimpan **di luar `public/`** (§4.5) dan disajikan lewat route unduhan terkontrol —
  jangan expose path asli ke client (§6 Security: no raw resource URL).

### 4.9 `jadwal_misa`
`id, jenis (enum: harian|mingguan|jumat_pertama|khusus), hari_label, jam, catatan (nullable), urutan, is_active`
- Tabel referensi kecil — tidak perlu paginasi.

### 4.10 `galeri`
`id, file_path, caption (nullable), urutan, created_at`

### 4.11 `hero_slide` (dulunya array JS statis `heroSlides` di prototipe — sekarang dikelola admin)
`id, eyebrow, judul, subjudul, cta1_label, cta1_href, cta2_label, cta2_href, gambar, urutan, is_active`
- `judul` boleh menyimpan `\n` literal untuk 2 baris, ditampilkan dengan `white-space:pre-line`
  seperti di prototipe — jangan diubah jadi 2 kolom terpisah, cukup satu kolom teks.

## 5. Rute (garis besar)

**Publik**
- `GET /` — Beranda (hero + seluruh section, sesuai `paroki-landing.html`); data dari `HomeService::getLandingData()`
- `GET /berita` — arsip berita terbit (paginasi 12/halaman); filter opsional `?kategori={kategori}` (query string)
- `GET /berita/{slug}` — detail berita terbit
- `GET /katekese` — arsip semua artikel terbit
- `GET /katekese/{kategori}` — arsip per kategori (`artikel_iman|renungan_harian|orang_kudus|mutiara_biblika`)
- `GET /katekese/{kategori}/{slug}` — detail artikel terbit
- `GET /dokumen/{id}/unduh` — download terkontrol, bukan path publik langsung
- `POST /formulir` — simpan ke `pendaftaran`; respons HTMX partial (§4.4), bukan redirect penuh

Controller publik: `Home`, `BeritaController`, `KatekeseController`, `FormulirController`, `DokumenController`.
Urutan route `katekese`: detail (`/{kategori}/{slug}`) didaftarkan **sebelum** arsip per kategori (`/{kategori}`).

**Admin** (prefix `/admin`, di belakang Shield)
- `/admin/wilayah`, `/admin/wilayah/{id}/lingkungan`
- `/admin/dewan-paroki`
- `/admin/sakramen-jenis`
- `/admin/pendaftaran` (list tanpa WA mentah), `/admin/pendaftaran/{id}` (detail + reveal WA)
- `/admin/berita`, `/admin/artikel`, `/admin/dokumen`, `/admin/jadwal-misa`, `/admin/galeri`,
  `/admin/hero-slide` (CRUD masing-masing)

## 6. Frontend

- Struktur View:
  - `app/Views/layouts/main.php` — layout **beranda** one-page (Alpine `landingPage()`, semua section)
  - `app/Views/layouts/public.php` — layout **arsip & detail** (berita, katekese): header ringkas + footer
  - `app/Views/layouts/admin.php` — layout admin
  - `app/Views/partials/*` — section beranda (hero, profil, jadwal, sakramen, berita, katekese, formulir, kontak, footer)
  - `app/Views/berita/*`, `app/Views/katekese/*` — halaman arsip/detail konten
  - `app/Views/partials/public_footer.php`, `public_pagination.php` — shared untuk layout publik
- Ganti seluruh array JS statis di prototipe (`heroSlides`, `bidangDPH`, `wilayahList`,
  `sakramenList`, `beritaList`, `katekeseList`, `dokumenList`) dengan data dari Controller,
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
   unduhan dokumen ✅; galeri publik (section beranda) ✅
7. Pendaftaran (PII, enkripsi, admin reveal, status workflow) ✅
8. Beranda (`HomeService` + partials) ✅; halaman arsip/detail berita & katekese ✅; HTMX formulir ✅
9. Outstanding: selaraskan partial beranda dengan `paroki-landing.html` (palet/SVG); upload orchestration
   HeroSlide/Galeri/Dokumen ke Service (sisa audit §8); build Tailwind produksi (Vite); testing menyeluruh +
   checklist §8 `.cursorrules` + persiapan deployment
