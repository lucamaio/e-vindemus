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

// Funzione per recuperare array di stati prodotti
if(!function_exists('dci_stati_prodotti_array')) {
    function dci_stati_prodotti_array() {
        $stati_prodotti_array = [
            "Disponibile",
            "Esaurito",
            "In arrivo", 
            "Non disponibile"
        ];

        return $stati_prodotti_array;
    }
}

// Funzione per recuperare array di materiali abbigliamento
if(!function_exists('dci_materiali_abbigliamento_array')) {
    function dci_materiali_abbigliamento_array() {
        $materiali_abbigliamento_array = [
            "Cotone",
            "Poliestere",
            "Lana",
            "Seta",
            "Denim",
            "Velluto",
            "Lycra",
            "Nylon",
            "Viscosa",
            "Acrilico"
        ];

        return $materiali_abbigliamento_array;
    }
}

// Funzione per recuperare array di taglie abbigliamento

if(!function_exists('dci_taglie_abbigliamento_array')) {
    function dci_taglie_abbigliamento_array() {
        $taglie_abbigliamento_array = [
            "XS",
            "S",
            "M",
            "L",
            "XL",
            "XXL",
            "3XL"
        ];

        return $taglie_abbigliamento_array;
    }
}

// funzione per recuperare il sesso (uomo, donna, unisex)

if(!function_exists('dci_sesso_abbigliamento_array')) {
    function dci_sesso_abbigliamento_array() {
        $sesso_abbigliamento_array = [
            "Uomo",
            "Donna",
            "Unisex"
        ];

        return $sesso_abbigliamento_array;
    }
}


// Funzione per recuperare array di taglie scarpe abbigliamento

if(!function_exists('dci_taglie_scarpe_array')) {
    function dci_taglie_scarpe_array() {
        $taglie_scarpe_array = [
            "36",
            "37",
            "38",
            "39",
            "40",
            "41",
            "42",
            "43",
            "44",
            "45",
            "46",
            "47",
            "48"
        ];

        return $taglie_scarpe_array;
    }
}

// Funzione che recupera array di colori abbigliamento
if(!function_exists('dci_colori_abbigliamento_array')) {
    function dci_colori_abbigliamento_array() {
        $colori_abbigliamento_array = [
            "Bianco",
            "Nero",
            "Rosso",
            "Blu",
            "Verde",
            "Giallo",
            "Arancione",
            "cyan",
            "Viola",
            "Rosa",
            "Grigio",
            "Marrone",
            "Beige",
            "Azzurro",
            "Turchese",
            "Lilla",
            "Oro",
            "Argento",
            "Multicolore"
        ];

        return $colori_abbigliamento_array;
    }
}