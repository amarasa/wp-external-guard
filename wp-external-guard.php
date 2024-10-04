<?php

/**
 * Plugin Name: WP External Guard
 * Description: Displays a SweetAlert notification when users click on external links. Customize settings to target all external links or specific URLs.
 * Version: 1.0.1
 * Author: Angelo Marasa
 * Text Domain: wp-external-guard
 */

require 'puc/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/amarasa/wp-external-guard',
    __FILE__,
    'wp-external-guard-plugin'
);

// Optional: If you're using a private repository, specify the access token like this:
// $myUpdateChecker->setAuthentication('your-token-here');

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue SweetAlert2 and Custom JavaScript
 */
function elas_enqueue_scripts()
{
    if (is_admin()) {
        return; // Do not enqueue scripts in the admin dashboard
    }

    // Enqueue SweetAlert2 CSS
    wp_enqueue_style(
        'sweetalert2-css',
        'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
        array(),
        '11.0.0'
    );

    // Enqueue SweetAlert2 JS
    wp_enqueue_script(
        'sweetalert2-js',
        'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js',
        array(),
        '11.0.0',
        true // Load in footer
    );

    // Register and enqueue the custom script
    wp_register_script(
        'external-link-alert-sweetalert-script',
        plugin_dir_url(__FILE__) . 'assets/js/external-guard.js',
        array('sweetalert2-js'),
        '1.0.1',
        true // Load in footer
    );

    // Retrieve settings from the database
    $options = get_option('elas_settings_options');

    // Prepare settings with defaults
    $settings = array(
        'excludes' => isset($options['excludes']) ? array_map('trim', explode("\n", $options['excludes'])) : array(),
        'message' => isset($options['message']) ? $options['message'] : 'You are about to leave our site. Continue?',
        'title' => isset($options['title']) ? $options['title'] : 'External Link',
        'confirmButtonText' => isset($options['confirmButtonText']) ? $options['confirmButtonText'] : 'Yes, proceed',
        'cancelButtonText' => isset($options['cancelButtonText']) ? $options['cancelButtonText'] : 'Cancel',
        'confirmButtonColor' => isset($options['confirmButtonColor']) ? $options['confirmButtonColor'] : '#3085d6', // Default SweetAlert2 Blue
        'cancelButtonColor' => isset($options['cancelButtonColor']) ? $options['cancelButtonColor'] : '#d33', // Default SweetAlert2 Red
    );

    // Normalize excluded domains by removing protocols, trailing slashes, and 'www.'
    $normalized_excludes = array_filter(array_map(function ($domain) {
        // Remove protocol (http:// or https://)
        $domain = preg_replace('#^https?://#', '', $domain);
        // Remove trailing slash
        $domain = rtrim($domain, '/');
        // Remove 'www.' prefix if present
        $domain = preg_replace('#^www\.#', '', $domain);
        // Validate domain format
        if (filter_var('http://' . $domain, FILTER_VALIDATE_URL)) {
            return strtolower($domain);
        }
        return null; // Exclude invalid domains
    }, $settings['excludes']));

    $settings['excludes'] = $normalized_excludes;

    // Localize script to pass PHP variables to JavaScript
    wp_localize_script(
        'external-link-alert-sweetalert-script',
        'ela_settings',
        $settings
    );

    // Enqueue the custom script
    wp_enqueue_script('external-link-alert-sweetalert-script');
}
add_action('wp_enqueue_scripts', 'elas_enqueue_scripts');

/**
 * Add Settings Menu
 */
function elas_add_admin_menu()
{
    add_options_page(
        __('WP External Guard Settings', 'wp-external-guard'),
        __('WP External Guard', 'wp-external-guard'),
        'manage_options',
        'wp-external-guard',
        'elas_options_page'
    );
}
add_action('admin_menu', 'elas_add_admin_menu');

/**
 * Register Settings
 */
