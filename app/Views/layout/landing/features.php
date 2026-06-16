        <section class="service-section" id="layanan">
            <div class="section-heading">
                <div>
                    <span class="section-eyebrow dark">Layanan SiPASTI</span>
                    <h2>Pantauan awal yang lebih mudah dipahami keluarga.</h2>
                </div>
            </div>

            <div class="service-grid">
                <article class="service-card">
                    <span class="service-icon service-icon-chat" aria-hidden="true"></span>
                    <b>01</b>
                    <h3>Konseling Awal</h3>
                    <p>Isi data anak dan gejala yang terlihat untuk mendapatkan gambaran risiko awal.</p>
                </article>
                <article class="service-card">
                    <span class="service-icon service-icon-chart" aria-hidden="true"></span>
                    <b>02</b>
                    <h3>Analisis Sistem Pakar</h3>
                    <p>Sistem membantu membaca pola gejala dengan metode yang sudah disiapkan admin.</p>
                </article>
                <article class="service-card">
                    <span class="service-icon service-icon-note" aria-hidden="true"></span>
                    <b>03</b>
                    <h3>Riwayat Diagnosa</h3>
                    <p>Data hasil konsultasi dapat dipantau kembali sebagai bahan evaluasi lanjutan.</p>
                </article>
                <article class="service-card">
                    <span class="service-icon service-icon-heart" aria-hidden="true"></span>
                    <b>04</b>
                    <h3>Arahan Tindak Lanjut</h3>
                    <p>Hasil awal membantu keluarga lebih siap saat berkonsultasi dengan tenaga kesehatan.</p>
                </article>
            </div>
        </section>

        <section class="process-section blog-section" id="blog" aria-label="Artikel SiPASTI">
            <div class="blog-heading">
                <h2>Artikel dan informasi stunting.</h2>
            </div>

            <?php $articleList = array_values($articles ?? []); ?>
            <?php $featuredArticle = $articleList[0] ?? null; ?>
            <?php if ($featuredArticle): ?>
            <div class="blog-layout">
                <article class="blog-featured">
                    <img src="<?= base_url($featuredArticle['image']) ?>" alt="<?= esc($featuredArticle['title'], 'attr') ?>">
                    <div>
                        <span><?= esc($featuredArticle['category']) ?></span>
                        <h3><?= esc($featuredArticle['title']) ?></h3>
                        <p><?= esc($featuredArticle['excerpt']) ?></p>
                        <a href="<?= base_url('/artikel/' . array_key_first($articles)) ?>">Read more</a>
                    </div>
                </article>
            <?php endif; ?>

            <div class="blog-grid">
                <?php foreach (array_slice($articles ?? [], 1, null, true) as $slug => $article): ?>
                    <article>
                    <img src="<?= base_url($article['image']) ?>" alt="<?= esc($article['title'], 'attr') ?>">
                    <div>
                        <span><?= esc($article['category']) ?></span>
                        <h3><?= esc($article['title']) ?></h3>
                        <p><?= esc($article['excerpt']) ?></p>
                        <a href="<?= base_url('/artikel/' . $slug) ?>">Read more</a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            </div>
        </section>

        <section class="stats-section cta-section" id="cta" aria-label="Ajakan konsultasi SiPASTI">
            <div class="cta-copy">
                <span class="section-eyebrow">Mulai Konsultasi</span>
                <h2>Cek kondisi anak dan dapatkan saran awal lebih cepat.</h2>
                <p>Isi data anak sesuai catatan posyandu atau buku KIA. Hasil konsultasi membantu orang tua menentukan langkah pemantauan berikutnya.</p>
            </div>
            <div class="cta-panel" aria-label="Ringkasan konsultasi awal">
                <div class="cta-points">
                    <article>
                        <strong>3 menit</strong>
                        <span>Isi data dasar anak</span>
                    </article>
                    <article>
                        <strong>Riwayat</strong>
                        <span>Hasil tersimpan rapi</span>
                    </article>
                    <article>
                        <strong>Arahan</strong>
                        <span>Siap untuk tindak lanjut</span>
                    </article>
                </div>
                <div class="cta-actions">
                    <a class="btn-light-landing" href="<?= base_url('/konsultasi') ?>">Mulai Konsultasi</a>
                    <a class="btn-outline-light-landing" href="<?= base_url('/#contact') ?>">Hubungi Puskesmas</a>
                </div>
            </div>
        </section>
