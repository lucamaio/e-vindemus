<?php
global $the_post;
// Mi ricavo tutti i prodotti pokemon per mostrarli nella sezione "Tutti i pokemon" della home page

$args = array(
    'post_type' => 'prodotto',
    'posts_per_page' => -1, // Mostra tutti i pokemon
    'orderby' => 'date', // Ordina per data di pubblicazione
    'order' => 'DESC', // Ordine decrescente
);

$query = new WP_Query($args);

$post = $query->post; // Imposta il post globale per i template part

var_dump($query->posts); // Debug: mostra i prodotti recuperati

?>