<div id="pendaftaran-layanan" class="mt-16 rounded-2xl border border-gold/20 bg-white p-6 shadow-sm lg:p-8">
    <span class="inline-block h-1 w-12 bg-gold"></span>
    <h3 class="mt-4 font-display text-2xl font-semibold text-maroon">Formulir Pendaftaran Layanan</h3>
    <p class="mt-3 text-sm text-stone-600">
        Ajukan pendaftaran sakramen atau layanan paroki. Tim paroki akan menghubungi Anda melalui WhatsApp.
    </p>

    <div id="pendaftaran-layanan-response" class="mt-4"></div>

    <form class="mt-6 space-y-4"
          hx-post="<?= site_url('formulir') ?>"
          hx-target="#pendaftaran-layanan-response"
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
