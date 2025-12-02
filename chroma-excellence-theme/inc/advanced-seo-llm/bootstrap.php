<?php
/**
 * Advanced SEO/LLM Module - Bootstrap
 * Loads all modules and registers meta boxes / hooks
 *
 * @package Chroma_Excellence
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Load Base Classes & Helpers
 */
require_once __DIR__ . '/class-meta-box-base.php';
require_once __DIR__ . '/class-field-sanitizer.php';
require_once __DIR__ . '/class-fallback-resolver.php';

/**
 * Load Core Classes
 */
require_once __DIR__ . '/meta-boxes/class-location-events.php';
require_once __DIR__ . '/meta-boxes/class-location-howto.php';
require_once __DIR__ . '/meta-boxes/class-location-llm-context.php';
require_once __DIR__ . '/meta-boxes/class-location-llm-prompt.php';
require_once __DIR__ . '/meta-boxes/class-location-media.php';
require_once __DIR__ . '/meta-boxes/class-location-pricing.php';
require_once __DIR__ . '/meta-boxes/class-location-reviews.php';
require_once __DIR__ . '/meta-boxes/class-location-service-area.php';
require_once __DIR__ . '/meta-boxes/class-program-relationships.php';
require_once __DIR__ . '/meta-boxes/class-universal-faq.php';
require_once __DIR__ . '/meta-boxes/class-hreflang-options.php';
require_once __DIR__ . '/meta-boxes/class-city-landing-meta.php';
require_once __DIR__ . '/meta-boxes/class-location-citation-facts.php';

/**
 * Load Core Classes (Missing Requires Added)
 */
require_once __DIR__ . '/class-seo-dashboard.php';
require_once __DIR__ . '/class-citation-datasets.php';
require_once __DIR__ . '/class-image-alt-automation.php';
require_once __DIR__ . '/class-admin-help.php';
require_once __DIR__ . '/class-breadcrumbs.php';

/**
 * Load Core Classes (Missing Requires Added)
 */
require_once __DIR__ . '/class-seo-dashboard.php';
require_once __DIR__ . '/class-citation-datasets.php';
require_once __DIR__ . '/class-image-alt-automation.php';
require_once __DIR__ . '/class-admin-help.php';
require_once __DIR__ . '/class-breadcrumbs.php';

/**
 * Load Endpoints
 */
require_once __DIR__ . '/endpoints/kml-endpoint.php';

/**
 * Load Schema Builders
 */
require_once __DIR__ . '/schema-builders/class-event-builder.php';
require_once __DIR__ . '/schema-builders/class-howto-builder.php';
require_once __DIR__ . '/schema-builders/class-llm-context-builder.php';
require_once __DIR__ . '/schema-builders/class-schema-injector.php';
require_once __DIR__ . '/schema-builders/class-service-area-builder.php';
require_once __DIR__ . '/schema-builders/class-universal-faq-builder.php';
require_once __DIR__ . '/schema-builders/class-page-type-builder.php';

/**
 * Initialize Modules
 */
function chroma_advanced_seo_init()
{
	// Core Modules
	(new Chroma_SEO_Dashboard())->init();
	(new Chroma_Citation_Datasets())->init();
	(new Chroma_Image_Alt_Automation())->init();
	(new Chroma_Image_Alt_Automation())->init();
	(new Chroma_Admin_Help())->init();
	(new Chroma_Breadcrumbs())->init();

	// Meta Boxes
	(new Chroma_Location_Citation_Facts())->register();
	(new Chroma_Location_Events())->register();
	(new Chroma_Location_HowTo())->register();
	(new Chroma_Location_LLM_Context())->register();
	(new Chroma_Location_LLM_Prompt())->register();
	(new Chroma_Location_Media())->register();
	(new Chroma_Location_Pricing())->register();
	(new Chroma_Location_Reviews())->register();
	(new Chroma_Location_Service_Area())->register();
	(new Chroma_Program_Relationships())->register();
	(new Chroma_Universal_FAQ())->register();
	(new Chroma_Hreflang_Options())->register();
	(new Chroma_City_Landing_Meta())->register();

	// Schema Builders (Hooks)
	add_action('wp_head', ['Chroma_Event_Schema_Builder', 'output']);
	add_action('wp_head', ['Chroma_HowTo_Schema_Builder', 'output']);
	add_action('wp_head', ['Chroma_Schema_Injector', 'output_person_schema']);
	add_action('wp_head', ['Chroma_Universal_FAQ_Builder', 'output']);
	add_action('wp_head', ['Chroma_Page_Type_Builder', 'output']);
}
add_action('init', 'chroma_advanced_seo_init');

/**
 * Admin Assets
 */
function chroma_advanced_seo_admin_assets($hook)
{
	global $post;

	if (!$post || !in_array($post->post_type, ['location', 'program', 'page', 'post'])) {
		return;
	}

	// Inline CSS for Meta Boxes
	?>
	<style>
		.chroma-advanced-seo-meta-box {
			padding: 10px 0;
		}

		.chroma-field-wrapper {
			margin-bottom: 20px;
		}

		.chroma-field-wrapper label {
			display: block;
			font-weight: 600;
			margin-bottom: 5px;
		}

		.chroma-field-wrapper .description {
			margin-top: 5px;
			font-style: normal;
			color: #666;
		}

		.chroma-field-wrapper .fallback-notice {
			color: #2271b1;
			font-style: italic;
		}

		.chroma-repeater-field {
			border: 1px solid #ddd;
			padding: 15px;
			background: #f9f9f9;
		}

		.chroma-repeater-items {
			margin-bottom: 10px;
		}

		.chroma-repeater-item {
			display: flex;
			gap: 10px;
			margin-bottom: 8px;
			align-items: center;
		}

		.chroma-repeater-item input {
			flex: 1;
		}

		.chroma-remove-item {
			color: #b32d2e;
		}
	</style>
	<script>
		jQuery(document).ready(function ($) {
			// Repeater Add
			$(document).on('click', '.chroma-add-item', function (e) {
				e.preventDefault();
				var $wrapper = $(this).closest('.chroma-repeater-field');
				var $items = $wrapper.find('.chroma-repeater-items');
				var $clone = $items.find('.chroma-repeater-item').first().clone();
				$clone.find('input, textarea').val('');
				$items.append($clone);
			});

			// Repeater Remove
			$(document).on('click', '.chroma-remove-item', function (e) {
				e.preventDefault();
				if ($(this).closest('.chroma-repeater-items').find('.chroma-repeater-item').length > 1) {
					$(this).closest('.chroma-repeater-item').remove();
				} else {
					$(this).closest('.chroma-repeater-item').find('input, textarea').val('');
				}
			});
		});
	</script>
	<?php
}
add_action('admin_enqueue_scripts', 'chroma_advanced_seo_admin_assets');