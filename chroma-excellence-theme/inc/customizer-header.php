<?php
/**
 * Header Customizer Settings
 *
 * @package Chroma_Excellence
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Register Header Customizer Settings
 */
function chroma_header_customizer_settings($wp_customize)
{

	// Add Header Section
	$wp_customize->add_section('chroma_header_settings', array(
		'title' => __('Header Settings', 'chroma-excellence'),
		'priority' => 30,
	));

	// Header Text
	$wp_customize->add_setting('chroma_header_text', array(
		'default' => "Early Learning\nAcademy",
		'sanitize_callback' => 'sanitize_textarea_field',
		'transport' => 'refresh',
	));

	$wp_customize->add_control('chroma_header_text', array(
		'label' => __('Header Text', 'chroma-excellence'),
		'description' => __('Enter text to display next to the logo. First line will be bold and larger. Additional lines will be small, uppercase, and blue.', 'chroma-excellence'),
		'section' => 'chroma_header_settings',
		'type' => 'textarea',
		'input_attrs' => array(
			'placeholder' => "Early Learning\nAcademy",
			'rows' => 3,
		),
	));

	// Book a Tour Button URL
	$wp_customize->add_setting('chroma_book_tour_url', array(
		'default' => home_url('/contact#tour'),
		'sanitize_callback' => 'esc_url_raw',
		'transport' => 'refresh',
	));

	$wp_customize->add_control('chroma_book_tour_url', array(
		'label' => __('Book a Tour Button URL', 'chroma-excellence'),
		'description' => __('Enter the URL for the "Book a Tour" button in the header.', 'chroma-excellence'),
		'section' => 'chroma_header_settings',
		'type' => 'url',
		'input_attrs' => array(
			'placeholder' => home_url('/contact#tour'),
		),
	));

	// Header Scripts
	$wp_customize->add_setting('chroma_header_scripts', array(
		'default' => '',
		'sanitize_callback' => 'chroma_sanitize_raw_html',
		'transport' => 'refresh',
	));

	$wp_customize->add_control('chroma_header_scripts', array(
		'label' => __('Header Scripts', 'chroma-excellence'),
		'description' => __('Scripts to be output before </head>.', 'chroma-excellence'),
		'section' => 'chroma_header_settings',
		'type' => 'textarea',
	));

}
add_action('customize_register', 'chroma_header_customizer_settings');
