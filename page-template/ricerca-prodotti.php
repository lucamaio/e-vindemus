<?php
/**
 * Template Name: Pagina Ricerca Prodotti
 * Template Post Type: page
 */

get_header();

$filters = function_exists('ev_get_search_product_filters')
    ? ev_get_search_product_filters()
    : [
        'tipo' => isset($_GET['tipo']) ? sanitize_title(wp_unslash((string) $_GET['tipo'])) : '',
        'prezzo_min' => isset($_GET['prezzo_min']) ? sanitize_text_field(wp_unslash((string) $_GET['prezzo_min'])) : '',
        'prezzo_max' => isset($_GET['prezzo_max']) ? sanitize_text_field(wp_unslash((string) $_GET['prezzo_max'])) : '',
    ];

$type_terms = get_terms([
    'taxonomy'   => 'categoria_prodotto',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
]);

if (is_wp_error($type_terms)) {
    $type_terms = [];
}
?>

<main>
    <section class="ev-home ev-mobile-search-page" aria-label="Ricerca prodotti mobile">
        <section class="ev-products">
            <div class="ev-products__head">
                <h1>Cerca prodotti</h1>
                <p>Trova rapidamente prodotti filtrando per tipologia e fascia di prezzo.</p>
            </div>

            <form class="ev-mobile-search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="hidden" name="post_type" value="prodotto">

                <label class="screen-reader-text" for="ev-mobile-search-input">Cerca prodotti</label>
                <input
                    id="ev-mobile-search-input"
                    type="search"
                    name="s"
                    placeholder="Cerca brand, categorie o prodotti..."
                    autofocus
                    value="<?php echo esc_attr(get_search_query()); ?>">

                <label class="screen-reader-text" for="ev-mobile-search-tipo">Tipologia</label>
                <select id="ev-mobile-search-tipo" name="tipo">
                    <option value="">Tutte le tipologie</option>
                    <?php foreach ($type_terms as $term) : ?>
                        <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($filters['tipo'], $term->slug); ?>>
                            <?php echo esc_html($term->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="ev-mobile-search-form__row">
                    <label class="screen-reader-text" for="ev-mobile-search-price-min">Prezzo minimo</label>
                    <input
                        id="ev-mobile-search-price-min"
                        type="number"
                        name="prezzo_min"
                        min="0"
                        step="0.01"
                        placeholder="Prezzo min"
                        value="<?php echo esc_attr($filters['prezzo_min']); ?>">

                    <label class="screen-reader-text" for="ev-mobile-search-price-max">Prezzo massimo</label>
                    <input
                        id="ev-mobile-search-price-max"
                        type="number"
                        name="prezzo_max"
                        min="0"
                        step="0.01"
                        placeholder="Prezzo max"
                        value="<?php echo esc_attr($filters['prezzo_max']); ?>">
                </div>

                <button type="submit" class="ev-mobile-search-form__submit">Cerca</button>
            </form>
        </section>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.matchMedia('(max-width: 980px)').matches) {
        var input = document.getElementById('ev-mobile-search-input');
        if (input) {
            window.setTimeout(function () {
                input.focus();
            }, 120);
        }
    }
});
</script>

<?php get_footer(); ?>
