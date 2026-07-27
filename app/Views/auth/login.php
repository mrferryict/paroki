<?= $this->extend('auth/layout') ?>

<?= $this->section('title') ?>Login Admin<?= $this->endSection() ?>

<?= $this->section('main') ?>
<div class="w-full max-w-md">
    <div class="mb-8 text-center">
        <p class="font-display text-3xl font-semibold text-maroon">Paroki Hati Kudus Yesus</p>
        <p class="mt-2 text-sm text-stone-600">Masuk ke panel admin</p>
    </div>

    <div class="rounded-2xl border border-gold/20 bg-white p-6 shadow-sm sm:p-8">
        <?php if (session('error') !== null): ?>
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <?= esc(session('error')) ?>
            </div>
        <?php elseif (session('errors') !== null): ?>
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <?php if (is_array(session('errors'))): ?>
                    <?php foreach (session('errors') as $error): ?>
                        <?= esc($error) ?><br>
                    <?php endforeach ?>
                <?php else: ?>
                    <?= esc(session('errors')) ?>
                <?php endif ?>
            </div>
        <?php endif ?>

        <?php if (session('message') !== null): ?>
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="alert">
                <?= esc(session('message')) ?>
            </div>
        <?php endif ?>

        <form action="<?= url_to('login') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-stone-700">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       inputmode="email"
                       autocomplete="email"
                       value="<?= esc(old('email', ''), 'attr') ?>"
                       required
                       class="w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm focus:border-maroon focus:outline-none focus:ring-1 focus:ring-maroon">
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-stone-700">Kata Sandi</label>
                <input type="password"
                       id="password"
                       name="password"
                       autocomplete="current-password"
                       required
                       class="w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm focus:border-maroon focus:outline-none focus:ring-1 focus:ring-maroon">
            </div>

            <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                <label class="flex items-center gap-2 text-sm text-stone-600">
                    <input type="checkbox"
                           name="remember"
                           value="1"
                           class="rounded border-stone-300 text-maroon focus:ring-maroon"
                           <?php if (old('remember')): ?>checked<?php endif ?>>
                    Ingat saya
                </label>
            <?php endif ?>

            <button type="submit"
                    class="w-full rounded-lg bg-maroon px-4 py-3 text-sm font-semibold text-ivory transition hover:bg-maroon/90">
                Masuk
            </button>
        </form>

        <?php if (setting('Auth.allowMagicLinkLogins')): ?>
            <p class="mt-4 text-center text-sm text-stone-600">
                Lupa kata sandi?
                <a href="<?= url_to('magic-link') ?>" class="font-medium text-maroon hover:text-gold">Gunakan magic link</a>
            </p>
        <?php endif ?>
    </div>

    <p class="mt-6 text-center text-sm text-stone-500">
        <a href="<?= site_url('/') ?>" class="hover:text-maroon">&larr; Kembali ke situs publik</a>
    </p>
</div>
<?= $this->endSection() ?>
