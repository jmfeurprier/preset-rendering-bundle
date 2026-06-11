<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

class DuplicatePresetException extends RenderingPresetException
{
    /**
     * @param non-empty-list<string> $presetIds
     */
    public function __construct(
        private readonly array $presetIds,
    ) {
        parent::__construct(
            sprintf(
                'Duplicate preset configuration for %s: defined both inline under "presets" and via "paths".',
                implode(', ', $this->presetIds),
            ),
        );
    }

    /**
     * @return non-empty-list<string>
     */
    public function getPresetIds(): array
    {
        return $this->presetIds;
    }
}
