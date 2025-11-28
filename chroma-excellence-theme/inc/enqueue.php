<?php
/**
 * Enqueue scripts and styles.
 *
 * @package Chroma_Excellence
 */

if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly.
}

/**
 * Determine whether map assets should be enqueued.
 */
function chroma_should_load_maps()
{
        $should_load_maps = is_post_type_archive('location') || is_singular('location') || is_page('locations');

        if (is_front_page() && function_exists('chroma_home_locations_preview')) {
                $locations_preview = chroma_home_locations_preview();
                $should_load_maps = $should_load_maps || (!empty($locations_preview['map_points']));
        }

        return $should_load_maps;
}

/**
 * Enqueue theme styles and scripts
 */
function chroma_enqueue_assets()
{
        // Self-Hosted Fonts (Outfit, Playfair Display).
        $fonts_path = CHROMA_THEME_DIR . '/assets/css/fonts.css';
        $fonts_version = file_exists($fonts_path) ? filemtime($fonts_path) : CHROMA_VERSION;
        wp_enqueue_style(
                'chroma-fonts',
                CHROMA_THEME_URI . '/assets/css/fonts.css',
                array(),
                $fonts_version,
                'all'
        );

        // Font Awesome (Local).
        $fa_path = CHROMA_THEME_DIR . '/assets/css/font-awesome.css';
        $fa_version = file_exists($fa_path) ? filemtime($fa_path) : '6.4.0';
        wp_enqueue_style(
                'font-awesome',
                CHROMA_THEME_URI . '/assets/css/font-awesome.css',
                array(),
                $fa_version,
                'all' // Load synchronously to ensure icons appear
        );

        // Compiled Tailwind CSS.
        $css_path = CHROMA_THEME_DIR . '/assets/css/main.css';
        $css_version = file_exists($css_path) ? filemtime($css_path) : CHROMA_VERSION;

        // Compiled Tailwind CSS - loads synchronously
        wp_enqueue_style(
                'chroma-main',
                CHROMA_THEME_URI . '/assets/css/main.css',
                array(),
                $css_version,
                'all' // Load normally to prevent FOUC
        );

        // Chart.js for curriculum radar (homepage and program pages).
        $script_dependencies = array();

        if (is_front_page() || is_singular('program') || is_post_type_archive('program')) {
                wp_enqueue_script(
                        'chartjs',
                        'https://cdn.jsdelivr.net/npm/chart.js',
                        array(),
                        '4.4.1',
                        true
                );

                wp_script_add_data('chartjs', 'defer', true);
                $script_dependencies[] = 'chartjs';
        }

        // Main JavaScript.
        $js_path = CHROMA_THEME_DIR . '/assets/js/main.js';
        $js_version = file_exists($js_path) ? filemtime($js_path) : CHROMA_VERSION;

        wp_enqueue_script(
                'chroma-main',
                CHROMA_THEME_URI . '/assets/js/main.js',
                $script_dependencies,
                $js_version,
                true
        );

        wp_script_add_data('chroma-main', 'defer', true);

        // Map Facade (Lazy Load Leaflet).
        $should_load_maps = chroma_should_load_maps();

        if ($should_load_maps) {
                wp_enqueue_script(
                        'chroma-map-facade',
                        CHROMA_THEME_URI . '/assets/js/map-facade.js',
                        array('chroma-main'), // Depend on main to ensure chromaData is available
                        $js_version,
                        true
                );
                wp_script_add_data('chroma-map-facade', 'defer', true);
        }

        // Localize script for AJAX and dynamic data.
        wp_localize_script(
                'chroma-main',
                'chromaData',
                array(
                        'ajaxUrl' => admin_url('admin-ajax.php'),
                        'nonce' => wp_create_nonce('chroma_nonce'),
                        'themeUrl' => CHROMA_THEME_URI,
                        'homeUrl' => home_url(),
                )
        );
}
add_action('wp_enqueue_scripts', 'chroma_enqueue_assets');



/**
 * Add resource hints for external assets to improve initial page performance.
 */
function chroma_resource_hints($urls, $relation_type)
{
        if ('preconnect' === $relation_type) {

                if (is_front_page() || is_singular('program') || is_post_type_archive('program')) {
                        $urls[] = 'https://cdn.jsdelivr.net';
                }

                if (chroma_should_load_maps()) {
                        $urls[] = 'https://unpkg.com';
                }

                // Preconnect to external origins identified in audit
                $urls[] = 'https://widgets.leadconnectorhq.com';
                $urls[] = 'https://services.leadconnectorhq.com';
                $urls[] = 'https://images.leadconnectorhq.com';
        }

        if ('dns-prefetch' === $relation_type) {

                if (is_front_page() || is_singular('program') || is_post_type_archive('program')) {
                        $urls[] = '//cdn.jsdelivr.net';
                }

                if (chroma_should_load_maps()) {
                        $urls[] = '//unpkg.com';
                }
                $urls[] = '//widgets.leadconnectorhq.com';
                $urls[] = '//services.leadconnectorhq.com';
                $urls[] = '//images.leadconnectorhq.com';
        }

        return array_unique($urls, SORT_REGULAR);
}
add_filter('wp_resource_hints', 'chroma_resource_hints', 10, 2);

/**
 * Enqueue admin assets
 */
function chroma_enqueue_admin_assets($hook)
{
        // Only load on post edit screens
        if ('post.php' !== $hook && 'post-new.php' !== $hook) {
                return;
        }

        // Font Awesome for icon previews in admin (using local version)
        $fa_path = CHROMA_THEME_DIR . '/assets/css/font-awesome.css';
        $fa_version = file_exists($fa_path) ? filemtime($fa_path) : '6.4.0';

        wp_enqueue_style(
                'font-awesome-admin',
                CHROMA_THEME_URI . '/assets/css/font-awesome.css',
                array(),
                $fa_version // Use same version as frontend for consistency
        );

        // Media uploader
        wp_enqueue_media();

        // Custom admin script for media uploader
        wp_enqueue_script(
                'chroma-admin',
                CHROMA_THEME_URI . '/assets/js/admin.js',
                array('jquery'),
                CHROMA_VERSION,
                true
        );
}
add_action('admin_enqueue_scripts', 'chroma_enqueue_admin_assets');

/**
 * Async load CSS for fonts only (not main CSS to prevent FOUC)
 */
function chroma_async_styles($html, $handle, $href, $media)
{
        // Only defer Google Fonts
        if ('chroma-fonts' === $handle) {
                $html = str_replace("media='print'", "media='print' onload=\"this.media='all'\"", $html);
                // Add fallback for no-js
                $html .= "<noscript><link rel='stylesheet' href='{$href}' media='all'></noscript>";
        }
        return $html;
}
add_filter('style_loader_tag', 'chroma_async_styles', 10, 4);


