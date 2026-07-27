<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Wilayah\WilayahWithLingkunganDto;
use App\Entities\Artikel;
use App\Entities\Berita;
use App\Entities\DewanParokiBidang;
use App\Entities\Dokumen;
use App\Entities\HeroSlide;
use App\Entities\JadwalMisa;
use App\Entities\Lingkungan;
use App\Entities\SakramenJenis;
use App\Enums\ArtikelKategori;
use App\Enums\BeritaKategori;
use CodeIgniter\I18n\Time;

class HomeService
{
    public function __construct(
        private readonly HeroSlideService $heroSlideService,
        private readonly DewanParokiBidangService $dewanParokiBidangService,
        private readonly WilayahService $wilayahService,
        private readonly JadwalMisaService $jadwalMisaService,
        private readonly SakramenJenisService $sakramenJenisService,
        private readonly BeritaService $beritaService,
        private readonly ArtikelService $artikelService,
        private readonly DokumenService $dokumenService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getLandingData(): array
    {
        $jadwalList = $this->jadwalMisaService->findAllActiveOrdered();

        return [
            'title'              => 'Paroki Hati Kudus Yesus',
            'heroSlides'         => $this->mapHeroSlides($this->heroSlideService->findAllActiveOrdered()),
            'bidangDPH'          => $this->mapBidangDph($this->dewanParokiBidangService->findAllOrdered()),
            'wilayahList'        => $this->mapWilayahList($this->wilayahService->findAllWithLingkunganForPublic()),
            'jadwalList'         => $this->mapJadwalList($jadwalList),
            'sakramenList'       => $this->mapSakramenList($this->sakramenJenisService->findAllActiveOrdered()),
            'beritaList'         => $this->mapBeritaList($this->beritaService->findLatestPublished(limit: 6)),
            'katekeseList'       => $this->mapKatekeseList($this->artikelService->findLatestPublished(kategori: null, limit: 12)),
            'dokumenList'        => $this->mapDokumenList($this->dokumenService->findAllOrdered()),
            'sakramenFormOptions'=> $this->mapSakramenFormOptions($this->sakramenJenisService->findAllActiveOrdered()),
            'katekeseKategori'   => $this->mapKatekeseKategori(),
            'jenisJadwalLabels'  => $this->jadwalMisaService->jenisOptions(),
        ];
    }

    /**
     * @param list<HeroSlide> $slides
     *
     * @return list<array<string, mixed>>
     */
    private function mapHeroSlides(array $slides): array
    {
        return array_map(static fn (HeroSlide $slide): array => [
            'eyebrow'    => (string) ($slide->eyebrow ?? ''),
            'judul'      => (string) ($slide->judul ?? ''),
            'subjudul'   => (string) ($slide->subjudul ?? ''),
            'cta1Label'  => (string) ($slide->cta1_label ?? ''),
            'cta1Href'   => (string) ($slide->cta1_href ?? '#'),
            'cta2Label'  => (string) ($slide->cta2_label ?? ''),
            'cta2Href'   => (string) ($slide->cta2_href ?? '#'),
            'gambar'     => base_url((string) ($slide->gambar ?? '')),
        ], $slides);
    }

    /**
     * @param list<DewanParokiBidang> $bidang
     *
     * @return list<array<string, mixed>>
     */
    private function mapBidangDph(array $bidang): array
    {
        return array_map(static fn (DewanParokiBidang $item): array => [
            'kode'      => (string) ($item->kode ?? ''),
            'nama'      => (string) ($item->nama_tampilan ?? ''),
            'deskripsi' => (string) ($item->deskripsi ?? ''),
            'icon'      => (string) ($item->icon ?? ''),
        ], $bidang);
    }

    /**
     * @param list<WilayahWithLingkunganDto> $wilayahRows
     *
     * @return list<array<string, mixed>>
     */
    private function mapWilayahList(array $wilayahRows): array
    {
        return array_map(static function (WilayahWithLingkunganDto $row): array {
            return [
                'id'         => (int) $row->wilayah->id,
                'nama'       => (string) $row->wilayah->nama,
                'ketuaNama'  => (string) ($row->wilayah->ketua_nama ?? ''),
                'lingkungan' => array_map(static fn (Lingkungan $lingkungan): array => [
                    'id'        => (int) $lingkungan->id,
                    'nama'      => (string) $lingkungan->nama,
                    'ketuaNama' => (string) ($lingkungan->ketua_nama ?? ''),
                ], $row->lingkungan),
            ];
        }, $wilayahRows);
    }

    /**
     * @param list<JadwalMisa> $jadwal
     *
     * @return list<array<string, mixed>>
     */
    private function mapJadwalList(array $jadwal): array
    {
        return array_map(fn (JadwalMisa $item): array => [
            'id'        => (int) $item->id,
            'jenis'     => (string) ($item->jenis ?? ''),
            'jenisLabel'=> $this->jadwalMisaService->jenisOptions()[(string) ($item->jenis ?? '')] ?? (string) ($item->jenis ?? ''),
            'hariLabel' => (string) ($item->hari_label ?? ''),
            'jam'       => $this->formatJamDisplay((string) ($item->jam ?? '')),
            'catatan'   => (string) ($item->catatan ?? ''),
        ], $jadwal);
    }

    /**
     * @param list<SakramenJenis> $items
     *
     * @return list<array<string, mixed>>
     */
    private function mapSakramenList(array $items): array
    {
        return array_map(static fn (SakramenJenis $item): array => [
            'id'        => (int) $item->id,
            'kode'      => (string) ($item->kode ?? ''),
            'nama'      => (string) ($item->nama ?? ''),
            'deskripsi' => (string) ($item->deskripsi ?? ''),
            'icon'      => (string) ($item->icon ?? ''),
        ], $items);
    }

    /**
     * @param list<SakramenJenis> $items
     *
     * @return list<array<string, mixed>>
     */
    private function mapSakramenFormOptions(array $items): array
    {
        return array_map(static fn (SakramenJenis $item): array => [
            'id'   => (int) $item->id,
            'nama' => (string) ($item->nama ?? ''),
        ], $items);
    }

    /**
     * @param list<Berita> $items
     *
     * @return list<array<string, mixed>>
     */
    private function mapBeritaList(array $items): array
    {
        return array_map(function (Berita $item): array {
            $kategori = BeritaKategori::tryFromString((string) ($item->kategori ?? ''));

            return [
                'id'            => (int) $item->id,
                'judul'         => (string) ($item->judul ?? ''),
                'slug'          => (string) ($item->slug ?? ''),
                'kategori'      => (string) ($item->kategori ?? ''),
                'kategoriLabel' => $kategori?->label() ?? (string) ($item->kategori ?? ''),
                'ringkasan'     => (string) ($item->ringkasan ?? ''),
                'gambar'        => $this->resolvePublicImage((string) ($item->gambar_utama ?? '')),
                'tanggalTerbit' => $this->formatDate((string) ($item->tanggal_terbit ?? '')),
                'href'          => site_url('berita/' . ($item->slug ?? '')),
            ];
        }, $items);
    }

    /**
     * @param list<Artikel> $items
     *
     * @return list<array<string, mixed>>
     */
    private function mapKatekeseList(array $items): array
    {
        return array_map(function (Artikel $item): array {
            $kategori = ArtikelKategori::tryFromString((string) ($item->kategori ?? ''));
            $excerpt  = strip_tags((string) ($item->konten ?? ''));

            if (mb_strlen($excerpt) > 140) {
                $excerpt = mb_substr($excerpt, 0, 137) . '…';
            }

            return [
                'id'            => (int) $item->id,
                'judul'         => (string) ($item->judul ?? ''),
                'slug'          => (string) ($item->slug ?? ''),
                'kategori'      => (string) ($item->kategori ?? ''),
                'kategoriLabel' => $kategori?->label() ?? (string) ($item->kategori ?? ''),
                'ringkasan'     => $excerpt,
                'tanggalTerbit' => $this->formatDate((string) ($item->tanggal_terbit ?? '')),
                'href'          => site_url('katekese/' . ($item->kategori ?? '') . '/' . ($item->slug ?? '')),
            ];
        }, $items);
    }

    /**
     * @param list<Dokumen> $items
     *
     * @return list<array<string, mixed>>
     */
    private function mapDokumenList(array $items): array
    {
        return array_map(fn (Dokumen $item): array => [
            'id'          => (int) $item->id,
            'nama'        => (string) ($item->nama ?? ''),
            'kategori'    => (string) ($item->kategori ?? ''),
            'downloadUrl' => $this->dokumenService->publicDownloadUrl((int) $item->id),
        ], $items);
    }

    /**
     * @return list<array<string, string>>
     */
    private function mapKatekeseKategori(): array
    {
        $result = [];

        foreach (ArtikelKategori::cases() as $case) {
            $result[] = [
                'value' => $case->value,
                'label' => $case->label(),
            ];
        }

        return $result;
    }

    private function formatJamDisplay(string $jam): string
    {
        $jam = trim($jam);

        if ($jam === '') {
            return '';
        }

        return substr($jam, 0, 5) . ' WIB';
    }

    private function formatDate(string $raw): string
    {
        if ($raw === '') {
            return '';
        }

        return Time::parse($raw, null, 'id_ID')->toLocalizedString('d MMM yyyy');
    }

    private function resolvePublicImage(string $relativePath): string
    {
        if ($relativePath === '') {
            return '';
        }

        return base_url($relativePath);
    }
}
