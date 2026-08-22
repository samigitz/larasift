<?php

declare(strict_types=1);

namespace LaraSift\Finding;

final readonly class Finding
{
    public function __construct(
        public string $ruleId,
        public string $title,
        public string $message,
        public Severity $severity,
        public Confidence $confidence,
        public Location $location,
        public string $evidence,
        public string $remediation,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'rule_id' => $this->ruleId,
            'title' => $this->title,
            'message' => $this->message,
            'severity' => $this->severity->value,
            'confidence' => $this->confidence->value,
            'location' => $this->location->toArray(),
            'evidence' => $this->evidence,
            'remediation' => $this->remediation,
        ];
    }
}
