<?php
global $the_post;
// Mi ricavo tutti i prodotti per mostrarli nella sezione "Tutti i prodotti" della home page

$args = array(
    'post_type' => 'prodotto',
    'posts_per_page' => -1, // Mostra tutti i prodotti
    'orderby' => 'date', // Ordina per data di pubblicazione
    'order' => 'DESC', // Ordine decrescente
);

$query = new WP_Query($args);

$post = $query->post; // Imposta il post globale per i template part

// var_dump($query->posts); // Debug: mostra i prodotti recuperati

if ($query->have_posts()) {?>
        <?php foreach ($query->posts as $post) {
            setup_postdata($post); // Imposta i dati del post globale per ogni prodotto
            get_template_part('template-parts/prodotto/card'); // Carica il template part per mostrare la scheda del prodotto
        }
        wp_reset_postdata(); // Resetta i dati del post globale dopo il loop ?>
    
<?php } else {
    echo '<p>Nessun prodotto trovato.</p>';
}

wp_reset_query(); // Resetta la query globale dopo aver eseguito la query personalizzata
?>