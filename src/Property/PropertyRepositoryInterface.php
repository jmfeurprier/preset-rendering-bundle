<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Property;

use Jmf\RenderingPreset\Exception\ReservedPropertyKeyException;

interface PropertyRepositoryInterface
{
    /**
     * @throws ReservedPropertyKeyException
     */
    public function getCollection(): PropertyCollection;
}
