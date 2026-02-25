<?php 
global $post;

if (!isset($post) || !$post instanceof WP_Post) {
    return;
}

$post_id = $post->ID;
$prefix  = '_dci_prodotto_';

// Titolo
$titolo = get_the_title($post_id);

// Descrizione breve (corretto ordine parametri)
$descrizione = get_post_meta($post_id, $prefix . 'descrizione_breve', true);

// Prezzo
$prezzo = get_post_meta($post_id, $prefix . 'prezzo', true);

// Formattazione prezzo
$prezzo_format = '';
if (!empty($prezzo)) {
    $prezzo_format = '€ ' . number_format((float)$prezzo, 2, ',', '.');
}

// Recupero URL immagine salvato da CMB2
$immagine = get_post_meta($post_id, $prefix . 'immagine_evidenza', true);

// Fallback se non presente
if (empty($immagine) || !filter_var($immagine, FILTER_VALIDATE_URL)) {
    $immagine = get_template_directory_uri() . '/assets/img/placeholder.jpg';
}
?>

<a href="<?php echo get_permalink($post_id) ?>" class="text-decoration-none" aria-label="<?php echo esc_attr($titolo); ?>">

<div class="ev-product-card">
    
    <span class="ev-product-card__tag">Nuovo</span>

    <div class="ev-product-card__thumb" 
         aria-hidden="true" 
         style="background-image: url('<?php echo esc_url($immagine); ?>');">
    </div>

    <h3><?php echo esc_html($titolo); ?></h3>

    <?php if (!empty($prezzo_format)) : ?>
        <p class="ev-product-card__price">
            <?php echo esc_html($prezzo_format); ?>
        </p>
    <?php endif; ?>

    <button type="button" class="ev-btn ev-btn--small">
        Aggiungi al carrello
    </button>

</div>
</a>