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

$tipologia = 'N/D';
$tipologia_link = '';

$categoria_terms = get_the_terms($post_id, 'categoria_prodotto');
if (!is_wp_error($categoria_terms) && !empty($categoria_terms)) {
    $categoria_term = $categoria_terms[0];
    if ($categoria_term instanceof WP_Term) {
        $tipologia = $categoria_term->name;
        $term_link = get_term_link($categoria_term);
        if (!is_wp_error($term_link)) {
            $tipologia_link = $term_link;
        }
    }
}
$stock_qty = get_post_meta($post_id, $prefix . 'quantita_disponibile', true);
$status_info = function_exists('ev_get_product_availability_indicator')
    ? ev_get_product_availability_indicator($post_id, $stock_qty)
    : [
        'label' => 'Non disponibile',
        'slug'  => 'non-disponibile',
        'class' => 'is-not-available',
    ];

$featured_image = get_post_meta($post_id, $prefix . 'immagine_evidenza', true);
$gallery_images = get_post_meta($post_id, $prefix . 'galleria_immagini', true);

$image_set = [];
if (!empty($featured_image) && filter_var($featured_image, FILTER_VALIDATE_URL)) {
    $image_set[] = $featured_image;
}

if (!empty($gallery_images) && is_array($gallery_images)) {
    foreach ($gallery_images as $gallery_image) {
        if (!empty($gallery_image) && filter_var($gallery_image, FILTER_VALIDATE_URL)) {
            $image_set[] = $gallery_image;
        }
    }
}

$image_set = array_values(array_unique($image_set));

if (empty($image_set)) {
    $image_set[] = get_template_directory_uri() . '/assets/img/placeholder.jpg';
}
?>

<article class="ev-product-card">
    <div class="ev-product-card__thumb-wrap" data-card-gallery>
        <div class="ev-product-card__thumb-viewport">
            <?php foreach ($image_set as $index => $image_url) : ?>
                <img
                    class="ev-product-card__thumb-image <?php echo 0 === $index ? 'is-active' : ''; ?>"
                    src="<?php echo esc_url($image_url); ?>"
                    alt="<?php echo esc_attr($titolo); ?>"
                    data-card-image
                    data-image-index="<?php echo esc_attr((string) $index); ?>">
            <?php endforeach; ?>

            <?php if (count($image_set) > 1) : ?>
                <button type="button" class="ev-product-card__nav ev-product-card__nav--prev" aria-label="Immagine precedente" data-card-prev>&lsaquo;</button>
                <button type="button" class="ev-product-card__nav ev-product-card__nav--next" aria-label="Immagine successiva" data-card-next>&rsaquo;</button>
            <?php endif; ?>
        </div>

        <?php if (count($image_set) > 1) : ?>
            <div class="ev-product-card__indicators" aria-label="Indicatori immagini prodotto">
                <?php foreach ($image_set as $index => $image_url) : ?>
                    <button
                        type="button"
                        class="ev-product-card__indicator <?php echo 0 === $index ? 'is-active' : ''; ?>"
                        data-card-thumb
                        data-image-index="<?php echo esc_attr((string) $index); ?>"
                        aria-label="<?php echo esc_attr('Mostra immagine ' . ($index + 1)); ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="ev-product-card__body">
        <p class="ev-product-card__category-line" aria-label="Tipologia prodotto">
            <span class="ev-product-card__category-dot" aria-hidden="true"></span>
            <?php if (!empty($tipologia_link)) : ?>
                <a class="ev-product-card__category-text ev-product-card__category-link" href="<?php echo esc_url($tipologia_link); ?>">
                    <?php echo esc_html($tipologia); ?>
                </a>
            <?php else : ?>
                <span class="ev-product-card__category-text"><?php echo esc_html($tipologia); ?></span>
            <?php endif; ?>
        </p>

        <h3>
            <a class="ev-product-card__title-link" href="<?php echo esc_url(get_permalink($post_id)); ?>">
                <?php echo esc_html($titolo); ?>
            </a>
        </h3>

        <?php if (!empty($descrizione)) : ?>
            <p class="ev-product-card__description"><?php echo esc_html($descrizione); ?></p>
        <?php endif; ?>

        <div class="ev-product-card__footer">
            <p class="ev-product-card__stock <?php echo esc_attr($status_info['class']); ?>">
                <span class="ev-product-card__stock-dot" aria-hidden="true"></span>
                <?php echo esc_html($status_info['label']); ?>
            </p>

            <?php if (!empty($prezzo_format)) : ?>
                <p class="ev-product-card__price"><?php echo esc_html($prezzo_format); ?></p>
            <?php endif; ?>

            <a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="ev-btn ev-btn--small">
                Vedi prodotto
            </a>
        </div>
    </div>
</article>

<?php if (empty($GLOBALS['ev_card_gallery_script_printed'])) : ?>
    <?php $GLOBALS['ev_card_gallery_script_printed'] = true; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var setActiveImage = function (galleryEl, index) {
                var images = Array.prototype.slice.call(galleryEl.querySelectorAll('[data-card-image]'));
                var thumbs = Array.prototype.slice.call(galleryEl.querySelectorAll('[data-card-thumb]'));

                if (images.length === 0) {
                    return;
                }

                var nextIndex = index;
                if (nextIndex < 0) {
                    nextIndex = images.length - 1;
                }
                if (nextIndex >= images.length) {
                    nextIndex = 0;
                }

                galleryEl.setAttribute('data-active-index', String(nextIndex));

                images.forEach(function (imageEl, imageIndex) {
                    imageEl.classList.toggle('is-active', imageIndex === nextIndex);
                });

                thumbs.forEach(function (thumbEl, thumbIndex) {
                    thumbEl.classList.toggle('is-active', thumbIndex === nextIndex);
                });
            };

            document.querySelectorAll('[data-card-gallery]').forEach(function (galleryEl) {
                setActiveImage(galleryEl, 0);
            });

            document.addEventListener('click', function (event) {
                var thumbBtn = event.target.closest('[data-card-thumb]');
                if (thumbBtn) {
                    var thumbGallery = thumbBtn.closest('[data-card-gallery]');
                    var thumbIndex = parseInt(thumbBtn.getAttribute('data-image-index') || '0', 10);
                    setActiveImage(thumbGallery, thumbIndex);
                    return;
                }

                var prevBtn = event.target.closest('[data-card-prev]');
                if (prevBtn) {
                    var prevGallery = prevBtn.closest('[data-card-gallery]');
                    var currentPrev = parseInt(prevGallery.getAttribute('data-active-index') || '0', 10);
                    setActiveImage(prevGallery, currentPrev - 1);
                    return;
                }

                var nextBtn = event.target.closest('[data-card-next]');
                if (nextBtn) {
                    var nextGallery = nextBtn.closest('[data-card-gallery]');
                    var currentNext = parseInt(nextGallery.getAttribute('data-active-index') || '0', 10);
                    setActiveImage(nextGallery, currentNext + 1);
                }
            });
        });
    </script>
<?php endif; ?>
