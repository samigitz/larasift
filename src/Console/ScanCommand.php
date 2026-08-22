<?php

declare(strict_types=1);

namespace LaraSift\Console;

use LaraSift\Discovery\FileDiscovery;
use LaraSift\Finding\Severity;
use LaraSift\Reporting\JsonReporter;
use LaraSift\Reporting\TerminalReporter;
use LaraSift\Rules\RuleRegistry;
use LaraSift\Scanning\ScanContext;
use LaraSift\Scanning\ScanResult;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'scan', description: 'Scan a Laravel project for common security issues')]
final class ScanCommand extends Command
{
    public function __construct(private readonly RuleRegistry $rules)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'Output format: table or json', 'table')
            ->addOption('rule', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Only run a rule ID')
            ->addOption('category', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Only run a category')
            ->addOption('severity', null, InputOption::VALUE_REQUIRED, 'Minimum displayed severity', 'low')
            ->addOption('details', null, InputOption::VALUE_NONE, 'Show remediation details');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startedAt = microtime(true);
        $root = getcwd();
        $format = strtolower((string) $input->getOption('format'));
        $severity = Severity::tryFrom(strtolower((string) $input->getOption('severity')));

        if ($root === false || ! is_dir($root)) {
            $output->writeln('<error>Unable to read the current working directory.</error>');

            return ExitCode::InvalidUsage->value;
        }

        if (! in_array($format, ['table', 'json'], true)) {
            $output->writeln('<error>Format must be table or json.</error>');

            return ExitCode::InvalidUsage->value;
        }

        if ($severity === null) {
            $output->writeln('<error>Severity must be low, medium, high, or critical.</error>');

            return ExitCode::InvalidUsage->value;
        }

        $selectedRules = $this->rules->select(
            $input->getOption('rule'),
            $input->getOption('category'),
        );

        if ($selectedRules === []) {
            $output->writeln('<error>No rules matched the requested filters.</error>');

            return ExitCode::InvalidUsage->value;
        }

        $files = (new FileDiscovery)->discover($root);
        $context = new ScanContext($root, $files);
        $findings = [];

        foreach ($selectedRules as $rule) {
            foreach ($rule->analyze($context) as $finding) {
                if ($finding->severity->rank() >= $severity->rank()) {
                    $findings[] = $finding;
                }
            }
        }

        usort($findings, static fn ($left, $right): int => [
            -$left->severity->rank(),
            $left->location->path,
            $left->location->line,
            $left->ruleId,
        ] <=> [
            -$right->severity->rank(),
            $right->location->path,
            $right->location->line,
            $right->ruleId,
        ]);

        $result = new ScanResult(
            $root,
            count($files),
            $findings,
            microtime(true) - $startedAt,
        );

        $reporter = $format === 'json' ? new JsonReporter : new TerminalReporter;
        $reporter->report($result, $output, (bool) $input->getOption('details'));

        return $findings === [] ? ExitCode::Clean->value : ExitCode::Findings->value;
    }
}
