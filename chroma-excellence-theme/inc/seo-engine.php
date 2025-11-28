<?php
/**
 * SEO Engine: Schema, Sitemap, Robots.txt, Meta Tags
 *
 * @package Chroma_Excellence
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
        exit;
}

/**
 * Add Organization Schema to Homepage
 */
function chroma_organization_schema()
{
        if (!is_front_page()) {
                return;
        }

        $homepage_id = get_option('page_on_front');

        // Get custom values or fallbacks
        $name = get_post_meta($homepage_id, 'schema_org_name', true) ?: get_bloginfo('name');
        $url = get_post_meta($homepage_id, 'schema_org_url', true) ?: home_url();
        $logo = get_post_meta($homepage_id, 'schema_org_logo', true) ?: chroma_get_global_setting('global_logo', '');
        $description = get_post_meta($homepage_id, 'schema_org_description', true) ?: chroma_global_seo_default_description();
        $area_served = get_post_meta($homepage_id, 'schema_org_area_served', true) ?: 'Atlanta';
        $telephone = get_post_meta($homepage_id, 'schema_org_telephone', true);
        $email = get_post_meta($homepage_id, 'schema_org_email', true);

        $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'ChildCare',
                'name' => $name,
                'url' => $url,
                'logo' => $logo,
                'description' => $description,
                'areaServed' => array(
                        '@type' => 'City',
                        'name' => $area_served,
                ),
                'sameAs' => array_filter(array(
                        chroma_global_facebook_url(),
                        chroma_global_instagram_url(),
                        chroma_global_linkedin_url(),
                )),
        );

        // Add optional fields if provided
        if ($telephone) {
                $schema['telephone'] = $telephone;
        }
        if ($email) {
                $schema['email'] = $email;
        }

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'chroma_organization_schema');

/**
 * Add WebSite Schema to Homepage (for Sitelinks Search Box)
 */
function chroma_website_schema()
{
        if (!is_front_page()) {
                return;
        }

        $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => get_bloginfo('name'),
                'url' => home_url(),
                'potentialAction' => array(
                        '@type' => 'SearchAction',
                        'target' => home_url('/?s={search_term_string}'),
                        'query-input' => 'required name=search_term_string',
                ),
        );

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'chroma_website_schema');

/**
 * Add LocalBusiness Schema to Location Pages
 */
function chroma_location_schema()
{
        if (!is_singular('location')) {
                return;
        }

        $location_id = get_the_ID();
        $location_fields = chroma_get_location_fields($location_id);

        // Get custom values or fallbacks
        $name = get_post_meta($location_id, 'schema_loc_name', true) ?: get_the_title();
        $description = get_post_meta($location_id, 'schema_loc_description', true) ?: (get_the_excerpt() ?: chroma_trimmed_excerpt(30, $location_id));
        $telephone = get_post_meta($location_id, 'schema_loc_telephone', true) ?: $location_fields['phone'];
        $email = get_post_meta($location_id, 'schema_loc_email', true) ?: $location_fields['email'];
        $price_range = get_post_meta($location_id, 'schema_loc_price_range', true);
        $opening_hours = get_post_meta($location_id, 'schema_loc_opening_hours', true);
        $payment = get_post_meta($location_id, 'schema_loc_payment_accepted', true);

        $schema = array(
                '@context' => 'https://schema.org',
                '@type' => array('ChildCare', 'LocalBusiness'),
                'name' => $name,
                'description' => $description,
                'url' => get_permalink(),
                'image' => get_the_post_thumbnail_url($location_id, 'full'),
                'address' => array(
                        '@type' => 'PostalAddress',
                        'streetAddress' => $location_fields['address'],
                        'addressLocality' => $location_fields['city'],
                        'addressRegion' => $location_fields['state'],
                        'postalCode' => $location_fields['zip'],
                ),
                'telephone' => $telephone,
                'email' => $email,
        );

        if ($location_fields['latitude'] && $location_fields['longitude']) {
                $schema['geo'] = array(
                        '@type' => 'GeoCoordinates',
                        'latitude' => $location_fields['latitude'],
                        'longitude' => $location_fields['longitude'],
                );
        }

        // Add optional fields if provided
        if ($price_range) {
                $schema['priceRange'] = $price_range;
        }
        if ($opening_hours) {
                $schema['openingHours'] = explode("\n", $opening_hours);
        }
        if ($payment) {
                $schema['paymentAccepted'] = $payment;
        }

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'chroma_location_schema');

