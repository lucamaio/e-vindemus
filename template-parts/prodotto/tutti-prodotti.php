<?php
$products_query = new WP_Query([
    'post_type'      => 'prodotto',
    'post_status'    => 'publish',
    'posts_per_page' => 8,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

$all_products_url = function_exists('ev_get_all_products_page_url')
    ? ev_get_all_products_page_url()
    : home_url('/tutti-prodotti/');
?>

<section class="ev-home" aria-label="Tutti i prodotti">
    <div class="ev-home__container">
        <section class="ev-products ev-products--all">
            <div class="ev-products__head">
                <h2>Tutti i prodotti</h2>
                <p>Esplora le ultime novita del catalogo: qui trovi gli 8 prodotti piu recenti.</p>
            </div>

            <div class="ev-products__grid">
                <?php if ($products_query->have_posts()) : ?>
                    <?php while ($products_query->have_posts()) : ?>
                        <?php $products_query->the_post(); ?>
                        <?php get_template_part('template-parts/prodotto/card'); ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p>Nessun prodotto trovato.</p>
                <?php endif; ?>
            </div>

            <?php if ((int) $products_query->found_posts > 8) : ?>
                <div class="ev-products__actions">
                    <a class="ev-btn ev-btn--small ev-products__explore-btn ev-products__explore-btn--compact" href="<?php echo esc_url($all_products_url); ?>">
                        Esplora tutti i prodotti
                    </a>
                </div>
            <?php endif; ?>
        </section>
    </div>
</section>
