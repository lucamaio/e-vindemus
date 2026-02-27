<?php
/**
 * Template Name: Pagina Pokemon
 * Template Post Type: page
 */

get_header();

$prefix = '_dci_prodotto_';

// Sottocategorie principali da mostrare nella pagina Pokemon.
$pokemon_subcategories = array(
    'Accessori',
    'Box',
    'Bustine singole',
    'Carte singole',
    'Carte gradate',
);

/**
 * Normalizza una stringa per confronti case-insensitive.
 */
function ev_normalize_label($value)
{
    return strtolower(trim(wp_strip_all_tags((string) $value)));
}

/**
 * Ricava la tipologia di un prodotto a partire dal meta o dalla tassonomia.
 */
function ev_get_product_type_label($product_id, $prefix)
{
    $type = get_post_meta($product_id, $prefix . 'tipo_prodotto', true);

    if (empty($type)) {
        $terms = get_the_terms($product_id, 'categoria_prodotto');

        if (!is_wp_error($terms) && !empty($terms)) {
            $type = $terms[0]->name;
        }
    }

    return !empty($type) ? $type : 'N/D';
}

/**
 * Recupera gli ID termine della categoria Pokemon (padre + figli).
 */
function ev_get_pokemon_term_ids()
{
    $pokemon_term = get_term_by('slug', 'pokemon', 'categoria_prodotto');

    if (!$pokemon_term || is_wp_error($pokemon_term)) {
        $pokemon_term = get_term_by('name', 'Pokemon', 'categoria_prodotto');
    }

    if (!$pokemon_term || is_wp_error($pokemon_term)) {
        return array();
    }

    $children_ids = get_term_children((int) $pokemon_term->term_id, 'categoria_prodotto');

    if (is_wp_error($children_ids)) {
        return array((int) $pokemon_term->term_id);
    }

    return array_merge(array((int) $pokemon_term->term_id), array_map('intval', $children_ids));
}

$search_query = isset($_GET['pokemon_search']) ? sanitize_text_field(wp_unslash($_GET['pokemon_search'])) : '';
$selected_subtypes_raw = isset($_GET['subtipologie']) ? (array) wp_unslash($_GET['subtipologie']) : array();
$selected_subtypes = array_values(array_filter(array_map('sanitize_text_field', $selected_subtypes_raw)));
$selected_subtypes_normalized = array_map('ev_normalize_label', $selected_subtypes);

$tax_query = array();
$pokemon_term_ids = ev_get_pokemon_term_ids();

if (!empty($pokemon_term_ids)) {
    $tax_query[] = array(
        'taxonomy' => 'categoria_prodotto',
        'field'    => 'term_id',
        'terms'    => $pokemon_term_ids,
        'operator' => 'IN',
    );
}

$query_args = array(
    'post_type'      => 'prodotto',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    's'              => $search_query,
);

if (!empty($tax_query)) {
    $query_args['tax_query'] = $tax_query;
}

$pokemon_query = new WP_Query($query_args);
$filtered_posts = array();

// Filtro aggiuntivo per sottotipologie (multi-selezione).
if ($pokemon_query->have_posts()) {
    foreach ($pokemon_query->posts as $product_post) {
        $type_label = ev_get_product_type_label($product_post->ID, $prefix);
        $type_normalized = ev_normalize_label($type_label);

        // Se non ci sono sottotipologie selezionate, includo tutto.
        if (empty($selected_subtypes_normalized) || in_array($type_normalized, $selected_subtypes_normalized, true)) {
            $filtered_posts[] = $product_post;
        }
    }
}

$featured_posts = array_slice($filtered_posts, 0, 3);
?>

