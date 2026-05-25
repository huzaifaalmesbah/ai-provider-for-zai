<?php
/**
 * Plugin Name: AI Provider for Z.AI
 * Plugin URI: https://github.com/huzaifaalmesbah/ai-provider-for-zai
 * Description: AI Provider for Z.AI (GLM models) for the WordPress AI Client.
 * Requires at least: 7.0
 * Requires PHP: 7.4
 * Version: 1.0.0
 * Author: Huzaifa Al Mesbah
 * Author URI: https://profiles.wordpress.org/huzaifaalmesbah/
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-provider-for-zai
 *
 * @package Huzaifa\AiProviderForZAI
 */

declare(strict_types=1);

namespace Huzaifa\AiProviderForZAI;

use WordPress\AiClient\AiClient;
use Huzaifa\AiProviderForZAI\Provider\ZAIProvider;
use Huzaifa\AiProviderForZAI\Settings\AdminPage;

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/src/autoload.php';

/**
 * Registers the Z.AI provider with the AI Client.
 *
 * @since 1.0.0
 *
 * @return void
 */
function register_provider(): void
{
    if (!class_exists(AiClient::class)) {
        return;
    }

    $registry = AiClient::defaultRegistry();

    if ($registry->hasProvider(ZAIProvider::class)) {
        return;
    }

    $registry->registerProvider(ZAIProvider::class);
}

add_action('init', __NAMESPACE__ . '\\register_provider', 5);

// Auto-detect API type when the API key is saved from the Connectors page.
add_action('update_option_connectors_ai_zai_api_key', [AdminPage::class, 'onApiKeyUpdate'], 10, 2);
add_action('add_option_connectors_ai_zai_api_key', [AdminPage::class, 'onApiKeyAdd'], 10, 2);
