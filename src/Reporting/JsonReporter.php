<?php

declare(strict_types=1);

namespace LaraSift\Reporting;

use LaraSift\Scanning\ScanResult;
use Symfony\Component\Console\Output\OutputInterface;

final class JsonReporter implements Reporter
{
    public function report(ScanResult $result, OutputInterface $output, bool $details): void
    {
        $output->writeln((string) json_encode(
            $result->toArray(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        ));
    }
}