<main>
    <section class="ev-home ev-pokemon" aria-label="Pagina Pokemon">
        <!-- HERO: introduzione della sezione e-commerce Pokemon -->
        <section class="ev-hero__image-wrap ev-pokemon__hero">
            <img
                class="ev-hero__image"
                src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/img-carousel1.png'); ?>"
                alt="Collezione prodotti Pokemon" />
            <div class="ev-hero__overlay" aria-hidden="true"></div>
            <div class="ev-hero__content">
                <span class="ev-badge">Collezione Pokemon</span>
                <h1>Scopri la nostra area Pokemon</h1>
                <p>
                    Qui trovi carte, box, accessori e articoli da collezione selezionati per appassionati,
                    giocatori e collezionisti. Usa i filtri per trovare subito il prodotto Pokemon più adatto.
                </p>
            </div>
        </section>

        <!-- Sottocategorie Pokemon -->
        <section class="ev-products" aria-label="Sottocategorie Pokemon">
            <div class="ev-products__head">
                <h2>Sottocategorie Pokemon</h2>
                <p>Naviga rapidamente tra le principali sottotipologie della categoria Pokemon.</p>
            </div>
            <ul class="ev-pokemon__subcategories">
                <?php foreach ($pokemon_subcategories as $subcategory) : ?>
                    <li class="ev-pokemon__subcategory-item"><?php echo esc_html($subcategory); ?></li>
                <?php endforeach; ?>
            </ul>
        </section>

        <!-- Barra ricerca + filtri -->
        <section class="ev-products" aria-label="Ricerca prodotti Pokemon">
            <div class="ev-products__head">
                <h2>Trova il tuo prodotto Pokemon</h2>
                <p>Cerca per nome e filtra per una o più sottotipologie.</p>
            </div>

            <form class="ev-pokemon__filter-form" method="get" action="<?php echo esc_url(get_permalink()); ?>">
                <label for="pokemon-search" class="ev-pokemon__label">Cerca prodotto</label>
                <input
                    id="pokemon-search"
                    type="search"
                    name="pokemon_search"
                    value="<?php echo esc_attr($search_query); ?>"
                    placeholder="Es. Charizard, Box Allenatore Fuoriclasse..." />

                <fieldset class="ev-pokemon__fieldset">
                    <legend>Filtra per sottotipologia</legend>
                    <div class="ev-pokemon__checkboxes">
                        <?php foreach ($pokemon_subcategories as $subcategory) : ?>
                            <?php $is_checked = in_array(ev_normalize_label($subcategory), $selected_subtypes_normalized, true); ?>
                            <label class="ev-pokemon__checkbox-label">
                                <input
                                    type="checkbox"
                                    name="subtipologie[]"
                                    value="<?php echo esc_attr($subcategory); ?>"
                                    <?php checked($is_checked); ?> />
                                <span><?php echo esc_html($subcategory); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <div class="ev-pokemon__actions">
                    <button type="submit" class="ev-btn ev-btn--small">Applica filtri</button>
                    <a class="ev-btn ev-btn--ghost-dark" href="<?php echo esc_url(get_permalink()); ?>">Reset</a>
                </div>
            </form>
        </section>

        <!-- Tutti i prodotti Pokemon -->
        <section class="ev-products" aria-label="Tutti i prodotti Pokemon">
            <div class="ev-products__head">
                <h2>Tutti i prodotti Pokemon</h2>
                <p><?php echo esc_html(sprintf('Risultati trovati: %d', count($filtered_posts))); ?></p>
            </div>

            <div class="ev-products__grid">
                <?php if (!empty($filtered_posts)) : ?>
                    <?php foreach ($filtered_posts as $post) : ?>
                        <?php setup_postdata($post); ?>
                        <?php get_template_part('template-parts/prodotto/card'); ?>
                    <?php endforeach; ?>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p>Nessun prodotto Pokemon trovato con i filtri selezionati.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Pokemon in evidenza: primi 3 elementi -->
        <section class="ev-products" aria-label="Pokemon in evidenza">
            <div class="ev-products__head">
                <h2>Pokemon in evidenza</h2>
                <p>Una selezione rapida dei primi tre prodotti Pokemon attualmente disponibili.</p>
            </div>

            <div class="ev-products__grid ev-pokemon__featured-grid">
                <?php if (!empty($featured_posts)) : ?>
                    <?php foreach ($featured_posts as $post) : ?>
                        <?php setup_postdata($post); ?>
                        <?php get_template_part('template-parts/prodotto/card'); ?>
                    <?php endforeach; ?>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p>Nessun prodotto in evidenza disponibile.</p>
                <?php endif; ?>
            </div>
        </section>
    </section>
</main>

<?php get_footer();
