<?php
/**
 * Template per il singolo prodotto.
 *
 * @package E-vindemus
 */

get_header();
?>

<main class="ev-single-product" role="main">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <?php
            $prefix = '_dci_prodotto_';

            $prodotto_id = get_the_ID();
            $titolo     = get_the_title($prodotto_id);

            $featured_image = get_post_meta($prodotto_id, $prefix . 'immagine_evidenza', true);
            $gallery_images = get_post_meta($prodotto_id, $prefix . 'galleria_immagini', true);

            $image_set = [];

            if (!empty($featured_image)) {
                $image_set[] = $featured_image;
            }

            if (!empty($gallery_images) && is_array($gallery_images)) {
                foreach ($gallery_images as $gallery_image) {
                    if (!empty($gallery_image)) {
                        $image_set[] = $gallery_image;
                    }
                }
            }

            $image_set = array_values(array_unique($image_set));

            $price             = get_post_meta($prodotto_id, $prefix . 'prezzo', true);
            $short_description = get_post_meta($prodotto_id, $prefix . 'descrizione_breve', true);
            $specifiche        = get_post_meta($prodotto_id, $prefix . 'specifiche_tecniche', true);
            $descrizioni       = get_post_meta($prodotto_id, $prefix . 'descrizioni', true);
            $recesso           = get_post_meta($prodotto_id, $prefix . 'recesso_garanzia', true);
            $altre_info        = get_post_meta($prodotto_id, $prefix . 'altre_informazioni', true);
            $stock             = get_post_meta($prodotto_id, $prefix . 'quantita_disponibile', true);

            $category_terms = get_the_terms($prodotto_id, 'categoria_prodotto') ?: [];
            // var_dump($category_terms); // Debug: mostra le categorie del prodotto    

            $price_formatted = null;
            if ($price !== '') {
                $price_normalized = str_replace(',', '.', (string) $price);

                if (is_numeric($price_normalized)) {
                    $price_value = (float) $price_normalized;
                    $price_formatted = rtrim(rtrim(number_format($price_value, 2, ',', '.'), '0'), ',');
                }
            }
            $stock_qty       = is_numeric($stock) ? (int) $stock : null;
            $in_stock        = null !== $stock_qty ? $stock_qty > 0 : null;
            ?>

            <article id="post-<?php echo esc_attr($prodotto_id); ?>" <?php post_class('ev-single-product__article'); ?> aria-label="Scheda Prodotto <?php echo esc_attr($titolo); ?>">
                <div class="ev-single-product__media">
                    <div class="ev-single-product__hero-image-wrap">
                        <?php if (!empty($image_set)) : ?>
                            <img
                                class="ev-single-product__hero-image"
                                src="<?php echo esc_url($image_set[0]); ?>"
                                alt="<?php echo esc_attr($titolo); ?>"
                                data-gallery-hero
                            >
                            <?php if (count($image_set) > 1) : ?>
                                <button class="ev-single-product__nav ev-single-product__nav--prev" type="button" aria-label="Immagine precedente" data-gallery-prev>
                                    <span aria-hidden="true">&#10094;</span>
                                </button>
                                <button class="ev-single-product__nav ev-single-product__nav--next" type="button" aria-label="Immagine successiva" data-gallery-next>
                                    <span aria-hidden="true">&#10095;</span>
                                </button>
                            <?php endif; ?>
                        <?php elseif (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large', ['class' => 'ev-single-product__hero-image', 'data-gallery-hero' => '']); ?>
                        <?php else : ?>
                            <div class="ev-single-product__hero-placeholder" aria-hidden="true">
                                <span>Anteprima prodotto</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($image_set)) : ?>
                        <div class="ev-single-product__gallery" aria-label="Galleria immagini prodotto">
                            <?php foreach ($image_set as $index => $image_url) : ?>
                                <figure class="ev-single-product__thumb">
                                    <button
                                        class="ev-single-product__thumb-button <?php echo 0 === $index ? 'is-active' : ''; ?>"
                                        type="button"
                                        aria-label="Mostra immagine <?php echo esc_attr((string) ($index + 1)); ?>"
                                        data-gallery-thumb
                                        data-image-src="<?php echo esc_url($image_url); ?>"
                                        data-image-index="<?php echo esc_attr((string) $index); ?>"
                                    >
                                        <img src="<?php echo esc_url($image_url); ?>" alt="Dettaglio di <?php echo esc_attr(get_the_title()); ?>">
                                    </button>
                                </figure>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="ev-single-product__content">
                    <p class="ev-single-product__eyebrow mb-2">
                        Scheda prodotto
                    </p>
                    <?php if ($category_terms && !empty($category_terms) && is_array($category_terms)) { 
                            $categorie_tot = count($category_terms);
                            foreach ($category_terms as $category) { ?>
                            <span class="ev-single-product__category mb-2" aria-label="Categoria prodotto">
                                <?php echo esc_html($category->name); ?>
                                <?php if ($categorie_tot > 1) { echo ','; } 
                                $categorie_tot--;  // diminuisco il contatore per sapere quando non mettere la virgola alla fine dell'ultimo elemento 
                                
                                ?>
                                
                            </span>
                        <?php } 
                    }else { ?>
                        <span class="ev-single-product__category mb-2" aria-label="Categoria prodotto">
                            Categoria non specificata
                        </span> 
                    <?php } ?>                    
                    <h1 class="ev-single-product__title"><?php the_title(); ?></h1>

                    <?php if (!empty($short_description)) : ?>
                        <p class="ev-single-product__lead"><?php echo esc_html($short_description); ?></p>
                    <?php endif; ?>

                    <div class="ev-single-product__purchase-box">
                        <?php if (null !== $price_formatted) : ?>
                            <p class="ev-single-product__price" aria-label="Prezzo prodotto">€ <?php echo esc_html($price_formatted); ?></p>
                        <?php endif; ?>

                        <?php if (null !== $in_stock) : ?>
                            <p class="ev-single-product__stock <?php echo $in_stock ? 'is-available' : 'is-unavailable'; ?>">
                                <?php echo $in_stock ? 'Disponibile' : 'Esaurito'; ?>
                                <?php if ($in_stock && null !== $stock_qty) : ?>
                                    <span>(<?php echo esc_html((string) $stock_qty); ?> disponibili)</span>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>

                        <div class="ev-single-product__cta">
                            <button class="ev-btn ev-btn--primary" type="button">Aggiungi al carrello</button>
                            <button class="ev-btn ev-btn--ghost-dark" type="button">Acquista ora</button>
                        </div>
                    </div>

                    <section class="ev-single-product__details">
                        <?php if (!empty($descrizioni)) : ?>
                            <div class="ev-single-product__card">
                                <h2>Descrizione</h2>
                                <div class="ev-single-product__wysiwyg"><?php echo wp_kses_post($descrizioni); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($specifiche)) : ?>
                            <div class="ev-single-product__card">
                                <h2>Specifiche tecniche</h2>
                                <div class="ev-single-product__wysiwyg"><?php echo wp_kses_post($specifiche); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($recesso)) : ?>
                            <div class="ev-single-product__card">
                                <h2>Recesso e garanzia</h2>
                                <div class="ev-single-product__wysiwyg"><?php echo wp_kses_post($recesso); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($altre_info)) : ?>
                            <div class="ev-single-product__card">
                                <h2>Altre informazioni</h2>
                                <div class="ev-single-product__wysiwyg"><?php echo wp_kses_post($altre_info); ?></div>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <section class="ev-single-product__empty">
            <h1>Prodotto non trovato</h1>
            <p>Il contenuto richiesto non è disponibile al momento.</p>
            <a class="ev-btn ev-btn--primary" href="<?php echo esc_url(home_url('/')); ?>">Torna alla home</a>
        </section>
    <?php endif; ?>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var heroImage = document.querySelector('[data-gallery-hero]');
        var thumbs = Array.prototype.slice.call(document.querySelectorAll('[data-gallery-thumb]'));
        var prevButton = document.querySelector('[data-gallery-prev]');
        var nextButton = document.querySelector('[data-gallery-next]');

        if (!heroImage || thumbs.length === 0) {
            return;
        }

        var activeIndex = 0;

        var setActiveImage = function (index) {
            if (index < 0) {
                activeIndex = thumbs.length - 1;
            } else if (index >= thumbs.length) {
                activeIndex = 0;
            } else {
                activeIndex = index;
            }

            var selectedThumb = thumbs[activeIndex];
            var selectedSrc = selectedThumb.getAttribute('data-image-src');

            if (selectedSrc) {
                heroImage.setAttribute('src', selectedSrc);
            }

            thumbs.forEach(function (thumb, thumbIndex) {
                var isActive = thumbIndex === activeIndex;
                thumb.classList.toggle('is-active', isActive);
                thumb.setAttribute('aria-current', isActive ? 'true' : 'false');
            });
        };

        thumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                var imageIndex = parseInt(thumb.getAttribute('data-image-index'), 10);

                if (!isNaN(imageIndex)) {
                    setActiveImage(imageIndex);
                }
            });
        });

        if (prevButton) {
            prevButton.addEventListener('click', function () {
                setActiveImage(activeIndex - 1);
            });
        }

        if (nextButton) {
            nextButton.addEventListener('click', function () {
                setActiveImage(activeIndex + 1);
            });
        }

        setActiveImage(0);
    });
</script>

<?php
get_footer();
