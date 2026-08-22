<?php

declare(strict_types=1);

namespace LaraSift\Scanning;

use LaraSift\Application;
use LaraSift\Finding\Finding;

final readonly class ScanResult
{
    /** @param list<Finding> $findings */
    public function __construct(
        public string $root,
        public int $filesScanned,
        public array $findings,
        public float $duration,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'schema_version' => 1,
            'tool' => ['name' => 'larasift', 'version' => Application::VERSION],
            'scan' => [
                'root' => $this->root,
                'files_scanned' => $this->filesScanned,
                'duration_seconds' => round($this->duration, 4),
                'status' => $this->findings === [] ? 'passed' : 'findings',
            ],
            'findings' => array_map(
                static fn (Finding $finding): array => $finding->toArray(),
                $this->findings,
            ),
        ];
    }
}
