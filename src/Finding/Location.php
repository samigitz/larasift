<?php

declare(strict_types=1);

namespace LaraSift\Finding;

final readonly class Location
{
    public function __construct(
        public string $path,
        public int $line,
        public int $column,
    ) {}

    /** @return array{path: string, line: int, column: int} */
    public function toArray(): array
    {
        return [
            'path' => $this->path,
            'line' => $this->line,
            'column' => $this->column,
        ];
    }
}
