<?php
/**
 * Template part per mostrare i prodotti Pokemon in evidenza
 */

    global $pokemon_featured_query;
?>

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