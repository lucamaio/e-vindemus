<?php
/**
 * Archivio tassonomia categoria_prodotto.
 *
 * @package E-vindemus
 */

get_header();

$term = get_queried_object();
$term_name = ($term instanceof WP_Term) ? $term->name : 'Categoria';
$term_description = ($term instanceof WP_Term) ? term_description($term) : '';

$term_path_items = [];
if ($term instanceof WP_Term) {
    $ancestor_ids = get_ancestors((int) $term->term_id, 'categoria_prodotto', 'taxonomy');
    $ancestor_ids = array_reverse(array_map('intval', $ancestor_ids));

    foreach ($ancestor_ids as $ancestor_id) {
        $ancestor_term = get_term($ancestor_id, 'categoria_prodotto');
        if (!$ancestor_term instanceof WP_Term) {
            continue;
        }

        $ancestor_link = get_term_link($ancestor_term);
        if (is_wp_error($ancestor_link)) {
            continue;
        }

        $term_path_items[] = [
            'name' => $ancestor_term->name,
            'url'  => $ancestor_link,
            'current' => false,
        ];
    }

    $term_path_items[] = [
        'name' => $term->name,
        'url'  => '',
        'current' => true,
    ];
}
?>

<main>
    <section class="ev-home" aria-label="Archivio categoria prodotto">
        <section class="ev-hero__image-wrap">
            <img
                class="ev-hero__image"
                src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/img-carousel1.png'); ?>"
                alt="<?php echo esc_attr('Categoria ' . $term_name); ?>">
            <div class="ev-hero__overlay" aria-hidden="true"></div>
            <div class="ev-hero__content">
                <span class="ev-badge">Categoria prodotto</span>
                <h1><?php echo esc_html($term_name); ?></h1>
                <p>
                    <?php if (!empty($term_description)) : ?>
                        <?php echo wp_kses_post(wp_strip_all_tags($term_description)); ?>
                    <?php else : ?>
                        Esplora tutti i prodotti presenti in questa categoria.
                    <?php endif; ?>
                </p>
            </div>
        </section>

        <section class="ev-products ev-products--all" aria-label="<?php echo esc_attr('Prodotti categoria ' . $term_name); ?>">
            <div class="ev-products__head">
                <?php if (!empty($term_path_items)) : ?>
                    <nav class="ev-taxonomy-path ev-taxonomy-path--inline" aria-label="Percorso categoria prodotto">
                        <span class="ev-taxonomy-path__label">Percorso:</span>
                        <ol class="ev-taxonomy-path__list">
                            <?php foreach ($term_path_items as $path_item) : ?>
                                <li class="ev-taxonomy-path__item">
                                    <?php if (!empty($path_item['current'])) : ?>
                                        <span class="ev-taxonomy-path__current" aria-current="page">
                                            <?php echo esc_html((string) $path_item['name']); ?>
                                        </span>
                                    <?php else : ?>
                                        <a class="ev-taxonomy-path__link" href="<?php echo esc_url((string) $path_item['url']); ?>">
                                            <?php echo esc_html((string) $path_item['name']); ?>
                                        </a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                <?php endif; ?>
                <h2><?php echo esc_html('Prodotti: ' . $term_name); ?></h2>
                <p><?php echo esc_html(sprintf('Totale prodotti: %d', (int) $wp_query->found_posts)); ?></p>
            </div>

            <div class="ev-products__grid">
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/prodotto/card'); ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p>Nessun prodotto disponibile in questa categoria.</p>
                <?php endif; ?>
            </div>

            <?php if ((int) $wp_query->max_num_pages > 1) : ?>
                <nav class="ev-search-pagination" aria-label="Paginazione categoria">
                    <?php
                    echo wp_kses_post(paginate_links([
                        'total'     => (int) $wp_query->max_num_pages,
                        'current'   => max(1, (int) get_query_var('paged')),
                        'type'      => 'list',
                        'prev_text' => 'Precedente',
                        'next_text' => 'Successiva',
                    ]));
                    ?>
                </nav>
            <?php endif; ?>
        </section>
    </section>
</main>

<?php get_footer(); ?>
