<?php
    //include tutti i file che descrivono le opzioni di configurazione del Sito dei Comuni
    foreach(glob(get_template_directory() . "/inc/admin/options/*.php") as $file){
        require $file;
    }
?>