<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\Config\RectorConfig;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\AddSeeTestAnnotationRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\YieldDataProviderRector;

$rootPath = realpath(__DIR__ . '/..') . '/';

return RectorConfig::configure()
    ->withCache($rootPath . 'var/cache/rector')
    ->withPaths(
        [
            $rootPath . 'src',
            $rootPath . 'tests',
        ],
    )
    ->withPhpSets()
    ->withSkip(
        [
            CatchExceptionNameMatchingTypeRector::class,
            FlipTypeControlToUseExclusiveTypeRector::class,
            PreferPHPUnitThisCallRector::class,
            RenameParamToMatchTypeRector::class,
            RenamePropertyToMatchTypeRector::class,
            AddSeeTestAnnotationRector::class,
            YieldDataProviderRector::class,
        ],
    )
    ->withPreparedSets(
        deadCode:            true,
        codeQuality:         true,
        codingStyle:         true,
        typeDeclarations:    true,
        privatization:       true,
        naming:              true,
        instanceOf:          true,
        earlyReturn:         true,
        carbon:              true,
        rectorPreset:        true,
        phpunitCodeQuality:  true,
        doctrineCodeQuality: true,
        symfonyCodeQuality:  true,
        symfonyConfigs:      true,
    )
;
