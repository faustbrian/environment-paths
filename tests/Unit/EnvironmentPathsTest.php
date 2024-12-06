<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Unit;

use BaseCodeOy\EnvironmentPaths\EnvironmentPaths;

it('should use the default suffix', function (): void {
    $name = 'unicorn';
    $paths = (new EnvironmentPaths())->get($name);

    foreach ($paths as $path) {
        expect(\str_ends_with((string) $path, $name.'-php'))->toBeTrue();
    }
});

it('should use a custom suffix', function (): void {
    $name = 'unicorn';
    $suffix = 'horn';
    $paths = (new EnvironmentPaths())->get($name, $suffix);

    expect(\str_ends_with((string) $paths['data'], \sprintf('%s-%s', $name, $suffix)))->toBeTrue();
});

it('should use no suffix', function (): void {
    $name = 'unicorn';
    $paths = (new EnvironmentPaths())->get($name, null);

    expect(\str_ends_with((string) $paths['data'], $name))->toBeTrue();
});

if (\PHP_OS_FAMILY === 'Linux') {
    it('should get the correct paths with XDG_*_HOME set', function (): void {
        $envVars = [
            'data' => 'XDG_DATA_HOME',
            'config' => 'XDG_CONFIG_HOME',
            'cache' => 'XDG_CACHE_HOME',
            'log' => 'XDG_STATE_HOME',
        ];

        foreach ($envVars as $env) {
            $_SERVER[$env] = '/tmp/'.$env;
        }

        $name = 'unicorn';
        $paths = (new EnvironmentPaths())->get($name);

        foreach ($envVars as $env => $envVar) {
            $expectedPath = $_SERVER[$envVar];
            expect(\str_starts_with((string) $paths[$env], (string) $expectedPath) && \str_ends_with((string) $paths[$env], $name.'-php'))->toBeTrue();
        }
    });
}
