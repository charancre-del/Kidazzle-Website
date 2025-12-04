<?php
/**
 * LLM Client
 * Handles communication with OpenAI API
 *
 * @package Chroma_Excellence
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Chroma_LLM_Client
{
    private $api_key;
    private $model;
    private $base_url;

    public function __construct()
    {
        $this->api_key = get_option('chroma_openai_api_key', '');
        $this->model = get_option('chroma_llm_model', 'gpt-4o-mini');
        $this->base_url = get_option('chroma_llm_base_url', 'https://api.openai.com/v1');

        // Register AJAX actions for saving key and testing connection
        add_action('wp_ajax_chroma_save_llm_settings', [$this, 'ajax_save_settings']);
        add_action('wp_ajax_chroma_test_llm_connection', [$this, 'ajax_test_connection']);
        add_action('wp_ajax_chroma_generate_schema', [$this, 'ajax_generate_schema']);
        add_action('wp_ajax_chroma_generate_llm_targeting', [$this, 'ajax_generate_llm_targeting']);
    }

    /**
     * Render Settings Section
     */
    public function render_settings()
    {
        $key = $this->api_key;
        $model = $this->model;
        $base_url = $this->base_url;
        ?>
        <div class="chroma-seo-card">
            <h2>🤖 AI Integration Settings</h2>
            <p>Configure your LLM provider (OpenAI, OpenRouter, etc.).</p>

            <table class="form-table">
                <tr>
                    <th scope="row">API Key</th>
                    <td>
                        <input type="password" id="chroma_openai_api_key" value="<?php echo esc_attr($key); ?>"
                            class="regular-text" placeholder="sk-..." autocomplete="off">
                        <p class="description">Your key is stored securely.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Model Name</th>
                    <td>
                        <input type="text" id="chroma_llm_model" value="<?php echo esc_attr($model); ?>" class="regular-text"
                            placeholder="gpt-4o-mini">
                        <p class="description">e.g., <code>gpt-4o</code>, <code>claude-3-sonnet</code> (via OpenRouter),
                            <code>llama-3</code></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Base URL</th>
                    <td>
                        <input type="text" id="chroma_llm_base_url" value="<?php echo esc_attr($base_url); ?>"
                            class="regular-text" placeholder="https://api.openai.com/v1">
                        <p class="description">Default: <code>https://api.openai.com/v1</code>. Change for OpenRouter/LocalAI.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Actions</th>
                    <td>
                        <div style="display: flex; gap: 10px;">
                            <button id="chroma-save-llm" class="button button-primary">Save Settings</button>
                            <button id="chroma-test-llm" class="button button-secondary">Test Connection</button>
                        </div>
                        <span id="chroma-llm-status" style="display:block; margin-top: 10px; font-weight: bold;"></span>
                    </td>
                </tr>
            </table>
        </div>

        <script>
            jQuery(document).ready(function ($) {
                // Save Settings
                $('#chroma-save-llm').on('click', function (e) {
                    e.preventDefault();
                    var btn = $(this);
                    btn.prop('disabled', true).text('Saving...');

                    $.post(ajaxurl, {
                        action: 'chroma_save_llm_settings',
                        api_key: $('#chroma_openai_api_key').val(),
                        model: $('#chroma_llm_model').val(),
                        base_url: $('#chroma_llm_base_url').val()
                    }, function (response) {
                        btn.prop('disabled', false).text('Save Settings');
                        if (response.success) {
                            alert('Settings saved!');
                        } else {
                            alert('Error saving settings.');
                        }
                    });
                });

                // Test Connection
                $('#chroma-test-llm').on('click', function (e) {
                    e.preventDefault();
                    var btn = $(this);
                    var status = $('#chroma-llm-status');

                    btn.prop('disabled', true).text('Testing...');
                    status.text('').css('color', 'inherit');

                    $.post(ajaxurl, {
                        action: 'chroma_test_llm_connection'
                    }, function (response) {
                        btn.prop('disabled', false).text('Test Connection');
                        if (response.success) {
                            status.text('✅ Connected successfully!').css('color', 'green');
                        } else {
                            status.text('❌ Connection failed: ' + response.data.message).css('color', 'red');
                        }
                    });
                });
            });
        </script>
        <?php
    }

    /**
     * AJAX: Save Settings
     */
    public function ajax_save_settings()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        if (isset($_POST['api_key'])) {
            update_option('chroma_openai_api_key', sanitize_text_field($_POST['api_key']));
        }
        if (isset($_POST['model'])) {
            update_option('chroma_llm_model', sanitize_text_field($_POST['model']));
        }
        if (isset($_POST['base_url'])) {
            $url = esc_url_raw($_POST['base_url']);
            // Remove trailing slash for consistency
            $url = rtrim($url, '/');
            update_option('chroma_llm_base_url', $url);
        }

        wp_send_json_success();
    }

    /**
     * AJAX: Test Connection
     */
    public function ajax_test_connection()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        if (!$this->api_key) {
            wp_send_json_error(['message' => 'No API Key found. Please save your key first.']);
        }

        $response = $this->make_request([
            'messages' => [
                ['role' => 'user', 'content' => 'Say "Hello" if you can hear me.']
            ]
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => $response->get_error_message()]);
        }

        wp_send_json_success(['message' => 'Connected!']);
    }

    /**
     * AJAX: Generate Schema from Content
     */
    public function ajax_generate_schema()
    {
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $post_id = intval($_POST['post_id']);
        $schema_type = sanitize_text_field($_POST['schema_type']);

        if (!$post_id || !$schema_type) {
            wp_send_json_error(['message' => 'Missing parameters']);
        }

        $post = get_post($post_id);
        if (!$post) {
            wp_send_json_error(['message' => 'Post not found']);
        }

        // Get schema definition to guide the LLM
        $definitions = Chroma_Schema_Types::get_definitions();
        $expected_keys = [];
        if (isset($definitions[$schema_type]['fields'])) {
            foreach ($definitions[$schema_type]['fields'] as $key => $field) {
                if ($key !== 'custom_fields') { // Skip custom fields repeater
                    $expected_keys[] = $key . ' (' . $field['label'] . ')';
                }
            }
        }

        // Prepare prompt based on schema type
        $prompt = "Analyze the following content and extract data for a Schema.org '{$schema_type}' object.\n";
        $prompt .= "Return ONLY valid JSON. Do not include markdown formatting.\n";

        if (!empty($expected_keys)) {
            $prompt .= "Map the extracted data to the following JSON keys ONLY:\n";
            $prompt .= "- " . implode("\n- ", $expected_keys) . "\n";
            $prompt .= "If a field cannot be found, leave it empty or omit it.\n";
        }

        $prompt .= "\nContent:\n" . strip_tags($post->post_content);

        $response = $this->make_request([
            'messages' => [
                ['role' => 'system', 'content' => 'You are an SEO expert assistant. You extract structured data from text.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'response_format' => ['type' => 'json_object']
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => $response->get_error_message()]);
        }

        if (!isset($response['choices'][0]['message']['content'])) {
            wp_send_json_error(['message' => 'Invalid API response format']);
        }

        $content = $response['choices'][0]['message']['content'];
        $json = json_decode($content, true);

        if (!$json) {
            wp_send_json_error(['message' => 'Failed to parse AI response']);
        }

        wp_send_json_success($json);
    }

    /**
     * AJAX: Generate LLM Targeting Data
     */
    public function ajax_generate_llm_targeting()
    {
        check_ajax_referer('chroma_seo_dashboard_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);

        if (!$post) {
            wp_send_json_error(['message' => 'Post not found']);
        }

        $prompt = "Analyze the following content and generate LLM optimization data.\n";
        $prompt .= "Return ONLY valid JSON with the following keys:\n";
        $prompt .= "- primary_intent: (string) The main user intent (e.g., 'informational', 'commercial', 'transactional').\n";
        $prompt .= "- target_queries: (array of strings) 3-5 natural language questions users might ask to find this.\n";
        $prompt .= "- key_differentiators: (array of strings) 3-5 unique selling points or key facts.\n";
        $prompt .= "\nContent:\n" . strip_tags($post->post_content);

        $response = $this->make_request([
            'messages' => [
                ['role' => 'system', 'content' => 'You are an SEO expert specializing in LLM optimization (GEO).'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'response_format' => ['type' => 'json_object']
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => $response->get_error_message()]);
        }

        $content = $response['choices'][0]['message']['content'];
        $json = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(['message' => 'Invalid JSON response from AI']);
        }

        wp_send_json_success($json);
    }

    /**
     * Make Request to OpenAI
     */
    private function make_request($data)
    {
        // Use configured base URL or default
        $base_url = $this->base_url ?: 'https://api.openai.com/v1';
        $url = $base_url . '/chat/completions';

        $body = array_merge([
            'model' => $this->model ?: 'gpt-4o-mini',
            'temperature' => 0.7,
        ], $data);

        $args = [
            'body' => json_encode($body),
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key
            ],
            'timeout' => 30
        ];

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ($code !== 200) {
            $msg = isset($data['error']['message']) ? $data['error']['message'] : 'Unknown API Error';
            return new WP_Error('api_error', $msg);
        }

        return $data;
    }
}
