<?php

declare(strict_types=1);

namespace LaraSift\Rules;

use LaraSift\Finding\Finding;
use LaraSift\Scanning\ScanContext;

interface Rule
{
    public function metadata(): RuleMetadata;

    /** @return iterable<Finding> */
    public function analyze(ScanContext $context): iterable;
}
