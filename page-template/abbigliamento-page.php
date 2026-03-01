<?php
/**
 * Template Name: Pagina Abbigliamento
 * Template Post Type: page
 */

get_header();

$abbigliamento_term_ids = function_exists('ev_get_term_ids_with_children')
    ? ev_get_term_ids_with_children('categoria_prodotto', 'abbigliamento', 'Abbigliamento')
    : [];

$abbigliamento_term = get_term_by('slug', 'abbigliamento', 'categoria_prodotto');
if ((!$abbigliamento_term || is_wp_error($abbigliamento_term))) {
    $abbigliamento_term = get_term_by('name', 'Abbigliamento', 'categoria_prodotto');
}

$abbigliamento_subcategories = [];
if ($abbigliamento_term instanceof WP_Term) {
    $abbigliamento_subcategories = get_terms([
        'taxonomy'   => 'categoria_prodotto',
        'parent'     => (int) $abbigliamento_term->term_id,
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    if (is_wp_error($abbigliamento_subcategories)) {
        $abbigliamento_subcategories = [];
    }
}

$abbigliamento_featured_ids = function_exists('ev_get_featured_product_ids_by_position')
    ? ev_get_featured_product_ids_by_position('abbigliamento', 'Abbigliamento', 8)
    : [];

$abbigliamento_featured_query = null;
if (!empty($abbigliamento_featured_ids)) {
    $abbigliamento_featured_query = new WP_Query([
        'post_type'      => 'prodotto',
        'post_status'    => 'publish',
        'post__in'       => $abbigliamento_featured_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => count($abbigliamento_featured_ids),
    ]);
}

$abbigliamento_all_args = [
    'post_type'      => 'prodotto',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
];

if (!empty($abbigliamento_term_ids)) {
    $abbigliamento_all_args['tax_query'] = [
        [
            'taxonomy' => 'categoria_prodotto',
            'field'    => 'term_id',
            'terms'    => $abbigliamento_term_ids,
            'operator' => 'IN',
        ],
    ];
}

$abbigliamento_all_query = new WP_Query($abbigliamento_all_args);
?>

<main>
    <section class="ev-home" aria-label="Pagina Abbigliamento">
        <section class="ev-hero__image-wrap">
            <img
                class="ev-hero__image"
                src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/img-carousel2.png'); ?>"
                alt="Collezione abbigliamento" />
            <div class="ev-hero__overlay" aria-hidden="true"></div>
            <div class="ev-hero__content">
                <span class="ev-badge">Sezione Abbigliamento</span>
                <h1>Scopri il nostro catalogo abbigliamento</h1>
                <p>Capi, accessori e sottocategorie selezionate per stile, comfort e qualita.</p>
            </div>
        </section>

        <?php if (!empty($abbigliamento_subcategories)) : ?>
            <section class="ev-products ev-products--subcats" aria-label="Sottocategorie Abbigliamento">
                <div class="ev-products__head">
                    <h2>Sottocategorie Abbigliamento</h2>
                    <p>Apri subito la categoria specifica che vuoi esplorare.</p>
                </div>

                <div class="ev-subcategory-nav">
                    <?php foreach ($abbigliamento_subcategories as $subcategory) : ?>
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

        <section class="ev-products ev-products--featured" aria-label="Abbigliamento in evidenza">
            <div class="ev-products__head">
                <h2>Prodotti in evidenza Abbigliamento</h2>
                <p>Prodotti con posizione evidenziata Abbigliamento attiva e disponibili.</p>
            </div>

            <div class="ev-products__grid">
                <?php if ($abbigliamento_featured_query instanceof WP_Query && $abbigliamento_featured_query->have_posts()) : ?>
                    <?php while ($abbigliamento_featured_query->have_posts()) : ?>
                        <?php $abbigliamento_featured_query->the_post(); ?>
                        <?php get_template_part('template-parts/prodotto/card'); ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p>Nessun prodotto Abbigliamento in evidenza disponibile.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="ev-products ev-products--all" aria-label="Tutti i prodotti Abbigliamento">
            <div class="ev-products__head">
                <h2>Tutti i prodotti Abbigliamento</h2>
                <p><?php echo esc_html(sprintf('Totale prodotti: %d', (int) $abbigliamento_all_query->found_posts)); ?></p>
            </div>

            <div class="ev-products__grid">
                <?php if ($abbigliamento_all_query->have_posts()) : ?>
                    <?php while ($abbigliamento_all_query->have_posts()) : ?>
                        <?php $abbigliamento_all_query->the_post(); ?>
                        <?php get_template_part('template-parts/prodotto/card'); ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p>Nessun prodotto Abbigliamento disponibile.</p>
                <?php endif; ?>
            </div>
        </section>
    </section>
</main>

<?php get_footer(); ?>
