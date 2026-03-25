<?php

declare(strict_types=1);

namespace Huzaifa\AiProviderForZAI\Metadata;

use WordPress\AiClient\Messages\Enums\ModalityEnum;
use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\DTO\Response;
use WordPress\AiClient\Providers\Http\Enums\HttpMethodEnum;
use WordPress\AiClient\Providers\Http\Exception\ResponseException;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Providers\Models\DTO\SupportedOption;
use WordPress\AiClient\Providers\Models\Enums\CapabilityEnum;
use WordPress\AiClient\Providers\Models\Enums\OptionEnum;
use WordPress\AiClient\Providers\OpenAiCompatibleImplementation\AbstractOpenAiCompatibleModelMetadataDirectory;
use Huzaifa\AiProviderForZAI\Provider\ZAIProvider;

/**
 * Class for the Z.AI model metadata directory.
 *
 * @since 1.0.0
 *
 * @package Huzaifa\AiProviderForZAI
 *
 * @phpstan-type ModelsResponseData array{
 *     data: list<array{id: string}>
 * }
 */
class ZAIModelMetadataDirectory extends AbstractOpenAiCompatibleModelMetadataDirectory
{
    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     */
    protected function createRequest(HttpMethodEnum $method, string $path, array $headers = [], $data = null): Request
    {
        return new Request(
            $method,
            ZAIProvider::url($path),
            $headers,
            $data
        );
    }

    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     */
    protected function parseResponseToModelMetadataList(Response $response): array
    {
        /** @var ModelsResponseData $responseData */
        $responseData = $response->getData();

        if (!isset($responseData['data']) || !$responseData['data']) {
            throw ResponseException::fromMissingData('Z.AI', 'data');
        }

        $textCapabilities = [
            CapabilityEnum::textGeneration(),
            CapabilityEnum::chatHistory(),
        ];

        $textOptions = [
            new SupportedOption(OptionEnum::systemInstruction()),
            new SupportedOption(OptionEnum::candidateCount()),
            new SupportedOption(OptionEnum::maxTokens()),
            new SupportedOption(OptionEnum::temperature()),
            new SupportedOption(OptionEnum::topP()),
            new SupportedOption(OptionEnum::stopSequences()),
            new SupportedOption(OptionEnum::presencePenalty()),
            new SupportedOption(OptionEnum::frequencyPenalty()),
            new SupportedOption(OptionEnum::outputMimeType(), ['text/plain', 'application/json']),
            new SupportedOption(OptionEnum::outputSchema()),
            new SupportedOption(OptionEnum::functionDeclarations()),
            new SupportedOption(OptionEnum::customOptions()),
            new SupportedOption(OptionEnum::inputModalities(), [[ModalityEnum::text()]]),
            new SupportedOption(OptionEnum::outputModalities(), [[ModalityEnum::text()]]),
        ];

        $modelsData = (array) $responseData['data'];

        $models = array_values(
            array_map(
                static function (array $modelData) use ($textCapabilities, $textOptions): ModelMetadata {
                    $modelId = $modelData['id'];

                    return new ModelMetadata(
                        $modelId,
                        $modelId,
                        $textCapabilities,
                        $textOptions
                    );
                },
                $modelsData
            )
        );

        usort($models, [$this, 'modelSortCallback']);

        return $models;
    }

    /**
     * Callback function for sorting models by ID, to be used with `usort()`.
     *
     * Prefers higher version numbers (glm-5 > glm-4.7 > glm-4.6) and
     * non-turbo/air variants are listed after the base model.
     *
     * @since 1.0.0
     *
     * @param ModelMetadata $a First model.
     * @param ModelMetadata $b Second model.
     * @return int Comparison result.
     */
    protected function modelSortCallback(ModelMetadata $a, ModelMetadata $b): int
    {
        $aId = $a->getId();
        $bId = $b->getId();

        // Extract version numbers for comparison (e.g. glm-4.5 → 4.5, glm-5 → 5.0).
        $aVersion = $this->extractVersion($aId);
        $bVersion = $this->extractVersion($bId);

        if ($aVersion !== $bVersion) {
            // Higher version first.
            return $bVersion <=> $aVersion;
        }

        // Same version: prefer base model over variants (turbo, air, flash, etc.).
        $aIsVariant = preg_match('/-(?:turbo|air|flash|plus|lite)$/', $aId);
        $bIsVariant = preg_match('/-(?:turbo|air|flash|plus|lite)$/', $bId);

        if ($aIsVariant && !$bIsVariant) {
            return 1;
        }
        if ($bIsVariant && !$aIsVariant) {
            return -1;
        }

        // Fallback: Sort alphabetically.
        return strcmp($aId, $bId);
    }

    /**
     * Extracts a float version number from a model ID string.
     *
     * Examples: glm-4.5 → 4.5, glm-5 → 5.0, glm-4.5-air → 4.5.
     *
     * @since 1.0.0
     *
     * @param string $modelId The model ID.
     * @return float The version number.
     */
    private function extractVersion(string $modelId): float
    {
        if (preg_match('/glm-([\d]+(?:\.[\d]+)?)/', $modelId, $matches)) {
            return (float) $matches[1];
        }

        return 0.0;
    }
}
