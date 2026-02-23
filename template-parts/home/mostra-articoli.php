  
<?php
    /* Questa sezione è un esempio statico per mostrare come potrebbero essere strutturate le sezioni "Novità", "Best seller" e "Offerte del momento". 
    In un progetto reale, questi dati verrebbero probabilmente recuperati dinamicamente da un database o da un'API. 
    */
    $home_sections = [
        'novita' => [
            'title' => 'Nuovi arrivi',
            'subtitle' => 'I prodotti più recenti selezionati dal nostro team.',
            'items' => [
                ['name' => 'Sneaker Urban Pulse', 'price' => '€89,90', 'tag' => 'Nuovo'],
                ['name' => 'Zaino Smart Travel', 'price' => '€64,90', 'tag' => 'Eco'],
                ['name' => 'Lampada Minimal Glow', 'price' => '€39,90', 'tag' => 'Casa'],
                ['name' => 'Felpa Essential Fit', 'price' => '€49,90', 'tag' => 'Trend'],
            ],
        ],
        'best-seller' => [
            'title' => 'Best seller',
            'subtitle' => 'I preferiti dei clienti, testati e approvati.',
            'items' => [
                ['name' => 'Auricolari Wave Pro', 'price' => '€79,90', 'tag' => 'Top'],
                ['name' => 'Smartwatch Active One', 'price' => '€129,90', 'tag' => 'Fitness'],
                ['name' => 'Sedia Ergo Comfort', 'price' => '€159,90', 'tag' => 'Ufficio'],
                ['name' => 'Diffusore Aroma Zen', 'price' => '€29,90', 'tag' => 'Relax'],
            ],
        ],
        'offerte' => [
            'title' => 'Offerte del momento',
            'subtitle' => 'Occasioni limitate per acquistare al miglior prezzo.',
            'items' => [
                ['name' => 'Set skincare Glow Kit', 'price' => '€24,90', 'tag' => '-30%'],
                ['name' => 'Giacca Rainproof Lite', 'price' => '€69,90', 'tag' => '-20%'],
                ['name' => 'Mouse Wireless Silent', 'price' => '€19,90', 'tag' => '-35%'],
                ['name' => 'Speaker Mini Boom', 'price' => '€34,90', 'tag' => '-25%'],
            ],
        ],
    ];
    ?>

    <?php foreach ($home_sections as $section_id => $section_data) : ?>
        <section id="<?php echo esc_attr($section_id); ?>" class="ev-products">
            <div class="ev-products__head">
                <h2><?php echo esc_html($section_data['title']); ?></h2>
                <p><?php echo esc_html($section_data['subtitle']); ?></p>
            </div>

            <div class="ev-products__grid">
                <?php foreach ($section_data['items'] as $item) : ?>
                    <article class="ev-product-card">
                        <span class="ev-product-card__tag"><?php echo esc_html($item['tag']); ?></span>
                        <div class="ev-product-card__thumb" aria-hidden="true"></div>
                        <h3><?php echo esc_html($item['name']); ?></h3>
                        <p class="ev-product-card__price"><?php echo esc_html($item['price']); ?></p>
                        <button type="button" class="ev-btn ev-btn--small">Aggiungi al carrello</button>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>