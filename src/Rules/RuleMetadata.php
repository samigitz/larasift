<?php

declare(strict_types=1);

namespace LaraSift\Rules;

use LaraSift\Finding\Severity;

final readonly class RuleMetadata
{
    public function __construct(
        public string $id,
        public string $title,
        public string $category,
        public Severity $severity,
    ) {}
}
