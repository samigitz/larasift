<?php

declare(strict_types=1);

namespace LaraSift;

use LaraSift\Console\ListRulesCommand;
use LaraSift\Console\ScanCommand;
use LaraSift\Rules\RuleRegistry;
use LaraSift\Rules\Xss\UnescapedBladeRule;
use Symfony\Component\Console\Application as SymfonyApplication;

final class Application extends SymfonyApplication
{
    public const VERSION = '0.1.0';

    public function __construct()
    {
        parent::__construct('LaraSift', self::VERSION);

        $rules = new RuleRegistry([
            new UnescapedBladeRule,
        ]);

        $this->add(new ScanCommand($rules));
        $this->add(new ListRulesCommand($rules));
        $this->setDefaultCommand('scan');
    }
}
