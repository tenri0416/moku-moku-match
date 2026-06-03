<?php

namespace App\Services\Ai\Support;

class AiTextNormalizer
{
    public function normalize(string $text): string
    {
        $lines = preg_split("/\r\n|\n|\r/", $text);

        if (! is_array($lines)) {
            return trim($text);
        }

        $lines = array_map(fn (string $line) => trim($line), $lines);

        return trim(implode(PHP_EOL, $lines));
    }
}
