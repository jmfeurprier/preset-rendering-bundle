<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Property;

use Jmf\PresetRendering\Exception\ReservedPropertyKeyException;

interface PropertyRepositoryInterface
{
    /**
     * @throws ReservedPropertyKeyException
     */
    public function getCollection(): PropertyCollection;
}
