<?php

declare(strict_types=1);

namespace Huzaifa\AiProviderForZAI\Settings;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registers and renders the Settings > Z.AI admin page.
 *
 * @since 1.0.0
 *
 * @package Huzaifa\AiProviderForZAI
 */
class AdminPage
{
    public const OPTION_API_TYPE = 'zai_api_type';
    public const OPTION_GROUP    = 'zai_settings';
    public const PAGE_SLUG       = 'zai-settings';

    /**
     * Register hooks for the admin page.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function register(): void
    {
        add_action('admin_menu', [self::class, 'add_menu_page']);
        add_action('admin_init', [self::class, 'register_settings']);
    }

    /**
     * Add the submenu page under Settings.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function add_menu_page(): void
    {
        add_options_page(
            __('Z.AI', 'ai-provider-for-zai'),
            __('Z.AI', 'ai-provider-for-zai'),
            'manage_options',
            self::PAGE_SLUG,
            [self::class, 'render_page']
        );
    }

    /**
     * Register the settings and fields.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function register_settings(): void
    {
        register_setting(
            self::OPTION_GROUP,
            self::OPTION_API_TYPE,
            [
                'type'              => 'string',
                'default'           => 'general',
                'sanitize_callback' => 'sanitize_text_field',
            ]
        );

        add_settings_section(
            'zai_api_section',
            __('API Settings', 'ai-provider-for-zai'),
            [self::class, 'render_section_description'],
            self::PAGE_SLUG
        );

        add_settings_field(
            self::OPTION_API_TYPE,
            __('API Type', 'ai-provider-for-zai'),
            [self::class, 'render_api_type_field'],
            self::PAGE_SLUG,
            'zai_api_section'
        );
    }

    /**
     * Render the section description.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function render_section_description(): void
    {
        echo '<p>' . esc_html__('Select the preferred API type for the Z.AI models.', 'ai-provider-for-zai') . '</p>';
    }

    /**
     * Render the API type dropdown field.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function render_api_type_field(): void
    {
        $current = get_option(self::OPTION_API_TYPE, 'general');
        ?>
        <select name="<?php echo esc_attr(self::OPTION_API_TYPE); ?>" id="zai_api_type_select">
            <option value="general" <?php selected($current, 'general'); ?>>
                <?php esc_html_e('General API', 'ai-provider-for-zai'); ?>
            </option>
            <option value="coding" <?php selected($current, 'coding'); ?>>
                <?php esc_html_e('Coding API', 'ai-provider-for-zai'); ?>
            </option>
        </select>
        <p class="description">
            <?php esc_html_e('Choose the endpoint type for generation. The Coding API is tailored natively for code generation, while the General API is the standard conversational endpoint.', 'ai-provider-for-zai'); ?>
        </p>
        <?php
    }

    /**
     * Render the settings page.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function render_page(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        echo '<div class="wrap">';
        echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';

        settings_errors(self::OPTION_API_TYPE);

        echo '<form action="options.php" method="post">';
        settings_fields(self::OPTION_GROUP);
        do_settings_sections(self::PAGE_SLUG);
        submit_button();
        echo '</form>';
        echo '</div>';
    }
}
