<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Exception;

use Jmf\PresetRendering\Preset\Preset;

class UndefinedArrayKeyException extends PresetRenderingException
{
    /**
     * @param array<string, mixed> $array
     */
    public function __construct(
        private readonly Preset $preset,
        private readonly array $array,
        private readonly string $key,
    ) {
        parent::__construct(
            message: $this->buildMessage(),
        );
    }

    private function buildMessage(): string
    {
        return sprintf(
            "Cannot read key %s from array (defined keys: %s) in preset '%s'.",
            $this->key,
            $this->buildKeysString(),
            $this->preset->getId(),
        );
    }

    private function buildKeysString(): string
    {
        if ([] === $this->array) {
            return 'none';
        }

        $keys      = array_keys($this->array);
        $truncated = false;

        if (count($keys) > 10) {
            $keys = array_slice($keys, 0, 10);

            $truncated = true;
        }

        $keysString = "'" . implode("', '", $keys) . "'";

        if ($truncated) {
            $keysString .= ', ...';
        }

        return $keysString;
    }

    public function getPreset(): Preset
    {
        return $this->preset;
    }

    /**
     * @return array<string, mixed>
     */
    public function getArray(): array
    {
        return $this->array;
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
