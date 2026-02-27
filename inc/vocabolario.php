<?php

if(!function_exists('dci_categorie_prodotti_array')) {
    function dci_categorie_prodotti_array() {
        $categorie_prodotti_array = [
            'Pokemon' =>
                [
                    'Accessori',
                    'Box',
                    'Bustine singole',
                    'Carte Singole',
                    'Carte Gradate',
                    'Carte Jumbo',
                    'Collezioni Speciali',
                    'Gadget',
                    'Mazzi',
                    'Mystery Box'
                ],
            "Abbigliamento" =>
                [
                    "T-shirt",
                    "Felpe",
                    "Camicie",
                    "Cappelli",
                    "Giacche",
                    "Pantaloni",
                    "Scarpe",
                    "Accessori",
                    "Zaini", 
                    'Intimo',
                    "Costumi da bagno",
                    "Borse",
                    "Guanti",
                    "Sciarpe",
                    "Calze",
                    "Orologi"
                ]
        ]; 

        return $categorie_prodotti_array;
    }
}