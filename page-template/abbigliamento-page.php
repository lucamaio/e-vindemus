<?php
/**
 * Template Name: Pagina Abbigliamento
 * Template Post Type: page
 */

get_header(); ?>

<main>
    <section class="ev-home" aria-label="Pagina Abbigliamento">
        <div class="ev-home__container">
            <section class="ev-products">
                <div class="ev-products__head">
                    <h2>Abbigliamento</h2>
                    <p>Esplora la nostra selezione di abbigliamento.</p>
                </div>
                <div class="ev-products__grid">
                    <?php get_template_part('template-parts/prodotto/card-list', 'home'); ?>
                </div>
            </section>
        </div>
    </section>
</main>

<?php get_footer();
