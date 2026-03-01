<?php
$arriving_query = new WP_Query([
    'post_type'      => 'prodotto',
    'post_status'    => 'publish',
    'posts_per_page' => 4,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'tax_query'      => [
        [
            'taxonomy' => 'stato_prodotto',
            'field'    => 'slug',
            'terms'    => ['in-arrivo', 'in_arrivo'],
            'operator' => 'IN',
        ],
    ],
]);
?>

<?php if ($arriving_query->have_posts()) : ?>
    <section class="ev-home" aria-label="Prodotti in arrivo homepage">
        <section class="ev-products ev-products--arriving">
            <div class="ev-products__head">
                <h2>Prodotti in arrivo</h2>
                <p>Scopri in anteprima i prossimi arrivi del catalogo.</p>
            </div>

            <div class="ev-products__grid">
                <?php while ($arriving_query->have_posts()) : ?>
                    <?php $arriving_query->the_post(); ?>
                    <?php get_template_part('template-parts/prodotto/card'); ?>
                <?php endwhile; ?>
            </div>
        </section>
    </section>
<?php endif; ?>

<?php wp_reset_postdata(); ?>
