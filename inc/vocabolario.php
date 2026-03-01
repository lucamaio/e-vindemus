<?php

// Funzione per recuperare le posizioni evidenziate dei prodotti

if(!function_exists('dci_posizioni_evidenziata_array')) {
    function dci_posizioni_evidenziata_array() {
        $posizioni_evidenziata_array = [
            "Homepage",
            "Offerte",
            "Novità",
            'Pokemon',
            'Abbigliamento',
            'Best seller'
        ];

        return $posizioni_evidenziata_array;
    }
}

// Funzione per recuperare array di categorie prodotti
if(!function_exists('dci_categorie_prodotti_array')) {
    function dci_categorie_prodotti_array() {
        $categorie_prodotti_array = [
            'Accessori' =>
                [
                    'Testa e collo' => [
                        'Cappelli',
                        'Guanti',
                        'Sciarpe',
                    ],
                    'Borse e zaini' => [
                        'Zaini',
                        'Borse',
                        'Portafogli',
                        'Marsupi'
                    ],
                    'Mani e polsi' => [
                        'Bracciali',
                        'Cinture'
                    ],
                    'Gioielli' => [
                        'Orecchini',
                        'Collane',
                        'Anelli',
                        'Orologi'
                    ],
                    'Accessori vari' => [
                        'Occhiali da sole',
                        'Cinture',
                        'Calze',
                        'Cravatte',
                        'Papillon',
                    ],
                    'Accessori per la casa' => [
                        'Tazze',
                        'Cuscini',
                        'Poster'
                    ]
                ],
            'Pokemon' =>
                [
                    'Box',
                    'Bustine singole',
                    'Carte Singole',
                    'Carte Gradate',
                    'Collezioni Speciali',
                    'Gadget',
                    'Mazzi',
                    'Mystery Box'
                ],
            "Abbigliamento" =>
                [
                    "T-shirt",
                    "Felpe",
                    "Maglioni",
                    "Camicie",
                    "Giacche",
                    "Pantaloni",
                    "J-eans",
                    "Scarpe",
                    'Intimo',
                    "Costumi da bagno",
                    'Pigiami',
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
