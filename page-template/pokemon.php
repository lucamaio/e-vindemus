<?php
/**
 * Template name: Pagina Pokemon
 * Template Post Type: page
 */

get_header(); ?>

<main>
    <section class="ev-home" aria-label="Tutti i prodotti">
    <div class="ev-home__container">
          <section class="ev-products">
            <div class="ev-products__head">
                <h2>Pokemon</h2>
                <p>Esplora la nostra sezione dedicata al mondo pokemon.</p>
            </div>
            <div class ="ev-products__grid">
                <?php get_template_part('template-parts/pokemon/card-list', 'home'); ?>
            </div>
          </section>
    </div>
</main>

<?php get_footer();
