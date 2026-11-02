<?php

/**
 * PHP 8.4/8.5 deprecation check, run by the CI "rector" job as a dry run.
 *
 * Only rules that remove a deprecation and keep PHP 8.1 syntax are enabled,
 * no modernization sets. The PHP version is 8.5 because Rector skips these
 * rules for any lower target version.
 */

use Rector\Config\RectorConfig;
use Rector\Php84\Rector\FuncCall\AddEscapeArgumentRector;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;
use Rector\Php85\Rector\FuncCall\ArrayKeyExistsNullToEmptyStringRector;
use Rector\Php85\Rector\ClassMethod\NullDebugInfoReturnRector;
use Rector\Php85\Rector\Class_\SleepToSerializeRector;
use Rector\Php85\Rector\Class_\WakeupToUnserializeRector;
use Rector\Php85\Rector\FuncCall\ChrArgModuloRector;
use Rector\Php85\Rector\FuncCall\OrdSingleByteRector;
use Rector\Php85\Rector\FuncCall\RemoveFinfoBufferContextArgRector;
use Rector\Php85\Rector\ShellExec\ShellExecFunctionCallOverBackticksRector;
use Rector\Php85\Rector\Switch_\ColonAfterSwitchCaseRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src/app/code/community/Dhl/Versenden',
        __DIR__ . '/src/app/design',
        __DIR__ . '/src/lib/Dhl/Versenden',
    ])
    ->withFileExtensions(['php', 'phtml'])
    ->withPhpVersion(PhpVersion::PHP_85)
    ->withRules([
        ExplicitNullableParamTypeRector::class,
        AddEscapeArgumentRector::class,
        ArrayKeyExistsNullToEmptyStringRector::class,
        ChrArgModuloRector::class,
        ColonAfterSwitchCaseRector::class,
        NullDebugInfoReturnRector::class,
        OrdSingleByteRector::class,
        RemoveFinfoBufferContextArgRector::class,
        ShellExecFunctionCallOverBackticksRector::class,
        SleepToSerializeRector::class,
        WakeupToUnserializeRector::class,
    ]);
