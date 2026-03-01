<?php
/**
 * Template Name: Pagina Offerte
 * Template Post Type: page
 */

get_header();

$offerte_ids = function_exists('ev_get_featured_product_ids_by_position')
    ? ev_get_featured_product_ids_by_position('offerte', 'Offerte', -1)
    : [];

$offerte_query = null;
if (!empty($offerte_ids)) {
    $offerte_query = new WP_Query([
        'post_type'      => 'prodotto',
        'post_status'    => 'publish',
        'post__in'       => $offerte_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => count($offerte_ids),
    ]);
}
?>

<main>
    <section class="ev-home" aria-label="Pagina Offerte">
        <section class="ev-hero__image-wrap">
            <img
                class="ev-hero__image"
                src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/img-carousel1.png'); ?>"
                alt="Offerte in evidenza" />
            <div class="ev-hero__overlay" aria-hidden="true"></div>
            <div class="ev-hero__content">
                <span class="ev-badge">Sezione Offerte</span>
                <h1>Scopri le offerte del momento</h1>
                <p>Prodotti in promozione evidenziati nella posizione Offerte.</p>
            </div>
        </section>

        <section class="ev-products ev-products--featured" aria-label="Prodotti in offerta">
            <div class="ev-products__head">
                <h2>Prodotti in offerta</h2>
                <p>Elenco completo dei prodotti con posizione evidenziata Offerte.</p>
            </div>

            <div class="ev-products__grid">
                <?php if ($offerte_query instanceof WP_Query && $offerte_query->have_posts()) : ?>
                    <?php while ($offerte_query->have_posts()) : ?>
                        <?php $offerte_query->the_post(); ?>
                        <?php get_template_part('template-parts/prodotto/card'); ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p>Nessun prodotto in offerta disponibile al momento.</p>
                <?php endif; ?>
            </div>
        </section>
    </section>
</main>

<?php get_footer(); ?>
