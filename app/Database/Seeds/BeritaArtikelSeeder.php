<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class BeritaArtikelSeeder extends Seeder
{
    public function run(): void
    {
        $now = Time::now()->toDateTimeString();

        if ($this->db->table('berita')->countAllResults() === 0) {
            $this->db->table('berita')->insertBatch([
                [
                    'judul'          => 'Jadwal Misa Natal Paroki 2026',
                    'slug'           => 'jadwal-misa-natal-paroki-2026',
                    'kategori'       => 'pengumuman',
                    'ringkasan'      => 'Informasi lengkap jadwal misa Natal di Paroki Hati Kudus Yesus, termasuk misa malam dan misa pagi.',
                    'konten'         => "Umat yang terkasih,\n\nBerikut jadwal misa Natal Paroki Hati Kudus Yesus:\n\n• Misa Malam: 24 Desember pukul 19.00 WIB\n• Misa Pagi: 25 Desember pukul 08.00 WIB\n• Misa Siang: 25 Desember pukul 10.00 WIB\n\nMohon datang 15 menit lebih awal. Persembahan Natal dapat disampaikan melalui petugas liturgi.",
                    'gambar_utama'   => null,
                    'status'         => 'terbit',
                    'tanggal_terbit' => $now,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
                [
                    'judul'          => 'Bakti Sosial ke Panti Asuhan St. Yusup',
                    'slug'           => 'bakti-sosial-panti-asuhan-st-yusup',
                    'kategori'       => 'pelayanan_sosial',
                    'ringkasan'      => 'Tim diakonia paroki mengunjungi panti asuhan dan menyerahkan bantuan sembako serta kebutuhan pendidikan.',
                    'konten'         => "Pada hari Sabtu lalu, tim Bidang Diakonia Paroki Hati Kudus Yesus melaksanakan kunjungan bakti sosial ke Panti Asuhan St. Yusup.\n\nKegiatan dihadiri oleh 35 relawan dari berbagai lingkungan. Bantuan yang diserahkan meliputi sembako, alat tulis, dan perlengkapan sekolah.\n\nTerima kasih atas partisipasi umat. Informasi kegiatan diakonia berikutnya akan diumumkan melalui pengumuman paroki.",
                    'gambar_utama'   => null,
                    'status'         => 'terbit',
                    'tanggal_terbit' => Time::now()->subDays(3)->toDateTimeString(),
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
                [
                    'judul'          => 'Retreat Keluarga Wilayah II',
                    'slug'           => 'retreat-keluarga-wilayah-ii',
                    'kategori'       => 'kegiatan_wilayah',
                    'ringkasan'      => 'Wilayah II mengadakan retreat keluarga dengan tema “Rumah Tangga sebagai Gereja Domestik”.',
                    'konten'         => "Retreat Keluarga Wilayah II dilaksanakan pada akhir pekan lalu dengan tema “Rumah Tangga sebagai Gereja Domestik”.\n\nKegiatan dipimpin oleh Romo paroki dan diikuti oleh 28 keluarga. Materi retreat membahas doa bersama keluarga, pendidikan iman anak, dan tanggung jawab sosial umat.\n\nWilayah II mengucapkan terima kasih kepada seluruh relawan dan peserta.",
                    'gambar_utama'   => null,
                    'status'         => 'terbit',
                    'tanggal_terbit' => Time::now()->subDays(7)->toDateTimeString(),
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
            ]);
        }

        if ($this->db->table('artikel')->countAllResults() === 0) {
            $this->db->table('artikel')->insertBatch([
                [
                    'judul'          => 'Iman: Anugerah dan Respons Umat',
                    'slug'           => 'iman-anugerah-dan-respons-umat',
                    'kategori'       => 'artikel_iman',
                    'konten'         => "Iman bukan sekadar setuju pada proposisi, melainkan respons total manusia kepada Allah yang berbelas kasih.\n\nDalam Katekismus Gereja Katolik, iman dipahami sebagai anugerah Allah yang memanggil kita masuk ke dalam persekutuan-Nya. Umat dipanggil untuk hidup iman lewat doa, sakramen, dan pelayanan.\n\nMarilah kita memperdalam iman dengan belajar sabda Tuhan dan berbagi kesaksian kepada sesama.",
                    'status'         => 'terbit',
                    'tanggal_terbit' => $now,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
                [
                    'judul'          => 'Renungan: Tuhan Memberi Kekuatan di Setiap Hari',
                    'slug'           => 'renungan-tuhan-memberi-kekuatan',
                    'kategori'       => 'renungan_harian',
                    'konten'         => "“Kekuatan-Ku cukup bagimu, sebab Kekuatan-Ku justru sempurna dalam kelemahan.” (2 Kor 12:9)\n\nKadang kita merasa lelah menghadapi tantangan hidup. Renungan hari ini mengajak kita untuk menyerahkan kelemahan kita kepada Tuhan.\n\nDalam doa hening, mintalah Roh Kudus memberi kekuatan baru. Tuhan tidak pernah meninggalkan umat-Nya.",
                    'status'         => 'terbit',
                    'tanggal_terbit' => Time::now()->subDays(1)->toDateTimeString(),
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
                [
                    'judul'          => 'Santa Teresa Avila: Doa adalah Persahabatan',
                    'slug'           => 'santa-teresa-avila-doa-adalah-persahabatan',
                    'kategori'       => 'orang_kudus',
                    'konten'         => "Santa Teresa dari Avila (1515–1582) adalah reformator Ordo Karmel dan Doktor Gereja.\n\nIa terkenal dengan ungkapan: “Doa bukanlah pikiran yang banyak, melainkan cinta yang banyak.” Bagi Santa Teresa, doa adalah percakapan hati dengan Allah yang mengasihi.\n\nMari meneladani semangat doa Santa Teresa dengan meluangkan waktu hening setiap hari, meski hanya beberapa menit.",
                    'status'         => 'terbit',
                    'tanggal_terbit' => Time::now()->subDays(4)->toDateTimeString(),
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
                [
                    'judul'          => 'Mutiara: Kasih Menutupi Segala-galanya',
                    'slug'           => 'mutiara-kasih-menutupi-segala-galanya',
                    'kategori'       => 'mutiara_biblika',
                    'konten'         => "“Di atas semuanya itu: pakailah kasih, sebab kasih menutupi segala-galanya.” (Kol 3:14)\n\nKasih adalah ikat pinggang yang menyatukan seluruh kebajikan Kristiani. Tanpa kasih, pelayanan kita kehilangan makna.\n\nRenungkan hari ini: bagaimana kita dapat menunjukkan kasih konkret kepada keluarga, tetangga, dan mereka yang membutuhkan?",
                    'status'         => 'terbit',
                    'tanggal_terbit' => Time::now()->subDays(2)->toDateTimeString(),
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
            ]);
        }
    }
}
