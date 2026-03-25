<?php

declare(strict_types=1);

namespace Huzaifa\AiProviderForZAI\Provider;

use WordPress\AiClient\AiClient;
use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Providers\ApiBasedImplementation\AbstractApiProvider;
use WordPress\AiClient\Providers\ApiBasedImplementation\ListModelsApiBasedProviderAvailability;
use WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface;
use WordPress\AiClient\Providers\Contracts\ProviderAvailabilityInterface;
use WordPress\AiClient\Providers\DTO\ProviderMetadata;
use WordPress\AiClient\Providers\Enums\ProviderTypeEnum;
use WordPress\AiClient\Providers\Http\Enums\RequestAuthenticationMethod;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use Huzaifa\AiProviderForZAI\Metadata\ZAIModelMetadataDirectory;
use Huzaifa\AiProviderForZAI\Models\ZAITextGenerationModel;

/**
 * Class for AI Provider for Z.AI.
 *
 * @since 1.0.0
 *
 * @package Huzaifa\AiProviderForZAI
 */
class ZAIProvider extends AbstractApiProvider
{
    /**
     * Fallback base URL for the Z.AI API.
     *
     * Used if the primary coding endpoint is unavailable.
     *
     * @since 1.0.0
     */
    public const FALLBACK_BASE_URL = 'https://api.z.ai/api/paas/v4';

    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     */
    protected static function baseUrl(): string
    {
        return 'https://api.z.ai/api/coding/paas/v4';
    }

    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     */
    protected static function createModel(
        ModelMetadata $modelMetadata,
        ProviderMetadata $providerMetadata
    ): ModelInterface {
        $capabilities = $modelMetadata->getSupportedCapabilities();
        foreach ($capabilities as $capability) {
            if ($capability->isTextGeneration()) {
                return new ZAITextGenerationModel($modelMetadata, $providerMetadata);
            }
        }

        // phpcs:disable WordPress.Security.EscapeOutput.ExceptionNotEscaped
        throw new RuntimeException(
            'Unsupported model capabilities: ' . implode(', ', $capabilities)
        );
        // phpcs:enable WordPress.Security.EscapeOutput.ExceptionNotEscaped
    }

    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     */
    protected static function createProviderMetadata(): ProviderMetadata
    {
        $providerMetadataArgs = [
            'zai',
            'Z.AI',
            ProviderTypeEnum::cloud(),
            'https://z.ai/subscribe',
        ];

        // Authentication method support was added in 0.4.0.
        if (class_exists(RequestAuthenticationMethod::class)) {
            $providerMetadataArgs[] = RequestAuthenticationMethod::apiKey();
        }

        // Provider description support was added in 1.2.0.
        if (defined(AiClient::class . '::VERSION') && version_compare(AiClient::VERSION, '1.2.0', '>=')) {
            if (function_exists('__')) {
                $providerMetadataArgs[] = __('Text generation with Z.AI GLM models.', 'ai-provider-for-zai');
            } else {
                $providerMetadataArgs[] = 'Text generation with Z.AI GLM models.';
            }

            // Provider logo support was added in 1.3.0.
            if (version_compare(AiClient::VERSION, '1.3.0', '>=')) {
                $providerMetadataArgs[] = dirname(__DIR__, 2) . '/assets/logo.svg';
            }
        }

        return new ProviderMetadata(...$providerMetadataArgs);
    }

    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     */
    protected static function createProviderAvailability(): ProviderAvailabilityInterface
    {
        // Check valid API access by attempting to list models.
        return new ListModelsApiBasedProviderAvailability(
            static::modelMetadataDirectory()
        );
    }

    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     */
    protected static function createModelMetadataDirectory(): ModelMetadataDirectoryInterface
    {
        return new ZAIModelMetadataDirectory();
    }
}
