<?php
/**
 * Plugin Name: ECCLA Simple Agenda
 * Description: Un calendrier léger et sur mesure pour gérer des événements avec PDF.
 * Version: 1.2
 * Author: Assistant ECCLA
 */

if (!defined('ABSPATH')) exit;

// Helper: Calculer la couleur de contraste (noir ou blanc)
function eccla_get_contrast_color($hexcolor) {
    $hexcolor = str_replace('#', '', $hexcolor);
    if (strlen($hexcolor) != 6) return '#181d1a';
    $r = hexdec(substr($hexcolor, 0, 2));
    $g = hexdec(substr($hexcolor, 2, 2));
    $b = hexdec(substr($hexcolor, 4, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    return ($yiq >= 128) ? '#181d1a' : '#ffffff';
}

// 1. Initialisation du Custom Post Type et de sa Taxonomie dédiée
add_action('init', 'eccla_agenda_init_cpt');
function eccla_agenda_init_cpt() {
    // Taxonomie dédiée (évite les conflits avec les catégories standards)
    register_taxonomy('eccla_event_cat', 'eccla_event', [
        'labels' => [
            'name' => 'Catégories d\'événements',
            'singular_name' => 'Catégorie d\'événement',
        ],
        'hierarchical' => true,
        'show_in_rest' => true,
        'public' => true,
        'rewrite' => ['slug' => 'cat-evenement'],
    ]);

    register_post_type('eccla_event', [
        'labels' => [
            'name' => 'Événements',
            'singular_name' => 'Événement',
            'add_new_item' => 'Ajouter un nouvel événement',
            'edit_item' => 'Modifier l\'événement'
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'evenements'],
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => ['title', 'editor', 'thumbnail'],
        'taxonomies' => ['eccla_event_cat'], // Utilise la nouvelle taxonomie
        'show_in_rest' => true
    ]);
}

// 2. Champs personnalisés (Meta Boxes)
add_action('add_meta_boxes', 'eccla_agenda_add_metaboxes');
function eccla_agenda_add_metaboxes() {
    add_meta_box('eccla_event_details', 'Détails de l\'événement', 'eccla_agenda_render_metabox', 'eccla_event', 'normal', 'high');
}

function eccla_agenda_render_metabox($post) {
    // Charger le color picker natif de WP
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('eccla-admin-js', plugin_dir_url(__FILE__) . 'assets/js/admin.js', ['wp-color-picker', 'jquery'], '1.0', true);

    $date = get_post_meta($post->ID, '_event_date', true);
    $pdf = get_post_meta($post->ID, '_event_pdf', true);
    wp_nonce_field('eccla_agenda_save', 'eccla_agenda_nonce');
    include plugin_dir_path(__FILE__) . 'templates/admin-metabox.php';
}

add_action('save_post', function($post_id) {
    if (!isset($_POST['eccla_agenda_nonce']) || !wp_verify_nonce($_POST['eccla_agenda_nonce'], 'eccla_agenda_save')) return;
    $fields = ['event_date', 'event_end_date', 'event_start_time', 'event_end_time', 'event_pdf', 'event_color'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, $_POST[$field]);
        }
    }
});

// 3. Chargement des Assets (CSS/JS)
function eccla_agenda_enqueue_assets($events = []) {
    wp_enqueue_style('eccla-agenda-css', plugin_dir_url(__FILE__) . 'assets/css/calendar.css');
    wp_enqueue_script('fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js', [], null, true);
    wp_enqueue_script('eccla-agenda-js', plugin_dir_url(__FILE__) . 'assets/js/calendar.js', ['fullcalendar'], '1.2', true);
    wp_localize_script('eccla-agenda-js', 'eccla_agenda_data', ['events' => $events]);
}

// 4. Shortcode : [eccla_agenda]
add_shortcode('eccla_agenda', function() {
    $query = new WP_Query(['post_type' => 'eccla_event', 'posts_per_page' => -1]);
    $events = [];
    while ($query->have_posts()) {
        $query->the_post();
        $id = get_the_ID();
        $date = get_post_meta($id, '_event_date', true);
        $end_date = get_post_meta($id, '_event_end_date', true);
        $start_time = get_post_meta($id, '_event_start_time', true);
        $end_time = get_post_meta($id, '_event_end_time', true);

        if ($date) {
            // FullCalendar a besoin que la date de fin soit exclusive (+1 jour pour l'affichage)
            $end_ts = $end_date ? strtotime($end_date . ' +1 day') : strtotime($date . ' +1 day');
            $color  = get_post_meta($id, '_event_color', true) ?: '#53a92c';
            
            $events[] = [
                'title' => get_the_title(),
                'start' => $date . ($start_time ? 'T' . $start_time : ''),
                'end'   => date('Y-m-d', $end_ts) . ($end_time ? 'T' . $end_time : ''),
                'color' => $color,
                'description' => get_the_content(),
                'pdf' => get_post_meta($id, '_event_pdf', true),
                'formatted_date' => date_i18n('d F Y', strtotime($date)),
                'display_time' => ($start_time ? $start_time : '') . ($end_time ? ' - ' . $end_time : '')
            ];
        }
    }
    wp_reset_postdata();
    eccla_agenda_enqueue_assets($events);
    
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/calendar-view.php';
    return ob_get_clean();
});

// 5. Shortcode : [eccla_upcoming_events]
add_shortcode('eccla_upcoming_events', function() {
    wp_enqueue_style('eccla-agenda-css', plugin_dir_url(__FILE__) . 'assets/css/calendar.css');
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $today = date('Y-m-d');
    $args = [
        'post_type'      => 'eccla_event',
        'posts_per_page' => 5,
        'paged'          => $paged,
        'meta_key'       => '_event_date',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_query'     => [['key' => '_event_date', 'value' => $today, 'compare' => '>=', 'type' => 'DATE']]
    ];
    $query = new WP_Query($args);
    $total_pages = $query->max_num_pages;

    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/upcoming-events-view.php';
    $output = ob_get_clean();
    wp_reset_postdata();
    return $output;
});
