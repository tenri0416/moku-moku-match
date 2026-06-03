<?php

namespace App\Services\Ai\Contracts;

use App\Services\Ai\Data\AiProviderResult;

interface AiProviderInterface
{
    public function providerName(): string;

    public function modelName(): string;

    public function isAvailable(): bool;

    public function requestJson(string $prompt, float $temperature, int $maxOutputTokens): AiProviderResult;
}
