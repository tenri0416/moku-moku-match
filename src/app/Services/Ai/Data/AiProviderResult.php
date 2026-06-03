<?php

namespace App\Services\Ai\Data;

class AiProviderResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $provider,
        public readonly string $model,
        public readonly ?string $text = null,
        public readonly ?string $errorMessage = null,
        public readonly ?int $statusCode = null,
        public readonly ?int $retryAfterSeconds = null,
    ) {
    }

    public static function success(string $provider, string $model, string $text): self
    {
        return new self(
            success: true,
            provider: $provider,
            model: $model,
            text: $text,
        );
    }

    public static function failed(
        string $provider,
        string $model,
        ?string $errorMessage = null,
        ?int $statusCode = null,
        ?int $retryAfterSeconds = null,
    ): self {
        return new self(
            success: false,
            provider: $provider,
            model: $model,
            errorMessage: $errorMessage,
            statusCode: $statusCode,
            retryAfterSeconds: $retryAfterSeconds,
        );
    }
}
