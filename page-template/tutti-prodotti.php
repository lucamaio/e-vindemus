<?php
/**
 * Template Name: Tutti i prodotti
 * Template Post Type: page
 */

get_header();

$paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));

$products_query = new WP_Query([
    'post_type'      => 'prodotto',
    'post_status'    => 'publish',
    'posts_per_page' => 12,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'paged'          => $paged,
]);
?>

<main>
    <section class="ev-home" aria-label="Catalogo completo prodotti">
        <section class="ev-products ev-products--all">
            <div class="ev-products__head">
                <h1>Tutti i prodotti</h1>
                <p>Catalogo completo del negozio.</p>
            </div>

            <div class="ev-products__grid">
                <?php if ($products_query->have_posts()) : ?>
                    <?php while ($products_query->have_posts()) : ?>
                        <?php $products_query->the_post(); ?>
                        <?php get_template_part('template-parts/prodotto/card'); ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p>Nessun prodotto disponibile al momento.</p>
                <?php endif; ?>
            </div>

            <?php if ((int) $products_query->max_num_pages > 1) : ?>
                <nav class="ev-search-pagination" aria-label="Paginazione prodotti">
                    <?php
                    echo wp_kses_post(paginate_links([
                        'total'     => (int) $products_query->max_num_pages,
                        'current'   => $paged,
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

<?php
wp_reset_postdata();
get_footer();
?>
