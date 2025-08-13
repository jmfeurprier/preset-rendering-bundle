<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

use Jmf\RenderingPreset\Preset\Preset;
use function sprintf;

class UndefinedArrayKeyException extends UnreadableItemValueException
{
    /**
     * @param array<string, mixed> $array
     */
    public function __construct(
        Preset $preset,
        private readonly array $array,
        private readonly string $key,
    ) {
        parent::__construct(
            preset:  $preset,
            message: sprintf(
                         "Cannot read key %s from array (defined keys: %s) in preset '%s'.",
                         $this->key,
                         $this->buildKeysString(),
                         $this->getPreset()->getId(),
                     ),
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
