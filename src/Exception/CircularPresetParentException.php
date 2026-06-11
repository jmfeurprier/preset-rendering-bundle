<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

class CircularPresetParentException extends InvalidConfigurationException
{
    /**
     * @param non-empty-list<string> $chain
     */
    public function __construct(array $chain)
    {
        parent::__construct(
            sprintf(
                'Circular preset parent chain detected: %s.',
                implode(' → ', $chain),
            ),
        );
    }
}
