<?php
/**
 * Navigation Menus with Tailwind Support
 *
 * @package Chroma_Excellence
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Register navigation menus
 */
function chroma_register_menus()
{
	register_nav_menus(array(
		'primary' => __('Primary Menu', 'chroma-excellence'),
		'footer' => __('Footer Menu', 'chroma-excellence'),
		'footer_contact' => __('Footer Contact Menu', 'chroma-excellence'),
	));
}
add_action('init', 'chroma_register_menus');

/**
 * Primary Navigation with Tailwind classes
 */
function chroma_primary_nav()
{
	wp_nav_menu(array(
		'theme_location' => 'primary',
		'container' => false,
		'menu_class' => '',
		'fallback_cb' => 'chroma_primary_nav_fallback',
		'items_wrap' => '%3$s',
		'depth' => 1,
		'walker' => new Chroma_Primary_Nav_Walker(),
	));
}

/**
 * Footer Navigation
 */
function chroma_footer_nav()
{
	wp_nav_menu(array(
		'theme_location' => 'footer',
		'container' => false,
{
	function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
	{
		$output .= '<a href="' . esc_url($item->url) . '" class="block hover:text-white transition">';
		$output .= esc_html($item->title);
		$output .= '</a>';
	}
}

/**
 * Mobile Navigation
 */
function chroma_mobile_nav()
{
	wp_nav_menu(array(
		'theme_location' => 'primary',
		'container' => false,
		'menu_class' => 'flex flex-col space-y-2',
		'fallback_cb' => 'chroma_mobile_nav_fallback',
		'items_wrap' => '%3$s',
		'depth' => 1,
		'walker' => new Chroma_Mobile_Nav_Walker(),
	));
}

/**
 * Mobile Nav Fallback
 */
function chroma_mobile_nav_fallback()
{
	$program_slug = chroma_get_program_base_slug();
	$pages = array($program_slug, "prismpath", "curriculum", "schedule", "locations", "faq");
	foreach ($pages as $slug) {
		echo '<a href="#' . esc_attr($slug) . '" class="block py-3 border-b border-brand-ink/5 text-lg font-semibold text-brand-ink hover:text-chroma-blue transition">' . esc_html(ucwords(str_replace('-', ' ', $slug))) . '</a>';
	}
}

/**
 * Custom Walker for Mobile Navigation
 */
class Chroma_Mobile_Nav_Walker extends Walker_Nav_Menu
{
	function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
	{
		$classes = 'block py-3 border-b border-brand-ink/5 text-lg font-semibold text-brand-ink hover:text-chroma-blue transition';

		if ($item->current) {
			$classes .= ' text-chroma-blue';
		}

		$output .= '<a href="' . esc_url($item->url) . '" class="' . esc_attr($classes) . '">';
		$output .= esc_html($item->title);
		$output .= '</a>';
	}
}