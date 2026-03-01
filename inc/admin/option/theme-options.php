<?php

/**
 * Recupera le opzioni alert della topbar dalla pagina Configurazione (option_key: homepage).
 * Mantiene fallback legacy per compatibilita con installazioni gia esistenti.
 *
 * @return array{message:string,color:string,show:bool}
 */
function ev_get_home_alert_options() {
    $defaults = [
        'message' => 'Spedizione gratuita sopra i 59 euro',
        'color'   => 'blue',
        'show'    => false,
    ];

    $allowed_colors = ['yellow', 'blue', 'red', 'green'];
    $homepage_options = get_option('homepage', []);

    if (is_array($homepage_options) && !empty($homepage_options)) {
        $message = isset($homepage_options['home_alert_message'])
            ? sanitize_text_field((string) $homepage_options['home_alert_message'])
            : $defaults['message'];

        $color = isset($homepage_options['home_alert_color'])
            ? sanitize_key((string) $homepage_options['home_alert_color'])
            : $defaults['color'];

        if (!in_array($color, $allowed_colors, true)) {
            $color = $defaults['color'];
        }

        $show = !empty($homepage_options['home_alert_show']);

        // Se sono valorizzate date inizio/fine, l'alert viene mostrato solo nella finestra temporale attiva.
        $now = current_time('timestamp');
        $start = isset($homepage_options['home_alert_start_date']) ? (int) $homepage_options['home_alert_start_date'] : 0;
        $end = isset($homepage_options['home_alert_end_date']) ? (int) $homepage_options['home_alert_end_date'] : 0;

        if ($show && $start > 0 && $now < $start) {
            $show = false;
        }

        if ($show && $end > 0 && $now > $end) {
            $show = false;
        }

        return [
            'message' => $message,
            'color'   => $color,
            'show'    => (bool) $show,
        ];
    }

    // Fallback legacy: vecchia pagina "Opzioni Homepage".
    $legacy_options = get_option('ev_home_alert_options', []);
    if (!is_array($legacy_options) || empty($legacy_options)) {
        return $defaults;
    }

    $message = isset($legacy_options['message']) ? sanitize_text_field((string) $legacy_options['message']) : $defaults['message'];
    $color = isset($legacy_options['color']) ? sanitize_key((string) $legacy_options['color']) : $defaults['color'];
    if (!in_array($color, $allowed_colors, true)) {
        $color = $defaults['color'];
    }

    return [
        'message' => $message,
        'color'   => $color,
        'show'    => !empty($legacy_options['show']),
    ];
}
