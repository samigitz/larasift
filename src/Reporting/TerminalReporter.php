<?php

declare(strict_types=1);

namespace LaraSift\Reporting;

use LaraSift\Finding\Severity;
use LaraSift\Scanning\ScanResult;
use Symfony\Component\Console\Output\OutputInterface;

final class TerminalReporter implements Reporter
{
    public function report(ScanResult $result, OutputInterface $output, bool $details): void
    {
        $output->writeln('');
        $output->writeln('<fg=cyan;options=bold>  LARASIFT</>  Laravel security check');
        $output->writeln(sprintf('  Scanned %d files in %.2fs', $result->filesScanned, $result->duration));
        $output->writeln('');

        if ($result->findings === []) {
            $output->writeln('<fg=green;options=bold>  ✓ No findings</>');

            return;
        }

        foreach ($result->findings as $finding) {
            $style = match ($finding->severity) {
                Severity::Critical, Severity::High => 'red',
                Severity::Medium => 'yellow',
                Severity::Low => 'blue',
            };

            $output->writeln(sprintf(
                '  <fg=%s;options=bold>%-8s</> <options=bold>%s</>  %s',
                $style,
                $finding->severity->label(),
                $finding->ruleId,
                $finding->title,
            ));
            $output->writeln(sprintf(
                '           <fg=gray>%s:%d:%d</>',
                $finding->location->path,
                $finding->location->line,
                $finding->location->column,
            ));
            $output->writeln('           '.$finding->message);

            if ($details) {
                $output->writeln('           <fg=gray>Evidence:</> '.$finding->evidence);
                $output->writeln('           <fg=green>Fix:</> '.$finding->remediation);
            }

            $output->writeln('');
        }

        $counts = array_fill_keys(array_map(
            static fn (Severity $severity): string => $severity->value,
            Severity::cases(),
        ), 0);

        foreach ($result->findings as $finding) {
            $counts[$finding->severity->value]++;
        }

        $output->writeln(sprintf(
            '  <options=bold>Summary</>  %d critical  %d high  %d medium  %d low',
            $counts['critical'],
            $counts['high'],
            $counts['medium'],
            $counts['low'],
        ));
    }
}
