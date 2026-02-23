<?php

/**
 * Opzioni tema: messaggio topbar homepage.
 */

function ev_get_home_alert_options() {
    $defaults = [
        'message' => '🚚 Spedizione gratuita sopra i 59€ · Resi entro 30 giorni',
        'color'   => 'blue',
    ];

    $saved_options = get_option('ev_home_alert_options', []);

    if (!is_array($saved_options)) {
        return $defaults;
    }

    return wp_parse_args($saved_options, $defaults);
}

function ev_register_home_alert_settings() {
    register_setting(
        'ev_home_alert_group',
        'ev_home_alert_options',
        [
            'type'              => 'array',
            'sanitize_callback' => 'ev_sanitize_home_alert_options',
            'default'           => [
                'message' => '🚚 Spedizione gratuita sopra i 59€ · Resi entro 30 giorni',
                'color'   => 'blue',
            ],
        ]
    );

    add_settings_section(
        'ev_home_alert_section',
        'Messaggio alert homepage',
        'ev_home_alert_section_description', // Callback per la descrizione della sezione
        'ev-home-alert-options'
    );

    add_settings_field(
        'ev_home_alert_message',
        'Testo messaggio',
        'ev_home_alert_message_field',  // Callback per il campo del messaggio
        'ev-home-alert-options',
        'ev_home_alert_section'
    );

    add_settings_field(
        'ev_home_alert_color',
        'Colore alert',
        'ev_home_alert_color_field', // Callback per il campo del colore
        'ev-home-alert-options',
        'ev_home_alert_section'
    );

    add_settings_field(
        'ev_home_show_msg',
        'Mostra messaggio alert',
        'ev_home_alert_show_msg_field', // Callback per il campo di visualizzazione del messaggio
        'ev-home-alert-options',
        'ev_home_alert_section'
    );
}
add_action('admin_init', 'ev_register_home_alert_settings');

function ev_sanitize_home_alert_options($input) {
    $allowed_colors = ['yellow', 'blue', 'red', 'green'];

    $message = '';
    if (isset($input['message'])) {
        $message = sanitize_text_field($input['message']);
    }

    $color = 'blue';
    if (isset($input['color']) && in_array($input['color'], $allowed_colors, true)) {
        $color = $input['color'];
    }

    $show = isset($input['show']) ? (bool) $input['show'] : false;

    return [
        'message' => $message,
        'color'   => $color,
        'show'    => $show,
    ];
}

function ev_home_alert_section_description() {
    echo '<p>Configura il messaggio in alto della homepage e il colore della barra alert.</p>';
}

function ev_home_alert_message_field() {
    $options = ev_get_home_alert_options();
    ?>
    <input
        type="text"
        class="regular-text"
        name="ev_home_alert_options[message]"
        value="<?php echo esc_attr($options['message']); ?>"
        placeholder="Inserisci il messaggio da mostrare in alto"
    >
    <?php
}

function ev_home_alert_color_field() {
    $options = ev_get_home_alert_options();
    $colors  = [
        'yellow' => 'Giallo',
        'blue'   => 'Blu',
        'red'    => 'Rosso',
        'green'  => 'Verde',
    ];
    ?>
    <select name="ev_home_alert_options[color]">
        <?php foreach ($colors as $value => $label) : ?>
            <option value="<?php echo esc_attr($value); ?>" <?php selected($options['color'], $value); ?>>
                <?php echo esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

function ev_home_alert_show_msg_field() {
    $options = ev_get_home_alert_options();
    ?>
    <label>
        <input
            type="checkbox"
            name="ev_home_alert_options[show]"
            value="1"
            <?php checked($options['show'], true); ?>
        >
        Mostra messaggio alert
    </label>
    <?php
}
function ev_add_home_alert_options_page() {
    add_theme_page(
        'Opzioni Homepage',
        'Opzioni Homepage',
        'manage_options',
        'ev-home-alert-options',
        'ev_render_home_alert_options_page'
    );
}
add_action('admin_menu', 'ev_add_home_alert_options_page');

function ev_render_home_alert_options_page() {
    ?>
    <div class="wrap">
        <h1>Opzioni Homepage</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('ev_home_alert_group');
            do_settings_sections('ev-home-alert-options');
            submit_button('Salva impostazioni');
            ?>
        </form>
    </div>
    <?php
}