function elas_settings_init()
{
    register_setting('elas_settings_group', 'elas_settings_options', 'elas_settings_sanitize');

    add_settings_section(
        'elas_settings_section',
        __('External Link Alert Settings', 'wp-external-guard'),
        'elas_settings_section_callback',
        'wp-external-guard'
    );

    add_settings_field(
        'excludes',
        __('Excluded Domains', 'wp-external-guard'),
        'elas_excludes_render',
        'wp-external-guard',
        'elas_settings_section'
    );

    add_settings_field(
        'message',
        __('Alert Message', 'wp-external-guard'),
        'elas_message_render',
        'wp-external-guard',
        'elas_settings_section'
    );

    add_settings_field(
        'title',
        __('Alert Title', 'wp-external-guard'),
        'elas_title_render',
        'wp-external-guard',
        'elas_settings_section'
    );

    add_settings_field(
        'confirmButtonText',
        __('Confirm Button Text', 'wp-external-guard'),
        'elas_confirm_button_text_render',
        'wp-external-guard',
        'elas_settings_section'
    );

    add_settings_field(
        'cancelButtonText',
        __('Cancel Button Text', 'wp-external-guard'),
        'elas_cancel_button_text_render',
        'wp-external-guard',
        'elas_settings_section'
    );

    // New Fields for Button Colors
    add_settings_field(
        'confirmButtonColor',
        __('Confirm Button Color', 'wp-external-guard'),
        'elas_confirm_button_color_render',
        'wp-external-guard',
        'elas_settings_section'
    );

    add_settings_field(
        'cancelButtonColor',
        __('Cancel Button Color', 'wp-external-guard'),
        'elas_cancel_button_color_render',
        'wp-external-guard',
        'elas_settings_section'
    );
}
add_action('admin_init', 'elas_settings_init');

/**
 * Sanitize Settings Inputs
 */
function elas_settings_sanitize($input)
{
    $sanitized = array();

    if (isset($input['excludes'])) {
        // Remove empty lines and sanitize each domain
        $domains = explode("\n", $input['excludes']);
        $domains = array_map('sanitize_text_field', $domains);
        $domains = array_filter($domains); // Remove empty entries
        $sanitized['excludes'] = implode("\n", $domains);
    }

    if (isset($input['message'])) {
        $sanitized['message'] = sanitize_textarea_field($input['message']);
    }

    if (isset($input['title'])) {
        $sanitized['title'] = sanitize_text_field($input['title']);
    }

    if (isset($input['confirmButtonText'])) {
        $sanitized['confirmButtonText'] = sanitize_text_field($input['confirmButtonText']);
    }

    if (isset($input['cancelButtonText'])) {
        $sanitized['cancelButtonText'] = sanitize_text_field($input['cancelButtonText']);
    }

    // Sanitize color inputs
    if (isset($input['confirmButtonColor'])) {
        $sanitized['confirmButtonColor'] = sanitize_hex_color($input['confirmButtonColor']);
    }

    if (isset($input['cancelButtonColor'])) {
        $sanitized['cancelButtonColor'] = sanitize_hex_color($input['cancelButtonColor']);
    }

    return $sanitized;
}

/**
 * Settings Section Callback
 */
function elas_settings_section_callback()
{
    echo '<p>' . __('Configure the settings for WP External Guard.', 'wp-external-guard') . '</p>';
}

/**
 * Render Excludes Field
 */
function elas_excludes_render()
{
    $options = get_option('elas_settings_options');
    $excludes = isset($options['excludes']) ? esc_textarea($options['excludes']) : '';
    echo '<textarea name="elas_settings_options[excludes]" rows="5" cols="50" class="large-text">' . $excludes . '</textarea>';
    echo '<p class="description">' . __('Enter one domain per line to exclude from the alert. Do not include "http://" or "https://". Example: google.com', 'wp-external-guard') . '</p>';
}

/**
 * Render Message Field as Textarea
 */
function elas_message_render()
{
    $options = get_option('elas_settings_options');
    $message = isset($options['message']) ? esc_textarea($options['message']) : 'You are about to leave our site. Continue?';
    echo '<textarea name="elas_settings_options[message]" rows="4" cols="50" class="large-text">' . $message . '</textarea>';
    echo '<p class="description">' . __('Enter the message to display in the alert. You can use multiple lines for a more detailed message.', 'wp-external-guard') . '</p>';
}

