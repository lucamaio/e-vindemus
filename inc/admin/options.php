<?php
// include tutti i file che descrivono le opzioni di configurazione del tema
foreach (glob(get_template_directory() . '/inc/admin/option/*.php') as $file) {
    require $file;
}
?>
