<?php

declare(strict_types=1);

namespace Huzaifa\AiProviderForZAI\Settings;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles auto-detection of the Z.AI API type based on the API key.
 *
 * @since 1.0.0
 *
 * @package Huzaifa\AiProviderForZAI
 */
class AdminPage
{
    public const OPTION_API_TYPE = 'zai_api_type';

    /**
     * Available API types and their base URLs.
     *
     * @since 1.0.0
     *
     * @var array<string, array{label: string, url: string}>
     */
    public const API_TYPES = [
        'general' => [
            'label' => 'General API',
            'url'   => 'https://api.z.ai/api/paas/v4',
        ],
        'coding' => [
            'label' => 'Coding API',
            'url'   => 'https://api.z.ai/api/coding/paas/v4',
        ],
    ];

    /**
     * Returns the base URL for the selected API type.
     *
     * @since 1.0.0
     *
     * @return string The base URL for the API.
     */
    public static function getBaseUrl(): string
    {
        $type = get_option(self::OPTION_API_TYPE, 'general');

        if (isset(self::API_TYPES[$type])) {
            return self::API_TYPES[$type]['url'];
        }

        return self::API_TYPES['general']['url'];
    }

    /**
     * Detects the correct API type for a given API key by testing each endpoint.
     *
     * @since 1.0.0
     *
     * @param string $api_key The API key to test.
     * @return string|null The detected API type key, or null if none matched.
     */
    public static function detectApiType(string $api_key): ?string
    {
        foreach (self::API_TYPES as $typeKey => $type) {
            $response = wp_remote_post(
                $type['url'] . '/chat/completions',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $api_key,
                        'Content-Type'  => 'application/json',
                    ],
                    'body'    => wp_json_encode([
                        'model'      => 'glm-4.5',
                        'messages'   => [['role' => 'user', 'content' => 'hi']],
                        'max_tokens' => 1,
                    ]),
                    'timeout' => 15,
                ]
            );

            if (is_wp_error($response)) {
                continue;
            }

            $code = wp_remote_retrieve_response_code($response);
            if (200 === $code) {
                return $typeKey;
            }
        }

        return null;
    }

    /**
     * Auto-detects and saves the correct API type when the API key is updated.
     *
     * @since 1.0.0
     *
     * @param mixed $old_value The old option value.
     * @param mixed $value     The new option value.
     * @return void
     */
    public static function onApiKeyUpdate($old_value, $value): void
    {
        if (!is_string($value) || '' === $value) {
            return;
        }

        if ($value === $old_value) {
            return;
        }

        $type = self::detectApiType($value);

        if ($type !== null) {
            update_option(self::OPTION_API_TYPE, $type);
        }
    }

    /**
     * Handles auto-detection when the API key option is first created.
     *
     * @since 1.0.0
     *
     * @param string $option The option name (unused).
     * @param mixed  $value  The new option value.
     * @return void
     */
    public static function onApiKeyAdd($option, $value): void
    {
        self::onApiKeyUpdate('', $value);
    }
}
