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
            $is_abbigliamento_subcategory = false;
            $is_scarpe_category           = false;

            if (!empty($category_terms) && is_array($category_terms)) {
                foreach ($category_terms as $category_term) {
                    if (!$category_term instanceof WP_Term) {
                        continue;
                    }

                    $category_slug = sanitize_title((string) $category_term->slug);
                    $category_name = sanitize_title((string) $category_term->name);

                    if (false !== strpos($category_slug, 'scarp') || false !== strpos($category_name, 'scarp')) {
                        $is_scarpe_category = true;
                    }

                    $ancestor_ids = get_ancestors((int) $category_term->term_id, 'categoria_prodotto', 'taxonomy');
                    if (empty($ancestor_ids)) {
                        continue;
                    }

                    foreach ($ancestor_ids as $ancestor_id) {
                        $ancestor_term = get_term((int) $ancestor_id, 'categoria_prodotto');

                        if (!$ancestor_term instanceof WP_Term) {
                            continue;
                        }

                        $ancestor_slug = sanitize_title((string) $ancestor_term->slug);
                        $ancestor_name = sanitize_title((string) $ancestor_term->name);

                        if ('abbigliamento' === $ancestor_slug || 'abbigliamento' === $ancestor_name) {
                            $is_abbigliamento_subcategory = true;
                            break 2;
                        }
                    }
                }
            }

            $variant_attributes = [];
            $selectable_variant_keys = [];
            if ($is_abbigliamento_subcategory) {
                $color_terms         = get_the_terms($prodotto_id, 'colore') ?: [];
                $size_terms          = get_the_terms($prodotto_id, 'taglia') ?: [];
                $shoe_size_terms     = get_the_terms($prodotto_id, 'taglia_scarpe') ?: [];
                $material_terms      = get_the_terms($prodotto_id, 'materiale') ?: [];

                $term_names = static function (array $terms): array {
                    $names = [];

                    foreach ($terms as $term) {
                        if ($term instanceof WP_Term) {
                            $names[] = $term->name;
                        }
                    }

                    return array_values(array_unique($names));
                };

                $colors = $term_names($color_terms);
                if (!empty($colors)) {
                    $variant_attributes[] = [
                        'key' => 'colore',
                        'label' => 'Colori',
                        'values' => $colors,
                    ];
                    $selectable_variant_keys[] = 'colore';
                }

                $sizes      = $term_names($size_terms);
                $shoe_sizes = $term_names($shoe_size_terms);

                if ($is_scarpe_category) {
                    if (!empty($shoe_sizes)) {
                        $variant_attributes[] = [
                            'key' => 'taglia_scarpe',
                            'label' => 'Taglie scarpe',
                            'values' => $shoe_sizes,
                        ];
                        $selectable_variant_keys[] = 'taglia_scarpe';
                    }
                } elseif (!empty($sizes)) {
                    $variant_attributes[] = [
                        'key' => 'taglia',
                        'label' => 'Taglie',
                        'values' => $sizes,
                    ];
                    $selectable_variant_keys[] = 'taglia';
                }

                $materials = $term_names($material_terms);
                if (!empty($materials)) {
                    $variant_attributes[] = [
                        'key' => 'materiale',
                        'label' => 'Materiali',
                        'values' => $materials,
                    ];
                }
            }

            $price_formatted = null;
            if ($price !== '') {
                $price_normalized = str_replace(',', '.', (string) $price);

                if (is_numeric($price_normalized)) {
                    $price_value = (float) $price_normalized;
                    $price_formatted = rtrim(rtrim(number_format($price_value, 2, ',', '.'), '0'), ',');
                }
            }
            $stock_qty       = is_numeric($stock) ? (int) $stock : null;
            $status_info = function_exists('ev_get_product_availability_indicator')
                ? ev_get_product_availability_indicator($prodotto_id, null !== $stock_qty ? $stock_qty : '')
                : [
                    'label' => 'Non disponibile',
                    'slug'  => 'non-disponibile',
                    'class' => 'is-not-available',
                ];
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
                    <?php if ($category_terms && !empty($category_terms) && is_array($category_terms)) : ?>
                        <div class="ev-single-product__categories" aria-label="Tipologie prodotto">
                            <?php foreach ($category_terms as $category) : ?>
                                <?php
                                if (!$category instanceof WP_Term) {
                                    continue;
                                }
                                $category_link = get_term_link($category);
                                ?>
                                <?php if (!is_wp_error($category_link)) : ?>
                                    <a class="ev-single-product__category mb-2" href="<?php echo esc_url($category_link); ?>" aria-label="Categoria prodotto <?php echo esc_attr($category->name); ?>">
                                        <?php echo esc_html($category->name); ?>
                                    </a>
                                <?php else : ?>
                                    <span class="ev-single-product__category mb-2" aria-label="Categoria prodotto">
                                        <?php echo esc_html($category->name); ?>
                                    </span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <span class="ev-single-product__category mb-2" aria-label="Categoria prodotto">
                            Categoria non specificata
                        </span>
                    <?php endif; ?>
                    <h1 class="ev-single-product__title"><?php the_title(); ?></h1>

                    <?php if (!empty($short_description)) : ?>
                        <p class="ev-single-product__lead"><?php echo esc_html($short_description); ?></p>
                    <?php endif; ?>

                    <form class="ev-single-product__purchase-box ev-single-product__purchase-form" method="post">
                        <?php if (null !== $price_formatted) : ?>
                            <p class="ev-single-product__price" aria-label="Prezzo prodotto">€ <?php echo esc_html($price_formatted); ?></p>
                        <?php endif; ?>

                        <p class="ev-single-product__stock <?php echo esc_attr($status_info['class']); ?>">
                            <?php echo esc_html($status_info['label']); ?>
                                <?php if ('is-available' === $status_info['class'] && null !== $stock_qty) : ?>
                                    <span>(<?php echo esc_html((string) $stock_qty); ?> disponibili)</span>
                                <?php endif; ?>
                        </p>

                        <?php if (isset($_GET['ev_added_to_cart']) && '1' === (string) $_GET['ev_added_to_cart']) : ?>
                            <p class="ev-single-product__cart-success">
                                Prodotto aggiunto al carrello con le varianti selezionate.
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($variant_attributes)) : ?>
                            <dl class="ev-single-product__variants" aria-label="Varianti disponibili del prodotto">
                                <?php foreach ($variant_attributes as $attribute) : ?>
                                    <div class="ev-single-product__variant-row">
                                        <dt class="ev-single-product__variant-label"><?php echo esc_html($attribute['label']); ?>:</dt>
                                        <dd class="ev-single-product__variant-values">
                                            <?php if (!empty($attribute['key']) && in_array($attribute['key'], $selectable_variant_keys, true)) : ?>
                                                <div class="ev-single-product__variant-options" role="radiogroup" aria-label="<?php echo esc_attr($attribute['label']); ?>">
                                                    <?php foreach ($attribute['values'] as $value_index => $value) : ?>
                                                        <?php
                                                        $variant_input_id = sprintf(
                                                            'ev-variant-%d-%s-%d',
                                                            (int) $prodotto_id,
                                                            sanitize_key((string) $attribute['key']),
                                                            (int) $value_index
                                                        );
                                                        ?>
                                                        <label class="ev-single-product__variant-option" for="<?php echo esc_attr($variant_input_id); ?>">
                                                            <input
                                                                id="<?php echo esc_attr($variant_input_id); ?>"
                                                                class="ev-single-product__variant-radio"
                                                                type="radio"
                                                                name="ev_varianti[<?php echo esc_attr((string) $attribute['key']); ?>]"
                                                                value="<?php echo esc_attr((string) $value); ?>"
                                                                required
                                                            >
                                                            <span><?php echo esc_html((string) $value); ?></span>
                                                        </label>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else : ?>
                                                <?php echo esc_html(implode(', ', $attribute['values'])); ?>
                                            <?php endif; ?>
                                        </dd>
                                    </div>
                                <?php endforeach; ?>
                            </dl>
                        <?php endif; ?>

                        <input type="hidden" name="ev_action" value="add_to_cart">
                        <input type="hidden" name="ev_product_id" value="<?php echo esc_attr((string) $prodotto_id); ?>">
                        <?php wp_nonce_field('ev_add_to_cart_' . $prodotto_id, 'ev_add_to_cart_nonce'); ?>

                        <p class="ev-single-product__variant-error" data-variant-error hidden>
                            Seleziona le varianti richieste prima di aggiungere al carrello.
                        </p>

                        <div class="ev-single-product__cta">
                            <button class="ev-btn ev-btn--primary" type="submit" name="ev_submit_action" value="add_to_cart">Aggiungi al carrello</button>
                            <button class="ev-btn ev-btn--ghost-dark" type="button">Acquista ora</button>
                        </div>
                    </form>

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
        var purchaseForm = document.querySelector('.ev-single-product__purchase-form');
        var variantError = document.querySelector('[data-variant-error]');
        var requiredVariantInputs = Array.prototype.slice.call(document.querySelectorAll('.ev-single-product__variant-radio[required]'));

        if (heroImage && thumbs.length > 0) {
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
        }

        if (purchaseForm && requiredVariantInputs.length > 0) {
            var allVariantGroupsSelected = function () {
                var groups = {};

                requiredVariantInputs.forEach(function (input) {
                    if (!groups[input.name]) {
                        groups[input.name] = [];
                    }

                    groups[input.name].push(input);
                });

                return Object.keys(groups).every(function (groupName) {
                    return groups[groupName].some(function (input) {
                        return input.checked;
                    });
                });
            };

            purchaseForm.addEventListener('submit', function (event) {
                var missingSelection = !allVariantGroupsSelected();

                if (variantError) {
                    variantError.hidden = !missingSelection;
                }

                if (missingSelection) {
                    event.preventDefault();
                }
            });

            requiredVariantInputs.forEach(function (input) {
                input.addEventListener('change', function () {
                    if (variantError) {
                        variantError.hidden = true;
                    }
                });
            });
        }
    });
</script>

<?php
get_footer();
