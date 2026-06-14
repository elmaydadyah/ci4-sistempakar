        <section class="article-detail">
            <a class="article-back-link" href="<?= base_url('/#layanan') ?>">Kembali ke beranda</a>

            <header class="article-detail-head">
                <span><?= esc($article['category']) ?></span>
                <h1><?= esc($article['title']) ?></h1>
                <p><?= esc($article['excerpt']) ?></p>
                <time datetime="2026-06-05"><?= esc($article['date']) ?></time>
            </header>

            <img class="article-hero-image" src="<?= base_url($article['image']) ?>" alt="<?= esc($article['title'], 'attr') ?>">

            <div class="article-body">
                <?php foreach ($article['body'] as $paragraph): ?>
                    <p><?= esc($paragraph) ?></p>
                <?php endforeach; ?>
            </div>

            <div class="article-next">
                <h2>Artikel lainnya</h2>
                <div class="article-next-grid">
                    <?php foreach ($articles as $slug => $item): ?>
                        <?php if ($item['title'] === $article['title']) {
                            continue;
                        } ?>
                        <a href="<?= base_url('/artikel/' . $slug) ?>">
                            <span><?= esc($item['category']) ?></span>
                            <strong><?= esc($item['title']) ?></strong>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
