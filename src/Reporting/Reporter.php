<?php

declare(strict_types=1);

namespace LaraSift\Reporting;

use LaraSift\Scanning\ScanResult;
use Symfony\Component\Console\Output\OutputInterface;

interface Reporter
{
    public function report(ScanResult $result, OutputInterface $output, bool $details): void;
}
