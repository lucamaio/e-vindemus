<?php
/**
 * Template Name: Pagina Pokemon
 * Template Post Type: page
 */

get_header();

$pokemon_term_ids = function_exists('ev_get_term_ids_with_children')
    ? ev_get_term_ids_with_children('categoria_prodotto', 'pokemon', 'Pokemon')
    : [];

$pokemon_term = get_term_by('slug', 'pokemon', 'categoria_prodotto');
if ((!$pokemon_term || is_wp_error($pokemon_term))) {
    $pokemon_term = get_term_by('name', 'Pokemon', 'categoria_prodotto');
}

$pokemon_subcategories = [];
if ($pokemon_term instanceof WP_Term) {
    $pokemon_subcategories = get_terms([
        'taxonomy'   => 'categoria_prodotto',
        'parent'     => (int) $pokemon_term->term_id,
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    if (is_wp_error($pokemon_subcategories)) {
        $pokemon_subcategories = [];
    }
}

$pokemon_featured_ids = function_exists('ev_get_featured_product_ids_by_position')
    ? ev_get_featured_product_ids_by_position('pokemon', 'Pokemon', 8)
    : [];

$pokemon_featured_query = null;
if (!empty($pokemon_featured_ids)) {
    $pokemon_featured_query = new WP_Query([
        'post_type'      => 'prodotto',
        'post_status'    => 'publish',
        'post__in'       => $pokemon_featured_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => count($pokemon_featured_ids),
    ]);
}

$pokemon_all_args = [
    'post_type'      => 'prodotto',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
];

if (!empty($pokemon_term_ids)) {
    $pokemon_all_args['tax_query'] = [
        [
            'taxonomy' => 'categoria_prodotto',
            'field'    => 'term_id',
            'terms'    => $pokemon_term_ids,
            'operator' => 'IN',
        ],
    ];
}

$pokemon_all_query = new WP_Query($pokemon_all_args);
?>

<main>
    <section class="ev-home" aria-label="Pagina Pokemon">
        <section class="ev-hero__image-wrap">
            <img
                class="ev-hero__image"
                src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/img-carousel1.png'); ?>"
                alt="Collezione prodotti Pokemon" />
            <div class="ev-hero__overlay" aria-hidden="true"></div>
            <div class="ev-hero__content">
                <span class="ev-badge">Collezione Pokemon</span>
                <h1>Scopri la nostra area Pokemon</h1>
                <p>Carte, box, accessori e prodotti da collezione selezionati per appassionati e giocatori.</p>
            </div>
        </section>

        <?php if (!empty($pokemon_subcategories)) : ?>
            <section class="ev-products ev-products--subcats" aria-label="Sottocategorie Pokemon">
                <div class="ev-products__head">
                    <h2>Sottocategorie Pokemon</h2>
                    <p>Vai direttamente alla sottocategoria che ti interessa.</p>
                </div>

                <div class="ev-subcategory-nav">
                    <?php foreach ($pokemon_subcategories as $subcategory) : ?>
                        <?php if (!$subcategory instanceof WP_Term) { continue; } ?>
                        <?php $subcategory_link = get_term_link($subcategory); ?>
                        <?php if (is_wp_error($subcategory_link)) { continue; } ?>
                        <a class="ev-subcategory-nav__item" href="<?php echo esc_url($subcategory_link); ?>">
                            <?php echo esc_html($subcategory->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="ev-products ev-products--featured" aria-label="Pokemon in evidenza">
            <div class="ev-products__head">
                <h2>Prodotti in evidenza Pokemon</h2>
                <p>Prodotti con posizione evidenziata Pokemon attiva e disponibili.</p>
            </div>

            <div class="ev-products__grid">
                <?php if ($pokemon_featured_query instanceof WP_Query && $pokemon_featured_query->have_posts()) : ?>
                    <?php while ($pokemon_featured_query->have_posts()) : ?>
                        <?php $pokemon_featured_query->the_post(); ?>
                        <?php get_template_part('template-parts/prodotto/card'); ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p>Nessun prodotto Pokemon in evidenza disponibile.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="ev-products ev-products--all" aria-label="Tutti i prodotti Pokemon">
            <div class="ev-products__head">
                <h2>Tutti i prodotti Pokemon</h2>
                <p><?php echo esc_html(sprintf('Totale prodotti: %d', (int) $pokemon_all_query->found_posts)); ?></p>
            </div>

            <div class="ev-products__grid">
                <?php if ($pokemon_all_query->have_posts()) : ?>
                    <?php while ($pokemon_all_query->have_posts()) : ?>
                        <?php $pokemon_all_query->the_post(); ?>
                        <?php get_template_part('template-parts/prodotto/card'); ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p>Nessun prodotto Pokemon disponibile.</p>
                <?php endif; ?>
            </div>
        </section>
    </section>
</main>

<?php get_footer(); ?>
