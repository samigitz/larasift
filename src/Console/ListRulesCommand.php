<?php

declare(strict_types=1);

namespace LaraSift\Console;

use LaraSift\Rules\RuleRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'list-rules', description: 'List available security rules')]
final class ListRulesCommand extends Command
{
    public function __construct(private readonly RuleRegistry $rules)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $rows = [];

        foreach ($this->rules->all() as $rule) {
            $metadata = $rule->metadata();
            $rows[] = [
                $metadata->id,
                $metadata->severity->label(),
                $metadata->category,
                $metadata->title,
            ];
        }

        $io->title('LaraSift rules');
        $io->table(['Rule', 'Severity', 'Category', 'Description'], $rows);

        return ExitCode::Clean->value;
    }
}
