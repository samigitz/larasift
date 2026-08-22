<?php

declare(strict_types=1);

namespace LaraSift\Scanning;

final readonly class ScanContext
{
    /** @param list<string> $files */
    public function __construct(
        public string $root,
        public array $files,
    ) {}

    public function relativePath(string $file): string
    {
        $prefix = rtrim($this->root, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        return str_starts_with($file, $prefix) ? substr($file, strlen($prefix)) : $file;
    }
}
