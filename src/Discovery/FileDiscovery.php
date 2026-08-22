<?php

declare(strict_types=1);

namespace LaraSift\Discovery;

use Symfony\Component\Finder\Finder;

final class FileDiscovery
{
    /** @return list<string> */
    public function discover(string $root): array
    {
        $directories = array_values(array_filter(
            ['app', 'routes', 'resources/views', 'config', 'bootstrap'],
            static fn (string $directory): bool => is_dir($root.'/'.$directory),
        ));

        if ($directories === []) {
            return [];
        }

        $finder = (new Finder)
            ->files()
            ->in(array_map(static fn (string $directory): string => $root.'/'.$directory, $directories))
            ->name(['*.php', '*.blade.php'])
            ->ignoreUnreadableDirs()
            ->sortByName();

        $files = [];

        foreach ($finder as $file) {
            if ($file->getSize() <= 2_000_000) {
                $files[] = $file->getRealPath();
            }
        }

        return $files;
    }
}
