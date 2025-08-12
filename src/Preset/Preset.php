<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset;

use Jmf\PresetRendering\Preset\Property\PresetPropertyCollection;
use Jmf\TemplateRendering\TemplateInterface;
use Webmozart\Assert\Assert;

readonly class Preset
{
    /**
     * @param non-empty-string      $id
     * @param null|non-empty-string $source
     */
    public function __construct(
        private string $id,
        private ?string $source,
        private ?TemplateInterface $template,
        private PresetPropertyCollection $properties,
    ) {
        Assert::stringNotEmpty($id);
        Assert::nullOrStringNotEmpty($source);
    }

    /**
     * @return non-empty-string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return null|non-empty-string
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getTemplate(): ?TemplateInterface
    {
        return $this->template;
    }

    public function getProperties(): PresetPropertyCollection
    {
        return $this->properties;
    }
}
