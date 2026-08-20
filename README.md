# Rendering Preset Bundle

Symfony bundle for defining and rendering named presets: reusable combinations of a data source, a Twig template, and a set of typed properties (label, alignment, etc.).

## Installation

```shell
composer require jmf/rendering-preset-bundle
```

Register the bundle if you are not using Symfony Flex:

```php
// config/bundles.php
return [
    Jmf\RenderingPreset\JmfRenderingPresetBundle::class => ['all' => true],
];
```

## Configuration

### One file per preset (recommended)

By default the bundle discovers every `*.yaml` file under `config/packages/jmf_rendering_preset/`. The filename (without `.yaml`) becomes the preset id. No explicit `paths` config is needed.

```
config/packages/
├── jmf_rendering_preset.yaml          # properties + optional twig_functions_prefix
└── jmf_rendering_preset/
    ├── boolean.yaml                   # preset id: boolean
    ├── date_time.yaml                 # preset id: date_time
    └── price.yaml                     # preset id: price
```

Each file contains only the preset body (no wrapper key):

```yaml
# config/packages/jmf_rendering_preset/price.yaml
align:    'end'
label:    'Price'
source:   'price'
template: 'preset/price.html.twig'
```

### Main config file

```yaml
# config/packages/jmf_rendering_preset.yaml
jmf_rendering_preset:

    # Optional: prefix for Twig functions (e.g. 'jmf_' → 'jmf_preset_render').
    #twig_functions_prefix: ''

    # Property definitions shared across all presets.
    properties:

        align:
            choices: ['center', 'left', 'right']
            default: 'left'
            required: false

        label:
            required: false
```

### Inline presets

Presets can also be defined inline under `presets:` instead of (or alongside) per-file presets. A preset id defined both ways is a configuration error.

```yaml
jmf_rendering_preset:
    presets:
        price:
            align:    'end'
            label:    'Price'
            source:   'price'
            template: 'preset/price.html.twig'
```

### Custom paths

Override the discovery directory with `paths:`:

```yaml
jmf_rendering_preset:
    paths:
        - '%kernel.project_dir%/config/presets'
```

## Preset options

| Key        | Type   | Description |
|------------|--------|-------------|
| `parent`   | string | Inherit from another preset id. Child values take precedence. |
| `source`   | string | Property path on the item to read the value from (e.g. `createdAt`). Used when the caller passes no `$source`. Ignored when the item is neither an array nor an object. |
| `template` | string | Twig template path used to render the value. |
| `<property>` | mixed | Any property key defined under `properties:` (e.g. `align`, `label`). |

## Twig usage

```twig
{# Render the preset, returns an HTML string. #}
{{ preset_render('price', item) }}

{# Override the source field at call site. #}
{{ preset_render('date_time', item, 'updatedAt') }}

{# The item is the value: no source anywhere, works for any type. #}
{{ preset_render('price', 12.5) }}
{{ preset_render('age', age) }}

{# Get a RenderedPreset object to access content + properties separately. #}
{% set rendered = preset_get('price', item) %}
{{ rendered.content }}
{{ rendered.getPropertyValue('align') }}
```

## Template example

```twig
{# templates/preset/price.html.twig #}
{% if _value is not null %}
    {{ _value | number_format(2) }} €
{% endif %}
```

Templates receive:
- `_value` : the resolved value (read from the item via `source`, or the item itself when no `source` applies)
- `_item` : the original item

## Items and sources

The item can be anything: an array, an object, or a plain value.

**When no `source` applies, the item itself is the value.** `_item` always carries the original item,
whatever the resolution.

| Item | Call-site `source` | Preset `source` | `_value` |
|------|--------------------|-----------------|----------|
| any | none | none | the item itself |
| array / object | set | any | read from the item |
| array / object | none | set | read from the item |
| value (scalar, null, enum) | none | set | the item itself (`source` does not apply, and is ignored) |
| value (scalar, null, enum) | set | any | `UnexpectedItemSourceException` |

**Enums count as values, not as containers.** No source can be read from an enum, so a preset `source`
simply does not apply to one. That lets a single preset serve both callers:

```twig
{# reads `lifeStatus` off the entity, through the preset's own source #}
{{ preset_render('life_status', individual) }}

{# the enum is already the value: the preset's source does not apply #}
{{ preset_render('life_status', individual.lifeStatus) }}
```

So a preset defining no `source` can be handed the value directly:

```twig
{{ preset_render('age', age) }}
{{ preset_render('boolean', isAlive) }}
```

while a preset defining a `source` still reads it from the array or object it is given:

```twig
{{ preset_render('birth_date', individual) }}
```

Without a template, the value is rendered as is (escaped): scalars, `Stringable` objects, and backed
enums (through their `->value`). A preset with neither a `source` nor a `template`, given anything else
(an array, a plain object, a pure enum), throws an `UnexpectedContentValueTypeException`: it has no way
to turn that item into content.
