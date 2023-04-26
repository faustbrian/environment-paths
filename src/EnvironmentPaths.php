<?php

declare(strict_types=1);

namespace BombenProdukt\EnvironmentPaths;

final class EnvironmentPaths
{
    public function get(string $name, ?string $suffix = 'php'): array
    {
        if (\is_string($suffix)) {
            $name .= '-'.$suffix;
        }

        if (\PHP_OS_FAMILY === 'Darwin') {
            return $this->macos($name);
        }

        if (\PHP_OS_FAMILY === 'Windows') {
            return $this->windows($name);
        }

        return $this->linux($name);
    }

    private function macos(string $name): array
    {
        $library = $this->joinPaths($this->homeDirectory(), 'Library');

        return [
            'data' => $this->joinPaths($library, 'Application Support', $name),
            'config' => $this->joinPaths($library, 'Preferences', $name),
            'cache' => $this->joinPaths($library, 'Caches', $name),
            'log' => $this->joinPaths($library, 'Logs', $name),
            'temp' => $this->joinPaths($this->tempDirectory(), $name),
        ];
    }

    private function windows(string $name): array
    {
        $appData = env('APPDATA') ?: $this->joinPaths($this->homeDirectory(), 'AppData', 'Roaming');
        $localAppData = env('LOCALAPPDATA') ?: $this->joinPaths($this->homeDirectory(), 'AppData', 'Local');

        return [
            'data' => $this->joinPaths($localAppData, $name, 'Data'),
            'config' => $this->joinPaths($appData, $name, 'Config'),
            'cache' => $this->joinPaths($localAppData, $name, 'Cache'),
            'log' => $this->joinPaths($localAppData, $name, 'Log'),
            'temp' => $this->joinPaths($this->tempDirectory(), $name),
        ];
    }

    private function linux(string $name): array
    {
        $username = \basename($this->homeDirectory());

        return [
            'data' => $this->joinPaths(env('XDG_DATA_HOME') ?: $this->joinPaths($this->homeDirectory(), '.local', 'share'), $name),
            'config' => $this->joinPaths(env('XDG_CONFIG_HOME') ?: $this->joinPaths($this->homeDirectory(), '.config'), $name),
            'cache' => $this->joinPaths(env('XDG_CACHE_HOME') ?: $this->joinPaths($this->homeDirectory(), '.cache'), $name),
            'log' => $this->joinPaths(env('XDG_STATE_HOME') ?: $this->joinPaths($this->homeDirectory(), '.local', 'state'), $name),
            'temp' => $this->joinPaths($this->tempDirectory(), $username, $name),
        ];
    }

    private function joinPaths(...$paths): string
    {
        return \preg_replace('~[/\\\\]+~', \DIRECTORY_SEPARATOR, \implode(\DIRECTORY_SEPARATOR, $paths));
    }

    private function homeDirectory(): string
    {
        return env('HOME') ?: env('USERPROFILE');
    }

    private function tempDirectory(): string
    {
        return \sys_get_temp_dir();
    }
}
