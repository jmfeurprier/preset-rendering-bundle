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
| `source`   | string | Property path on the item to read the value from (e.g. `createdAt`). Overrides the caller's `$source` argument. |
| `template` | string | Twig template path used to render the value. |
| `<property>` | mixed | Any property key defined under `properties:` (e.g. `align`, `label`). |

## Twig usage

```twig
{# Render the preset, returns an HTML string. #}
{{ preset_render('price', item) }}

{# Override the source field at call site. #}
{{ preset_render('date_time', item, 'updatedAt') }}

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
- `_value` : the resolved value read from the item via `source`
- `_item` : the original item (array or object)
