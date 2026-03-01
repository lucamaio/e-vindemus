<?php get_header(); ?>

<?php
$filters = function_exists('ev_get_search_product_filters') ? ev_get_search_product_filters() : [
    'tipo' => '',
    'prezzo_min' => '',
    'prezzo_max' => '',
];

$tipo_label = '';
if (!empty($filters['tipo'])) {
    $tipo_term = get_term_by('slug', $filters['tipo'], 'categoria_prodotto');
    if ($tipo_term && !is_wp_error($tipo_term)) {
        $tipo_label = $tipo_term->name;
    }
}
?>

<main>
    <section class="ev-home" aria-label="Risultati ricerca prodotti">
        <section class="ev-products">
            <div class="ev-products__head">
                <h1>Risultati ricerca</h1>
                <p>
                    <?php
                    printf(
                        'Trovati %d prodotti per "%s".',
                        (int) $wp_query->found_posts,
                        esc_html(get_search_query())
                    );
                    ?>
                </p>
                <?php if ($tipo_label || $filters['prezzo_min'] !== '' || $filters['prezzo_max'] !== '') : ?>
                    <p>
                        Filtri attivi:
                        <?php if ($tipo_label) : ?>
                            <?php echo esc_html('Tipologia: ' . $tipo_label); ?>
                        <?php endif; ?>
                        <?php if ($filters['prezzo_min'] !== '') : ?>
                            <?php echo esc_html(' Prezzo min: € ' . number_format((float) $filters['prezzo_min'], 2, ',', '.')); ?>
                        <?php endif; ?>
                        <?php if ($filters['prezzo_max'] !== '') : ?>
                            <?php echo esc_html(' Prezzo max: € ' . number_format((float) $filters['prezzo_max'], 2, ',', '.')); ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="ev-products__grid">
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/prodotto/card'); ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p>Nessun prodotto trovato con i filtri selezionati.</p>
                <?php endif; ?>
            </div>

            <?php if ((int) $wp_query->max_num_pages > 1) : ?>
                <nav class="ev-search-pagination" aria-label="Paginazione risultati">
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