/**
 * Add Service Schema to Program Pages
 */
function chroma_program_schema()
{
        if (!is_singular('program')) {
                return;
        }

        $program_id = get_the_ID();

        // Get custom values or fallbacks
        $name = get_post_meta($program_id, 'schema_prog_name', true) ?: get_the_title();
        $description = get_post_meta($program_id, 'schema_prog_description', true) ?: (get_the_excerpt() ?: chroma_trimmed_excerpt(30, $program_id));
        $service_type = get_post_meta($program_id, 'schema_prog_service_type', true) ?: 'Early Childhood Education';
        $provider_name = get_post_meta($program_id, 'schema_prog_provider_name', true) ?: get_bloginfo('name');
        $area_served = get_post_meta($program_id, 'schema_prog_area_served', true) ?: 'Metro Atlanta';
        $category = get_post_meta($program_id, 'schema_prog_category', true);

        $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'Service',
                'name' => $name,
                'description' => $description,
                'url' => get_permalink(),
                'provider' => array(
                        '@type' => 'Organization',
                        'name' => $provider_name,
                ),
                'serviceType' => $service_type,
                'areaServed' => $area_served,
        );

        // Add optional category if provided
        if ($category) {
                $schema['category'] = $category;
        }

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'chroma_program_schema');

/**
 * Add FAQPage Schema to Homepage (when FAQ section exists)
 */
function chroma_faq_schema()
{
        if (!is_front_page()) {
                return;
        }

        // Check if FAQ data exists
        if (!function_exists('chroma_home_has_faq') || !chroma_home_has_faq()) {
                return;
        }

        if (!function_exists('chroma_home_faq')) {
                return;
        }

        $faq_data = chroma_home_faq();
        if (empty($faq_data['items'])) {
                return;
        }

        // Build FAQ schema
        $main_entity = array();
        foreach ($faq_data['items'] as $item) {
                if (empty($item['question']) || empty($item['answer'])) {
                        continue;
                }

                $main_entity[] = array(
                        '@type' => 'Question',
                        'name' => $item['question'],
                        'acceptedAnswer' => array(
                                '@type' => 'Answer',
                                'text' => wp_strip_all_tags($item['answer']),
                        ),
                );
        }

        if (empty($main_entity)) {
                return;
        }

        $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $main_entity,
        );

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'chroma_faq_schema');

/**
 * Add BreadcrumbList Schema for non-homepage pages
 */
function chroma_breadcrumb_schema()
{
        // Skip homepage
        if (is_front_page()) {
                return;
        }

        $items = array();
        $position = 1;

        // Always start with Home
        $items[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => 'Home',
                'item' => home_url('/'),
        );

        // Add breadcrumb items based on page type
        if (is_singular('program')) {
                // Programs: Home > Programs > Program Name
                $items[] = array(
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => 'Programs',
                        'item' => get_post_type_archive_link('program'),
                );
                $items[] = array(
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => get_the_title(),
                        'item' => get_permalink(),
                );
        } elseif (is_singular('location')) {
                // Locations: Home > Locations > Location Name
                $items[] = array(
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => 'Locations',
                        'item' => get_post_type_archive_link('location') ?: home_url('/locations/'),
                );
                $items[] = array(
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => get_the_title(),
                        'item' => get_permalink(),
                );
        } elseif (is_singular('post')) {
                // Blog posts: Home > Stories > Post Name
                $items[] = array(
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => 'Stories',
                        'item' => home_url('/stories/'),
                );
                $items[] = array(
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => get_the_title(),
                        'item' => get_permalink(),
                );
        } elseif (is_singular('page')) {
                // Regular pages: Home > Page Name
                $items[] = array(
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => get_the_title(),
                        'item' => get_permalink(),
                );
        } elseif (is_post_type_archive('program')) {
                // Programs archive
                $items[] = array(
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => 'Programs',
                        'item' => get_post_type_archive_link('program'),
                );
        } elseif (is_post_type_archive('location')) {
                // Locations archive
                $items[] = array(
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => 'Locations',
                        'item' => get_post_type_archive_link('location'),
                );
        }

        if (count($items) < 2) {
                return; // Need at least 2 items for breadcrumbs
        }

        $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $items,
        );

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'chroma_breadcrumb_schema');

