<?php get_header(); ?>

<main>
    
  <?php
    // var_dump(get_stylesheet_uri());
    get_template_part('template-parts/home/carousel', 'home');
    get_template_part('template-parts/home/prodotti-evidenza');
    get_template_part('template-parts/home/prodotti-in-arrivo');
    // get_template_part('template-parts/home/prodotti-offerta');
    get_template_part('template-parts/prodotto/tutti-prodotti');
  ?>
</main>

<?php get_footer(); ?>