/**
 * Render Title Field
 */
function elas_title_render()
{
    $options = get_option('elas_settings_options');
    $title = isset($options['title']) ? esc_attr($options['title']) : 'External Link';
    echo '<input type="text" name="elas_settings_options[title]" value="' . $title . '" class="regular-text" />';
    echo '<p class="description">' . __('Enter the title for the alert dialog.', 'wp-external-guard') . '</p>';
}

/**
 * Render Confirm Button Text Field
 */
function elas_confirm_button_text_render()
{
    $options = get_option('elas_settings_options');
    $confirmText = isset($options['confirmButtonText']) ? esc_attr($options['confirmButtonText']) : 'Yes, proceed';
    echo '<input type="text" name="elas_settings_options[confirmButtonText]" value="' . $confirmText . '" class="regular-text" />';
    echo '<p class="description">' . __('Enter the text for the confirm button.', 'wp-external-guard') . '</p>';
}

/**
 * Render Cancel Button Text Field
 */
function elas_cancel_button_text_render()
{
    $options = get_option('elas_settings_options');
    $cancelText = isset($options['cancelButtonText']) ? esc_attr($options['cancelButtonText']) : 'Cancel';
    echo '<input type="text" name="elas_settings_options[cancelButtonText]" value="' . $cancelText . '" class="regular-text" />';
    echo '<p class="description">' . __('Enter the text for the cancel button.', 'wp-external-guard') . '</p>';
}

/**
 * Render Confirm Button Color Field
 */
function elas_confirm_button_color_render()
{
    $options = get_option('elas_settings_options');
    $confirmColor = isset($options['confirmButtonColor']) ? esc_attr($options['confirmButtonColor']) : '#3085d6'; // Default SweetAlert2 Blue
?>
    <input type="text" name="elas_settings_options[confirmButtonColor]" value="<?php echo $confirmColor; ?>" class="color-field" data-default-color="#3085d6" />
    <p class="description"><?php _e('Select the color for the confirm button ("Yes, proceed").', 'wp-external-guard'); ?></p>
<?php
}

/**
 * Render Cancel Button Color Field
 */
function elas_cancel_button_color_render()
{
    $options = get_option('elas_settings_options');
    $cancelColor = isset($options['cancelButtonColor']) ? esc_attr($options['cancelButtonColor']) : '#d33'; // Default SweetAlert2 Red
?>
    <input type="text" name="elas_settings_options[cancelButtonColor]" value="<?php echo $cancelColor; ?>" class="color-field" data-default-color="#d33" />
    <p class="description"><?php _e('Select the color for the cancel button ("Cancel").', 'wp-external-guard'); ?></p>
<?php
}

/**
 * Enqueue Color Picker Assets on Settings Page
 */
function elas_admin_enqueue_scripts($hook)
{
    // Load color picker only on the plugin's settings page
    if ($hook !== 'settings_page_wp-external-guard') {
        return;
    }

    // Enqueue WordPress color picker CSS and JS
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script(
        'wp-color-picker'
    );

    // Initialize the color picker
    wp_add_inline_script('wp-color-picker', '
        jQuery(document).ready(function($){
            $(".color-field").wpColorPicker();
        });
    ');
}
add_action('admin_enqueue_scripts', 'elas_admin_enqueue_scripts');

/**
 * Display the Settings Page
 */
function elas_options_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }
?>
    <div class="wrap">
        <h1><?php esc_html_e('WP External Guard Settings', 'wp-external-guard'); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('elas_settings_group');
            do_settings_sections('wp-external-guard');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Show Admin Notice on Plugin Activation
 */
function elas_activation_notice()
{
    if (get_transient('elas_activation_notice')) {
    ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('WP External Guard activated successfully! Please configure the settings.', 'wp-external-guard'); ?></p>
        </div>
<?php
        delete_transient('elas_activation_notice');
    }
}
add_action('admin_notices', 'elas_activation_notice');

/**
 * Set Transient on Plugin Activation
 */
function elas_activate()
{
    set_transient('elas_activation_notice', true, 5);
}
register_activation_hook(__FILE__, 'elas_activate');
