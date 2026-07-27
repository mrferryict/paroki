<section id="formulir" class="bg-ivory py-20 lg:py-28">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <div class="grid gap-12 lg:grid-cols-2 lg:gap-16">
            <div>
                <span class="inline-block h-1 w-12 bg-gold"></span>
                <h2 class="mt-4 font-display text-3xl font-semibold text-maroon lg:text-4xl">Formulir & Dokumen</h2>
                <p class="mt-4 text-stone-600">
                    Ajukan pendaftaran sakramen atau layanan paroki. Tim paroki akan menghubungi Anda melalui WhatsApp.
                </p>

                <div class="mt-10">
                    <h3 class="font-display text-xl font-semibold text-maroon">Dokumen & Materi Unduhan</h3>
                    <ul class="mt-4 space-y-2">
                        <template x-if="dokumenList.length === 0">
                            <li class="rounded-lg border border-gold/20 bg-white px-4 py-3 text-sm text-stone-500">
                                Belum ada dokumen tersedia.
                            </li>
                        </template>
                        <template x-for="doc in dokumenList" :key="doc.id">
                            <li>
                                <a :href="doc.downloadUrl"
                                   class="flex items-center gap-3 rounded-lg border border-gold/20 bg-white px-4 py-3 text-sm transition hover:border-maroon/30 hover:bg-maroon/5">
                                    <span class="text-maroon" x-html="iconSvg('download')"></span>
                                    <span class="flex-1">
                                        <span class="font-medium text-stone-800" x-text="doc.nama"></span>
                                        <span x-show="doc.kategori" class="ml-2 text-xs text-stone-500" x-text="'(' + doc.kategori + ')'"></span>
                                    </span>
                                </a>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <div class="rounded-2xl border border-gold/20 bg-white p-6 shadow-sm lg:p-8">
                <h3 class="font-display text-xl font-semibold text-maroon">Formulir Pendaftaran</h3>
                <div id="formulir-response" class="mt-4"></div>
                <form class="mt-6 space-y-4"
                      hx-post="<?= site_url('formulir') ?>"
                      hx-target="#formulir-response"
                      hx-swap="innerHTML">
                    <?= csrf_field() ?>
                    <div>
                        <label for="nama_lengkap" class="mb-1 block text-sm font-medium text-stone-700">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" required
                               class="w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm focus:border-maroon focus:outline-none focus:ring-1 focus:ring-maroon">
                    </div>
                    <div>
                        <label for="whatsapp" class="mb-1 block text-sm font-medium text-stone-700">Nomor WhatsApp</label>
                        <input type="tel" name="whatsapp" id="whatsapp" required
                               placeholder="08xxxxxxxxxx"
                               class="w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm focus:border-maroon focus:outline-none focus:ring-1 focus:ring-maroon">
                    </div>
                    <div>
                        <label for="sakramen_jenis_id" class="mb-1 block text-sm font-medium text-stone-700">Jenis Layanan (opsional)</label>
                        <select name="sakramen_jenis_id" id="sakramen_jenis_id"
                                class="w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm focus:border-maroon focus:outline-none focus:ring-1 focus:ring-maroon">
                            <option value="">— Pilih layanan —</option>
                            <?php foreach ($sakramenFormOptions as $option): ?>
                                <option value="<?= esc((string) $option['id']) ?>"><?= esc($option['nama']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div>
                        <label for="pesan" class="mb-1 block text-sm font-medium text-stone-700">Pesan (opsional)</label>
                        <textarea name="pesan" id="pesan" rows="4"
                                  class="w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm focus:border-maroon focus:outline-none focus:ring-1 focus:ring-maroon"></textarea>
                    </div>
                    <button type="submit"
                            class="w-full rounded-lg bg-maroon px-4 py-3 text-sm font-semibold text-ivory transition hover:bg-maroon/90">
                        Kirim Pendaftaran
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
