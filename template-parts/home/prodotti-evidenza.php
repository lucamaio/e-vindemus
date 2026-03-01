<?php
$featured_ids = function_exists('ev_get_homepage_featured_product_ids')
    ? ev_get_homepage_featured_product_ids()
    : [];

if (empty($featured_ids)) {
    return;
}

$featured_query = new WP_Query([
    'post_type'      => 'prodotto',
    'post_status'    => 'publish',
    'post__in'       => $featured_ids,
    'orderby'        => 'post__in',
    'posts_per_page' => count($featured_ids),
]);
?>

<?php if ($featured_query->have_posts()) : ?>
    <section class="ev-home" aria-label="Prodotti in evidenza homepage">
        <section class="ev-products ev-products--featured">
            <div class="ev-products__head">
                <h2>Prodotti in evidenza</h2>
                <p>Prodotti marcati con posizione "Homepage", attivi per data e disponibili.</p>
            </div>

            <div class="ev-products__grid">
                <?php while ($featured_query->have_posts()) : ?>
                    <?php $featured_query->the_post(); ?>
                    <?php get_template_part('template-parts/prodotto/card'); ?>
                <?php endwhile; ?>
            </div>
        </section>
    </section>
<?php endif; ?>

<?php wp_reset_postdata(); ?>
