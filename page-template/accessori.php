<?php
/**
 * Template Name: Pagina Accessori
 * Template Post Type: page
 */

get_header();

$accessori_term_ids = function_exists('ev_get_term_ids_with_children')
    ? ev_get_term_ids_with_children('categoria_prodotto', 'accessori', 'Accessori')
    : [];

$accessori_args = [
    'post_type'      => 'prodotto',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
];

if (!empty($accessori_term_ids)) {
    $accessori_args['tax_query'] = [
        [
            'taxonomy' => 'categoria_prodotto',
            'field'    => 'term_id',
            'terms'    => $accessori_term_ids,
            'operator' => 'IN',
        ],
    ];
}

$accessori_query = new WP_Query($accessori_args);
?>

<main>
    <section class="ev-home" aria-label="Pagina Accessori">
        <section class="ev-hero__image-wrap">
            <img
                class="ev-hero__image"
                src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/img-carousel2.png'); ?>"
                alt="Collezione accessori" />
            <div class="ev-hero__overlay" aria-hidden="true"></div>
            <div class="ev-hero__content">
                <span class="ev-badge">Sezione Accessori</span>
                <h1>Scopri i nostri accessori</h1>
                <p>Selezione completa di accessori e sottocategorie collegate.</p>
            </div>
        </section>

        <section class="ev-products ev-products--all" aria-label="Tutti i prodotti Accessori">
            <div class="ev-products__head">
                <h2>Tutti i prodotti Accessori</h2>
                <p><?php echo esc_html(sprintf('Totale prodotti: %d', (int) $accessori_query->found_posts)); ?></p>
            </div>

            <div class="ev-products__grid">
                <?php if ($accessori_query->have_posts()) : ?>
                    <?php while ($accessori_query->have_posts()) : ?>
                        <?php $accessori_query->the_post(); ?>
                        <?php get_template_part('template-parts/prodotto/card'); ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p>Nessun prodotto Accessori disponibile.</p>
                <?php endif; ?>
            </div>
        </section>
    </section>
</main>

<?php get_footer(); ?>
