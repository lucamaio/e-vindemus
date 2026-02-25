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

            $featured_image = get_post_meta(get_the_ID(), $prefix . 'immagine_evidenza', true);
            $gallery_images = get_post_meta(get_the_ID(), $prefix . 'galleria_immagini', true);

            $price             = get_post_meta(get_the_ID(), $prefix . 'prezzo', true);
            $short_description = get_post_meta(get_the_ID(), $prefix . 'descrizione_breve', true);
            $specifiche        = get_post_meta(get_the_ID(), $prefix . 'specifiche_tecniche', true);
            $descrizioni       = get_post_meta(get_the_ID(), $prefix . 'descrizioni', true);
            $recesso           = get_post_meta(get_the_ID(), $prefix . 'recesso_garanzia', true);
            $altre_info        = get_post_meta(get_the_ID(), $prefix . 'altre_informazioni', true);
            $stock             = get_post_meta(get_the_ID(), $prefix . 'quantita_disponibile', true);

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

            <article id="post-<?php the_ID(); ?>" <?php post_class('ev-single-product__article'); ?>>
                <div class="ev-single-product__media">
                    <div class="ev-single-product__hero-image-wrap">
                        <?php if (!empty($featured_image)) : ?>
                            <img
                                class="ev-single-product__hero-image"
                                src="<?php echo esc_url($featured_image); ?>"
                                alt="<?php echo esc_attr(get_the_title()); ?>"
                            >
                        <?php elseif (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large', ['class' => 'ev-single-product__hero-image']); ?>
                        <?php else : ?>
                            <div class="ev-single-product__hero-placeholder" aria-hidden="true">
                                <span>Anteprima prodotto</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($gallery_images) && is_array($gallery_images)) : ?>
                        <div class="ev-single-product__gallery" aria-label="Galleria immagini prodotto">
                            <?php foreach ($gallery_images as $image_url) : ?>
                                <figure class="ev-single-product__thumb">
                                    <img src="<?php echo esc_url($image_url); ?>" alt="Dettaglio di <?php echo esc_attr(get_the_title()); ?>">
                                </figure>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="ev-single-product__content">
                    <p class="ev-single-product__eyebrow">Scheda prodotto</p>
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

<?php
get_footer();