/**
 * Open Graph Tags
 */
function chroma_og_tags()
{
        echo '<meta property="og:type" content="website" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '" />' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";

        if (has_post_thumbnail()) {
                echo '<meta property="og:image" content="' . esc_url(get_the_post_thumbnail_url(null, 'full')) . '" />' . "\n";
        }

        $description = get_the_excerpt() ?: chroma_global_seo_default_description();
        echo '<meta property="og:description" content="' . esc_attr(wp_strip_all_tags($description)) . '" />' . "\n";
}
add_action('wp_head', 'chroma_og_tags', 5);

/**
 * Twitter Card Tags
 */
function chroma_twitter_cards()
{
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr(get_the_title()) . '" />' . "\n";

        if (has_post_thumbnail()) {
                echo '<meta name="twitter:image" content="' . esc_url(get_the_post_thumbnail_url(null, 'full')) . '" />' . "\n";
        }

        $description = get_the_excerpt() ?: chroma_global_seo_default_description();
        echo '<meta name="twitter:description" content="' . esc_attr(wp_strip_all_tags($description)) . '" />' . "\n";

        // Optional: Add Twitter site handle if available
        $twitter_handle = get_option('chroma_twitter_handle', '');
        if ($twitter_handle) {
                echo '<meta name="twitter:site" content="' . esc_attr($twitter_handle) . '" />' . "\n";
        }
}
add_action('wp_head', 'chroma_twitter_cards', 6);

/**
 * Hreflang Tags for EN/ES
 */
function chroma_hreflang_tags()
{
        $post_id = get_the_ID();

        if (!$post_id) {
                return;
        }

        $alternate_en = get_post_meta($post_id, 'alternate_url_en', true);
        $alternate_es = get_post_meta($post_id, 'alternate_url_es', true);

        if ($alternate_en) {
                echo '<link rel="alternate" hreflang="en" href="' . esc_url($alternate_en) . '" />' . "\n";
        }

        if ($alternate_es) {
                echo '<link rel="alternate" hreflang="es" href="' . esc_url($alternate_es) . '" />' . "\n";
        }
}
add_action('wp_head', 'chroma_hreflang_tags', 1);

/**
 * Shared meta description output with fallbacks
 */
/**
 * Shared meta description output with fallbacks
 */
