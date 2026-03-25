<?php

declare(strict_types=1);

namespace Huzaifa\AiProviderForZAI\Models;

use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\Enums\HttpMethodEnum;
use WordPress\AiClient\Providers\OpenAiCompatibleImplementation\AbstractOpenAiCompatibleTextGenerationModel;
use Huzaifa\AiProviderForZAI\Provider\ZAIProvider;

/**
 * Class for text generation models used by AI Provider for Z.AI.
 *
 * @since 1.0.0
 *
 * @package Huzaifa\AiProviderForZAI
 */
class ZAITextGenerationModel extends AbstractOpenAiCompatibleTextGenerationModel
{
    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     */
    protected function createRequest(
        HttpMethodEnum $method,
        string $path,
        array $headers = [],
        $data = null
    ): Request {
        return new Request(
            $method,
            ZAIProvider::url($path),
            $headers,
            $data,
            $this->getRequestOptions()
        );
    }
}
