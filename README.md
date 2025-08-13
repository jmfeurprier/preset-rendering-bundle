# Rendering Preset Bundle

Symfony bundle which allows rendering preset templates.

## Installation & Requirements

Install with [Composer](https://getcomposer.org):

```shell script
composer require jmf/rendering-preset-bundle
```

If you have [Flex](https://symfony.com/packages/Symfony%20Flex) installed, the bundle is then instantly available without any need for initial configuration.
If not, complete your `config/bundles.php` file as indicated below:

```php
<?php

return [
    // ...
    // Other existing bundles.
    // ...
    Jmf\RenderingPreset\RenderingPresetBundle::class => ['all' => true],
];
```