function chroma_shared_meta_description()
{
        // 1. Manual Override (General SEO Meta Box)
        $post_id = get_the_ID();
        $manual_description = $post_id ? get_post_meta($post_id, 'meta_description', true) : '';

        if (!empty($manual_description)) {
                echo '<meta name="description" content="' . esc_attr(wp_strip_all_tags($manual_description)) . '" />' . "\n";
                return;
        }

        // 2. Dynamic Templates
        $description = '';

        if (is_singular('location')) {
                // Location Template: "Visit our [Location Name] campus in [City], [State]. [Tagline]. Serving families in [Service Areas]. [Phone]."
                $city = get_post_meta($post_id, 'location_city', true);
                $state = get_post_meta($post_id, 'location_state', true);
                $tagline = get_post_meta($post_id, 'location_tagline', true);
                $service_areas = get_post_meta($post_id, 'location_service_areas', true);
                $phone = get_post_meta($post_id, 'location_phone', true);

                $parts = array();
                $parts[] = 'Visit our ' . get_the_title() . ' campus';
                if ($city && $state) {
                        $parts[] = "in $city, $state";
                }
                if ($tagline) {
                        $parts[] = ". $tagline";
                }
                if ($service_areas) {
                        $parts[] = ". Serving families in " . $service_areas;
                }
                if ($phone) {
                        $parts[] = ". Call us at $phone";
                }

                $description = implode('', $parts) . '.';

        } elseif (is_singular('program')) {
                // Program Template: "[Program Name] at Chroma Early Learning Academy. [Excerpt]."
                $excerpt = has_excerpt() ? get_the_excerpt() : chroma_trimmed_excerpt(20, $post_id);
                $description = get_the_title() . ' at Chroma Early Learning Academy. ' . $excerpt;

        } elseif (is_singular('post')) {
                // Blog Post Template: "[Title] - [Excerpt]"
                $excerpt = has_excerpt() ? get_the_excerpt() : chroma_trimmed_excerpt(30, $post_id);
                $description = get_the_title() . ' - ' . $excerpt;

        } elseif (is_front_page()) {
                // Homepage Template: Global Default > Tagline > Constructed Fallback
                $description = chroma_global_seo_default_description();
                if (empty($description)) {
                        $description = get_bloginfo('description');
                }
                if (empty($description)) {
                        $description = get_bloginfo('name') . ' offers premier child care, daycare, and early childhood education in the Metro Atlanta area.';
                }
        }

        // 3. Global Default / Fallback
        if (empty($description)) {
                echo "<!-- Debug: Description empty, trying global fallback -->\n";
                // Preserve About page specific metadata if defined (legacy support)
                if (function_exists('chroma_is_about_template') && function_exists('chroma_get_about_seo_fields') && chroma_is_about_template()) {
                        $about_fields = chroma_get_about_seo_fields();
                        if (!empty($about_fields['description'])) {
                                $description = $about_fields['description'];
                        }
                }

                if (empty($description) && $post_id) {
                        $description = has_excerpt($post_id) ? get_the_excerpt($post_id) : chroma_trimmed_excerpt(32, $post_id);
                }

                if (empty($description)) {
                        $description = chroma_global_seo_default_description();
                }
        }

        if ($description) {
                echo '<meta name="description" content="' . esc_attr(wp_strip_all_tags($description)) . '" />' . "\n";
        } else {
                echo "<!-- Debug: Final Description is EMPTY -->\n";
        }
}
add_action('wp_head', 'chroma_shared_meta_description', 2);

/**
 * Meta Keywords Output
 */
function chroma_meta_keywords()
{
        // 1. Manual Override
        $post_id = get_the_ID();
        $manual_keywords = $post_id ? get_post_meta($post_id, 'meta_keywords', true) : '';

        if (!empty($manual_keywords)) {
                echo '<meta name="keywords" content="' . esc_attr(wp_strip_all_tags($manual_keywords)) . '" />' . "\n";
                return;
        }

        // 2. CSV-based Keyword Mapping
        $keywords = array();
        $keyword_map_file = get_template_directory() . '/inc/seo-keywords-data.php';

        if (file_exists($keyword_map_file)) {
                $keyword_map = include $keyword_map_file;
        } else {
                $keyword_map = array();
        }

        $slug = $post_id ? get_post_field('post_name', $post_id) : '';

        // Handle Homepage case
        if (is_front_page() || is_home()) {
                $slug = 'home';
        }

        if (!empty($slug) && isset($keyword_map[$slug]) && is_array($keyword_map[$slug])) {
                $keywords = array_merge($keywords, $keyword_map[$slug]);
        }

        // 3. Dynamic Templates (Fallback/Addition)
        if (is_singular('location')) {
                $city = get_post_meta($post_id, 'location_city', true);
                $service_areas = get_post_meta($post_id, 'location_service_areas', true);
                if (empty($keywords)) {
                        $keywords[] = get_the_title();
                }

                if ($city) {
                        $keywords[] = "child care $city";
                        $keywords[] = "daycare $city";
                        $keywords[] = "preschool $city";
                }
                if ($service_areas) {
                        // Split service areas by comma if it's a list
                        $areas = explode(',', $service_areas);
                        foreach ($areas as $area) {
                                $keywords[] = trim($area);
                        }
                }

        } elseif (is_singular('program')) {
                if (empty($keywords)) {
                        $keywords[] = get_the_title();
                }
                $keywords[] = 'early childhood education';
                $keywords[] = 'curriculum';
                $keywords[] = 'child development';

        } else {
                // Global defaults if still empty
                if (empty($keywords)) {
                        $keywords[] = 'child care';
                        $keywords[] = 'daycare';
                        $keywords[] = 'preschool';
                        $keywords[] = 'early learning';
                        $keywords[] = 'Atlanta';
                }
        }

        // Deduplicate and output
        $keywords = array_unique($keywords);

        if (!empty($keywords)) {
                echo '<meta name="keywords" content="' . esc_attr(implode(', ', $keywords)) . '" />' . "\n";
        }
}
add_action('wp_head', 'chroma_meta_keywords', 3);

