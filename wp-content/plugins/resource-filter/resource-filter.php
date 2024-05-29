<?php
/*
Plugin Name: Resource Filter
Description: A plugin to add a custom post type "Resource" with custom taxonomies and an AJAX filter.
Version: 1.0
Author: Pooja Parmar
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type
function create_resource_post_type() {
    $labels = array(
        'name' => 'Resources',
        'singular_name' => 'Resource',
        'menu_name' => 'Resources',
        'name_admin_bar' => 'Resource',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Resource',
        'new_item' => 'New Resource',
        'edit_item' => 'Edit Resource',
        'view_item' => 'View Resource',
        'all_items' => 'All Resources',
        'search_items' => 'Search Resources',
        'parent_item_colon' => 'Parent Resources:',
        'not_found' => 'No resources found.',
        'not_found_in_trash' => 'No resources found in Trash.',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'resource'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
    );

    register_post_type('resource', $args);
}
add_action('init', 'create_resource_post_type');

// Register Custom Taxonomies
function create_resource_taxonomies() {
    // Resource Type
    $labels = array(
        'name' => 'Resource Types',
        'singular_name' => 'Resource Type',
        'search_items' => 'Search Resource Types',
        'all_items' => 'All Resource Types',
        'parent_item' => 'Parent Resource Type',
        'parent_item_colon' => 'Parent Resource Type:',
        'edit_item' => 'Edit Resource Type',
        'update_item' => 'Update Resource Type',
        'add_new_item' => 'Add New Resource Type',
        'new_item_name' => 'New Resource Type Name',
        'menu_name' => 'Resource Type',
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'resource-type'),
    );

    register_taxonomy('resource_type', array('resource'), $args);

    // Resource Topic
    $labels = array(
        'name' => 'Resource Topics',
        'singular_name' => 'Resource Topic',
        'search_items' => 'Search Resource Topics',
        'all_items' => 'All Resource Topics',
        'parent_item' => 'Parent Resource Topic',
        'parent_item_colon' => 'Parent Resource Topic:',
        'edit_item' => 'Edit Resource Topic',
        'update_item' => 'Update Resource Topic',
        'add_new_item' => 'Add New Resource Topic',
        'new_item_name' => 'New Resource Topic Name',
        'menu_name' => 'Resource Topic',
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'resource-topic'),
    );

    register_taxonomy('resource_topic', array('resource'), $args);
}
add_action('init', 'create_resource_taxonomies');

// Enqueue Scripts
function resource_filter_enqueue_scripts() {
   
        wp_enqueue_script('resource-filter', plugin_dir_url(__FILE__) . 'resource-filter.js', array('jquery'), null, true);
        wp_localize_script('resource-filter', 'rfajax', array('ajaxurl' => admin_url('admin-ajax.php')));
    
}
add_action('wp_enqueue_scripts', 'resource_filter_enqueue_scripts');

// AJAX Handler
function resource_filter_ajax() {
    $args = array(
        'post_type' => 'resource',
        'posts_per_page' => -1,
    );

    if (isset($_POST['resource_type']) && $_POST['resource_type'] != '') {
        $args['tax_query'][] = array(
            'taxonomy' => 'resource_type',
            'field' => 'slug',
            'terms' => sanitize_text_field($_POST['resource_type']),
        );
    }

    if (isset($_POST['resource_topic']) && $_POST['resource_topic'] != '') {
        $args['tax_query'][] = array(
            'taxonomy' => 'resource_topic',
            'field' => 'slug',
            'terms' => sanitize_text_field($_POST['resource_topic']),
        );
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $args['s'] = sanitize_text_field($_POST['keyword']);
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            include plugin_dir_path(__FILE__) . 'templates/content-resource.php';
        }
    } else {
        echo 'No resources found';
    }

    wp_die();
}
add_action('wp_ajax_resource_filter', 'resource_filter_ajax');
add_action('wp_ajax_nopriv_resource_filter', 'resource_filter_ajax');

// Shortcode to display filter form and results
function resource_filter_shortcode() {
    ob_start();
    ?>
    <form id="resource-filter">
        <select name="resource_type">
            <option value="">Select Resource Type</option>
            <?php
            $terms = get_terms(array('taxonomy' => 'resource_type', 'hide_empty' => false));
            foreach ($terms as $term) {
                echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
            }
            ?>
        </select>

        <select name="resource_topic">
            <option value="">Select Resource Topic</option>
            <?php
            $terms = get_terms(array('taxonomy' => 'resource_topic', 'hide_empty' => false));
            foreach ($terms as $term) {
                echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
            }
            ?>
        </select>

        <input type="text" name="keyword" placeholder="Keyword">

        <button type="submit">Filter</button>
    </form>

    <div id="resource-results">
        <?php
        $args = array(
            'post_type' => 'resource',
            'posts_per_page' => -1,
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                include plugin_dir_path(__FILE__) . 'templates/content-resource.php';
            }
        } else {
            echo 'No resources found';
        }
        ?>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('resource_filter', 'resource_filter_shortcode');


?>
