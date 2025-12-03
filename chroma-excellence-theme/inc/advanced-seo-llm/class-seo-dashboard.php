<?php
/**
 * Advanced SEO/LLM Dashboard
 * Provides a centralized view of all SEO data
 * Shows manual values vs. fallback values
 *
 * @package Chroma_Excellence
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Chroma_SEO_Dashboard
{
    /**
     * Initialize the dashboard
     */
    public function init()
    {
        add_action('admin_menu', [$this, 'register_menu_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_chroma_fetch_schema_inspector', [$this, 'ajax_fetch_inspector_data']);
        add_action('wp_ajax_chroma_save_schema_inspector', [$this, 'ajax_save_inspector_data']);
    }

    /**
     * Register the menu page
     */
    public function register_menu_page()
    {
        add_menu_page(
            'SEO & LLM Data',              // Page title
            'SEO & LLM',                   // Menu title
            'edit_posts',                  // Capability
            'chroma-seo-dashboard',        // Menu slug
            [$this, 'render_page'],        // Callback
            'dashicons-chart-area',        // Icon
            80                             // Position
        );
    }

    /**
     * Enqueue assets
     */
    public function enqueue_assets($hook)
    {
        // Check if we are on the correct page
        if (!isset($_GET['page']) || $_GET['page'] !== 'chroma-seo-dashboard') {
            return;
        }

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-tabs');

        // Simple inline styles for the dashboard
        wp_add_inline_style('common', '
			.chroma-seo-table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 1px 1px rgba(0,0,0,0.04); }
			.chroma-seo-table th, .chroma-seo-table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e5e5; vertical-align: top; }
			.chroma-seo-table th { background: #f9f9f9; font-weight: 600; border-bottom: 2px solid #ddd; }
			.chroma-seo-table tr:hover { background: #fbfbfb; }
			.chroma-value-manual { color: #2271b1; font-weight: 500; }
			.chroma-value-fallback { color: #646970; font-style: italic; }
			.chroma-badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 11px; margin-right: 4px; }
			.chroma-badge-manual { background: #e6f6e6; color: #006600; border: 1px solid #b3e6b3; }
			.chroma-badge-auto { background: #f0f0f1; color: #646970; border: 1px solid #dcdcde; }
			.chroma-status-icon { font-size: 16px; margin-right: 5px; }
			.chroma-check { color: #00a32a; }
			.chroma-cross { color: #d63638; }
            
            /* Inspector Styles */
            .chroma-inspector-controls { background: #fff; padding: 20px; border: 1px solid #ccd0d4; margin-bottom: 20px; display: flex; gap: 20px; align-items: center; }
            .chroma-inspector-table input[type="text"], .chroma-inspector-table textarea { width: 100%; }
            .chroma-inspector-row.modified { background-color: #f0f6fc; }
		');
    }

    /**
     * Render the dashboard page
     */
    public function render_page()
    {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'locations';
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">SEO & LLM Data Dashboard</h1>

            <nav class="nav-tab-wrapper">
                <a href="<?php echo admin_url('admin.php?page=chroma-seo-dashboard&tab=locations'); ?>"
                    class="nav-tab <?php echo $active_tab === 'locations' ? 'nav-tab-active' : ''; ?>">Locations</a>
                <a href="<?php echo admin_url('admin.php?page=chroma-seo-dashboard&tab=programs'); ?>"
                    class="nav-tab <?php echo $active_tab === 'programs' ? 'nav-tab-active' : ''; ?>">Programs</a>
                <a href="<?php echo admin_url('admin.php?page=chroma-seo-dashboard&tab=pages'); ?>"
                    class="nav-tab <?php echo $active_tab === 'pages' ? 'nav-tab-active' : ''; ?>">Pages</a>
                <a href="<?php echo admin_url('admin.php?page=chroma-seo-dashboard&tab=cities'); ?>"
                    class="nav-tab <?php echo $active_tab === 'cities' ? 'nav-tab-active' : ''; ?>">Cities</a>
                <a href="<?php echo admin_url('admin.php?page=chroma-seo-dashboard&tab=posts'); ?>"
                    class="nav-tab <?php echo $active_tab === 'posts' ? 'nav-tab-active' : ''; ?>">Blog Posts</a>
                <a href="<?php echo admin_url('admin.php?page=chroma-seo-dashboard&tab=geo'); ?>"
                    class="nav-tab <?php echo $active_tab === 'geo' ? 'nav-tab-active' : ''; ?>">GEO Settings</a>
                <a href="<?php echo admin_url('admin.php?page=chroma-seo-dashboard&tab=inspector'); ?>"
                    class="nav-tab <?php echo $active_tab === 'inspector' ? 'nav-tab-active' : ''; ?>">Inspector</a>
                <a href="<?php echo admin_url('admin.php?page=chroma-seo-dashboard&tab=breadcrumbs'); ?>"
                    class="nav-tab <?php echo $active_tab === 'breadcrumbs' ? 'nav-tab-active' : ''; ?>">Breadcrumbs</a>
            </nav>

            <br>

            <?php
            switch ($active_tab) {
                case 'locations':
                    $this->render_overview_tab('location');
                    break;
                case 'programs':
                    $this->render_overview_tab('program');
                    break;
                case 'pages':
                    $this->render_overview_tab('page');
                    break;
                case 'cities':
                    $this->render_overview_tab('city');
                    break;
                case 'posts':
                    $this->render_overview_tab('post');
                    break;
                case 'geo':
                    $this->render_geo_tab();
                    break;
                case 'inspector':
                    $this->render_inspector_tab();
                    break;
                case 'breadcrumbs':
                    if (class_exists('Chroma_Breadcrumbs')) {
                        (new Chroma_Breadcrumbs())->render_settings();
                    } else {
                        echo '<p>Breadcrumbs module not loaded.</p>';
                    }
                    break;
                default:
                    $this->render_overview_tab('location');
                    break;
            }
            ?>
        </div>
        <?php
    }

    /**
     * Render GEO Tab
     */
    private function render_geo_tab()
    {
        ?>
        <div class="chroma-seo-card">
            <h2>🌍 Geo-Optimization Settings</h2>
            <p>Manage your location-based SEO settings.</p>

            <div class="chroma-doc-section" style="margin-top: 20px;">
                <h3>KML File</h3>
                <p>Your KML file is automatically generated and available at:</p>
                <code><a href="<?php echo home_url('/locations.kml'); ?>" target="_blank"><?php echo home_url('/locations.kml'); ?></a></code>
                <p class="description">Submit this URL to Google Earth and other geo-directories.</p>
            </div>

            <div class="chroma-doc-section" style="margin-top: 20px;">
                <h3>Service Area Defaults</h3>
                <p>If a location does not have specific coordinates set, the system will attempt to geocode the address
                    automatically.</p>
                <p>Default Radius: <strong>10 miles</strong></p>
            </div>
        </div>
        <?php
    }

    /**
     * Render Overview Tab (Generic)
     */
    private function render_overview_tab($post_type)
    {
        $args = [
            'post_type' => $post_type,
            'posts_per_page' => 50,
            'orderby' => 'title',
            'order' => 'ASC',
        ];
        $posts = get_posts($args);
        $type_obj = get_post_type_object($post_type);
        ?>
        <p class="description">
            Overview of SEO/LLM data for <strong><?php echo esc_html($type_obj->labels->name); ?></strong>.
            <span class="chroma-badge chroma-badge-manual">Manual</span> values are set by you.
            <span class="chroma-badge chroma-badge-auto">Auto</span> values are generated by the system fallbacks.
        </p>
        <br>
        <table class="chroma-seo-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 250px;">Title</th>
                    <th>LLM Context</th>
                    <th>LLM Prompt</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $p):
                    if (!$p || !is_a($p, 'WP_Post'))
                        continue;
                    $id = $p->ID;
                    // LLM Context
                    $intent_manual = get_post_meta($id, 'seo_llm_primary_intent', true);
                    $desc = Chroma_Fallback_Resolver::get_llm_description($id);
                    // LLM Prompt
                    $prompt_manual = get_post_meta($id, 'seo_llm_custom_prompt', true);
                    ?>
                    <tr>
                        <td>
                            <strong><a
                                    href="<?php echo admin_url('post.php?post=' . $id . '&action=edit'); ?>"><?php echo esc_html($p->post_title); ?></a></strong>
                            <?php if ($post_type === 'location'): ?>
                                <br><small><?php echo get_post_meta($id, 'location_city', true); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="margin-bottom: 6px;">
                                <strong>Intent:</strong>
                                <?php if ($intent_manual): ?>
                                    <span class="chroma-value-manual"><?php echo esc_html($intent_manual); ?></span>
                                <?php else: ?>
                                    <span class="chroma-value-fallback">Auto-Generated</span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <strong>Description:</strong>
                                <div style="font-size: 11px; line-height: 1.4;"><?php echo wp_trim_words($desc, 15); ?></div>
                            </div>
                        </td>
                        <td>
                            <?php if ($prompt_manual): ?>
                                <span class="chroma-check">✓</span> Custom Prompt Set
                            <?php else: ?>
                                <span style="color: #ccc;">-</span> Default
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?page=chroma-seo-dashboard&tab=inspector&post_id=<?php echo $id; ?>"
                                class="button button-small">Inspect</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }

    /**
     * Render Inspector Tab
     */
    private function render_inspector_tab()
    {
        $locations = get_posts(['post_type' => 'location', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
        $programs = get_posts(['post_type' => 'program', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
        $pages = get_posts(['post_type' => 'page', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
        $posts = get_posts(['post_type' => 'post', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);

        $selected_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
        ?>
        <div class="chroma-inspector-controls">
            <label><strong>Select Page to Inspect:</strong></label>
            <select id="chroma-inspector-select" style="min-width: 300px;">
                <option value="">-- Select a Page --</option>
                <optgroup label="Locations">
                    <?php foreach ($locations as $loc):
                        if (!$loc || !is_a($loc, 'WP_Post'))
                            continue; ?>
                        <option value="<?php echo $loc->ID; ?>" <?php selected($selected_id, $loc->ID); ?>>
                            <?php echo esc_html($loc->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="Programs">
                    <?php foreach ($programs as $prog):
                        if (!$prog || !is_a($prog, 'WP_Post'))
                            continue; ?>
                        <option value="<?php echo $prog->ID; ?>" <?php selected($selected_id, $prog->ID); ?>>
                            <?php echo esc_html($prog->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="Pages">
                    <?php foreach ($pages as $pg):
                        if (!$pg || !is_a($pg, 'WP_Post'))
                            continue; ?>
                        <option value="<?php echo $pg->ID; ?>" <?php selected($selected_id, $pg->ID); ?>>
                            <?php echo esc_html($pg->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="Cities">
                    <?php
                    $cities = get_posts(['post_type' => 'city', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
                    foreach ($cities as $city):
                        if (!$city || !is_a($city, 'WP_Post'))
                            continue; ?>
                        <option value="<?php echo $city->ID; ?>" <?php selected($selected_id, $city->ID); ?>>
                            <?php echo esc_html($city->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="Blog Posts">
                    <?php foreach ($posts as $pt):
                        if (!$pt || !is_a($pt, 'WP_Post'))
                            continue; ?>
                        <option value="<?php echo $pt->ID; ?>" <?php selected($selected_id, $pt->ID); ?>>
                            <?php echo esc_html($pt->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
            <span class="spinner" id="chroma-inspector-spinner"></span>
        </div>

        <div id="chroma-inspector-content">
            <p class="description">Select a page above to view and edit its Schema/SEO data.</p>
        </div>

        <script>
            jQuery(document).ready(function ($) {
                var chroma_nonce = '<?php echo wp_create_nonce('chroma_seo_dashboard_nonce'); ?>';
                var selectedId = '<?php echo $selected_id; ?>';
                if (selectedId && selectedId != '0') {
                    loadInspectorData(selectedId);
                }

                $('#chroma-inspector-select').on('change', function () {
                    var id = $(this).val();
                    if (id) loadInspectorData(id);
                });

                function loadInspectorData(id) {
                    $('#chroma-inspector-spinner').addClass('is-active');
                    $.post(ajaxurl, {
                        action: 'chroma_fetch_schema_inspector',
                        nonce: chroma_nonce,
                        post_id: id
                    }, function (response) {
                        $('#chroma-inspector-spinner').removeClass('is-active');
                        if (response.success) {
                            $('#chroma-inspector-content').html(response.data.html);
                        } else {
                            alert('Error loading data');
                        }
                    });
                }

                // Save Handler
                $(document).on('click', '#chroma-inspector-save', function (e) {
                    e.preventDefault();
                    var btn = $(this);
                    btn.prop('disabled', true).text('Saving...');

                    var data = {
                        action: 'chroma_save_schema_inspector',
                        nonce: chroma_nonce,
                        post_id: $('#chroma-inspector-post-id').val(),
                        fields: {}
                    };

                    $('.chroma-inspector-field-row').each(function () {
                        var key = $(this).data('key');
                        var useFallback = $(this).find('.use-fallback-check').is(':checked');
                        var value = $(this).find('.manual-value-input').val();

                        // If "Use Fallback" is checked, we send empty string to delete meta
                        // If unchecked, we send the manual value
                        data.fields[key] = useFallback ? '' : value;
                    });

                    $.post(ajaxurl, data, function (response) {
                        btn.prop('disabled', false).text('Update Schema Settings');
                        if (response.success) {
                            alert('Settings saved successfully!');
                            loadInspectorData(data.post_id); // Reload to refresh
                        } else {
                            alert('Error saving settings.');
                        }
                    });
                });

                // Toggle Handler
                $(document).on('change', '.use-fallback-check', function () {
                    var row = $(this).closest('tr');
                    var input = row.find('.manual-value-input');
                    if ($(this).is(':checked')) {
                        input.prop('disabled', true).addClass('disabled');
                        row.removeClass('modified');
                    } else {
                        input.prop('disabled', false).removeClass('disabled');
                        row.addClass('modified');
                    }
                });
            });
        </script>
        <?php
    }

    /**
     * AJAX: Fetch Inspector Data
     */
    public function ajax_fetch_inspector_data()
    {
        check_ajax_referer('chroma_seo_dashboard_nonce', 'nonce');

        $post_id = intval($_POST['post_id']);
        if (!$post_id)
            wp_send_json_error();

        // Define fields based on post type
        $post_type = get_post_type($post_id);
        $fields = [];

        // Common Fields
        $fields['seo_llm_primary_intent'] = ['label' => 'Primary Intent', 'fallback' => 'Auto-Detected'];
        $fields['seo_llm_custom_prompt'] = ['label' => 'Custom LLM Prompt', 'fallback' => ''];

        if ($post_type === 'location') {
            $fields += [
                'schema_loc_name' => ['label' => 'Schema Name', 'fallback' => get_post_field('post_title', $post_id)],
                'schema_loc_description' => ['label' => 'Description', 'fallback' => get_post_field('post_excerpt', $post_id)],
                'schema_loc_telephone' => ['label' => 'Telephone', 'fallback' => get_post_meta($post_id, 'location_phone', true)],
                'schema_loc_email' => ['label' => 'Email', 'fallback' => get_post_meta($post_id, 'location_email', true)],
                'schema_loc_price_range' => ['label' => 'Price Range (String)', 'fallback' => '$$'],
                'seo_llm_service_area_lat' => ['label' => 'Service Area Lat', 'fallback' => get_post_meta($post_id, 'location_latitude', true)],
                'seo_llm_service_area_lng' => ['label' => 'Service Area Lng', 'fallback' => get_post_meta($post_id, 'location_longitude', true)],
                'seo_llm_service_area_radius' => ['label' => 'Service Radius (mi)', 'fallback' => '10'],
            ];
        } elseif ($post_type === 'program') {
            $fields += [
                'schema_prog_name' => ['label' => 'Schema Name', 'fallback' => get_post_field('post_title', $post_id)],
                'schema_prog_description' => ['label' => 'Description', 'fallback' => get_post_field('post_excerpt', $post_id)],
                'schema_prog_service_type' => ['label' => 'Service Type', 'fallback' => 'Early Childhood Education'],
                'program_age_range' => ['label' => 'Age Range', 'fallback' => ''],
            ];
        } else {
            // General Pages/Posts
            $fields += [
                'schema_page_type' => ['label' => 'Schema Type', 'fallback' => 'WebPage'],
                'schema_description' => ['label' => 'Schema Description', 'fallback' => get_post_field('post_excerpt', $post_id)],
            ];
        }

        ob_start();
        ?>
        <input type="hidden" id="chroma-inspector-post-id" value="<?php echo $post_id; ?>">
        <table class="chroma-seo-table chroma-inspector-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 200px;">Field</th>
                    <th>Fallback / Auto Value</th>
                    <th style="width: 50px;">Use Auto</th>
                    <th>Manual Override</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fields as $key => $config):
                    $manual_value = get_post_meta($post_id, $key, true);
                    $has_manual = !empty($manual_value);
                    ?>
                    <tr class="chroma-inspector-field-row <?php echo $has_manual ? 'modified' : ''; ?>"
                        data-key="<?php echo esc_attr($key); ?>">
                        <td><strong><?php echo esc_html($config['label']); ?></strong><br><small
                                style="color:#999"><?php echo esc_html($key); ?></small></td>
                        <td>
                            <div class="chroma-value-fallback"><?php echo esc_html($config['fallback'] ?: '(Empty)'); ?></div>
                        </td>
                        <td style="text-align: center;">
                            <input type="checkbox" class="use-fallback-check" <?php checked(!$has_manual); ?>>
                        </td>
                        <td>
                            <input type="text" class="manual-value-input" value="<?php echo esc_attr($manual_value); ?>" <?php disabled(!$has_manual); ?> placeholder="Enter custom value...">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="submit">
            <button id="chroma-inspector-save" class="button button-primary button-large">Update Schema Settings</button>
        </p>
        <?php
        $html = ob_get_clean();
        wp_send_json_success(['html' => $html]);
    }

    /**
     * AJAX: Save Inspector Data
     */
    public function ajax_save_inspector_data()
    {
        check_ajax_referer('chroma_seo_dashboard_nonce', 'nonce');

        if (!current_user_can('edit_posts'))
            wp_send_json_error();

        $post_id = intval($_POST['post_id']);
        $fields = $_POST['fields'];

        if (!$post_id || !is_array($fields))
            wp_send_json_error();

        foreach ($fields as $key => $value) {
            if ($value === '') {
                delete_post_meta($post_id, $key);
            } else {
                update_post_meta($post_id, $key, sanitize_text_field($value));
            }
        }

        wp_send_json_success();
    }
}
