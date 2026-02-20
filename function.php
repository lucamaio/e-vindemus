<?php

// Carica lo stile e gli script del tema
function mio_tema_scripts() {

    // Stile principale del tema
    wp_enqueue_style('style', get_stylesheet_uri());

    // CSS Bootstrap
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', [], '5.3.2');

    // JS Bootstrap
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', ['jquery'], '5.3.2', true);
}
add_action('wp_enqueue_scripts', 'mio_tema_scripts');