/**
 * Custom Sitemap.xml
 */
function chroma_custom_sitemap()
{
        if (!(isset($_GET['sitemap']) && 'xml' === $_GET['sitemap'])) {
                return;
        }

        header('Content-Type: application/xml; charset=utf-8');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Homepage.
        echo '<url>' . "\n";
        echo '  <loc>' . esc_url(home_url('/')) . '</loc>' . "\n";
        echo '  <lastmod>' . date('c') . '</lastmod>' . "\n";
        echo '  <changefreq>daily</changefreq>' . "\n";
        echo '  <priority>1.0</priority>' . "\n";
        echo '</url>' . "\n";

        // Pages.
        $pages = get_posts(
                array(
                        'post_type' => 'page',
                        'posts_per_page' => -1,
                )
        );
        foreach ($pages as $page) {
                echo '<url>' . "\n";
                echo '  <loc>' . esc_url(get_permalink($page->ID)) . '</loc>' . "\n";
                echo '  <lastmod>' . get_the_modified_date('c', $page->ID) . '</lastmod>' . "\n";
                echo '  <changefreq>weekly</changefreq>' . "\n";
                echo '  <priority>0.8</priority>' . "\n";
                echo '</url>' . "\n";
        }

        // Programs.
        $programs = get_posts(
                array(
                        'post_type' => 'program',
                        'posts_per_page' => -1,
                )
        );
        foreach ($programs as $program) {
                echo '<url>' . "\n";
                echo '  <loc>' . esc_url(get_permalink($program->ID)) . '</loc>' . "\n";
                echo '  <lastmod>' . get_the_modified_date('c', $program->ID) . '</lastmod>' . "\n";
                echo '  <changefreq>monthly</changefreq>' . "\n";
                echo '  <priority>0.9</priority>' . "\n";
                echo '</url>' . "\n";
        }

        // Locations.
        $locations = get_posts(
                array(
                        'post_type' => 'location',
                        'posts_per_page' => -1,
                )
        );
        foreach ($locations as $location) {
                echo '<url>' . "\n";
                echo '  <loc>' . esc_url(get_permalink($location->ID)) . '</loc>' . "\n";
                echo '  <lastmod>' . get_the_modified_date('c', $location->ID) . '</lastmod>' . "\n";
                echo '  <changefreq>monthly</changefreq>' . "\n";
                echo '  <priority>0.9</priority>' . "\n";
                echo '</url>' . "\n";
        }

        echo '</urlset>';
        exit;
}
add_action('template_redirect', 'chroma_custom_sitemap');

/**
 * Custom Robots.txt
 */
function chroma_custom_robots_txt($output)
{
        $output .= 'Sitemap: ' . home_url('/?sitemap=xml') . "\n";
        return $output;
}
add_filter('robots_txt', 'chroma_custom_robots_txt');
