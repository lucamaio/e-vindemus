<?php 
global $post;

if (!isset($post) || !$post instanceof WP_Post) {
    return;
}

$post_id = $post->ID;
$prefix  = '_dci_prodotto_';

$titolo      = get_the_title($post_id);
$descrizione = get_post_meta($post_id, $prefix . 'descrizione_breve', true);
$prezzo      = get_post_meta($post_id, $prefix . 'prezzo', true);

$prezzo_format = '';
if ($prezzo !== '') {
    $prezzo_normalized = str_replace(',', '.', (string) $prezzo);
    if (is_numeric($prezzo_normalized)) {
        $prezzo_format = '€ ' . number_format((float) $prezzo_normalized, 2, ',', '.');
    }
}

$tipologia = get_post_meta($post_id, $prefix . 'tipo_prodotto', true);
if (empty($tipologia)) {
    $taxonomies = get_object_taxonomies('prodotto', 'names');

    if (!empty($taxonomies)) {
        foreach ($taxonomies as $taxonomy) {
            $terms = get_the_terms($post_id, $taxonomy);

            if (!is_wp_error($terms) && !empty($terms)) {
                $tipologia = $terms[0]->name;
                break;
            }
        }
    }
}

$tipologia = !empty($tipologia) ? $tipologia : 'N/D';

$immagine = get_post_meta($post_id, $prefix . 'immagine_evidenza', true);
if (empty($immagine) || !filter_var($immagine, FILTER_VALIDATE_URL)) {
    $immagine = get_template_directory_uri() . '/assets/img/placeholder.jpg';
}
?>

<a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="ev-product-card-link" aria-label="<?php echo esc_attr($titolo); ?>">
    <article class="ev-product-card">
        <div class="ev-product-card__thumb-wrap">
            <div class="ev-product-card__thumb"
                aria-hidden="true"
                style="background-image: url('<?php echo esc_url($immagine); ?>');">
            </div>
            <span class="ev-product-card__tag"><?php echo esc_html($tipologia); ?></span>
        </div>

        <div class="ev-product-card__body">
            <p class="ev-product-card__type">
                <span>Tipologia prodotto:</span>
                <?php echo esc_html($tipologia); ?>
            </p>

            <h3><?php echo esc_html($titolo); ?></h3>

            <?php if (!empty($descrizione)) : ?>
                <p class="ev-product-card__description"><?php echo esc_html($descrizione); ?></p>
            <?php endif; ?>

            <div class="ev-product-card__footer">
                <?php if (!empty($prezzo_format)) : ?>
                    <p class="ev-product-card__price"><?php echo esc_html($prezzo_format); ?></p>
                <?php endif; ?>

                <button type="button" class="ev-btn ev-btn--small">
                    Aggiungi al carrello
                </button>
            </div>
        </div>
    </article>
</a>
