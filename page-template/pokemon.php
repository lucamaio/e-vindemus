<?php
/**
 * Template name: Pagina Pokemon
 * Template post type: prodotto
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
            <?php //get_template_part('template-parts/home/mostra-articoli', 'home'); ?>
            <div class ="ev-products__grid">
                <?php get_template_part('template-parts/prodotto/card-list', 'home'); ?>
            </div>
          </section>
    </div>
</main>