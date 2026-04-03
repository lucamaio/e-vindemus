<?php
get_header();
?>

<main>
    <section class="container py-5 text-center" aria-label="Pagina non trovata">
        <h1 class="mb-3">404 - Pagina non trovata</h1>
        <p class="mb-4">La pagina che hai cercato non esiste oppure è stata spostata.</p>
        <a class="btn btn-primary" href="<?php echo esc_url(home_url('/')); ?>">Torna alla home</a>
    </section>
</main>

<?php get_footer(); ?>
