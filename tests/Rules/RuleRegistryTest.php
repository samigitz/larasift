<?php

declare(strict_types=1);

namespace LaraSift\Tests\Rules;

use LaraSift\Rules\RuleRegistry;
use LaraSift\Rules\Xss\UnescapedBladeRule;
use PHPUnit\Framework\TestCase;

final class RuleRegistryTest extends TestCase
{
    public function test_it_filters_rules_by_id_or_category(): void
    {
        $registry = new RuleRegistry([new UnescapedBladeRule]);

        self::assertCount(1, $registry->select(['LSEC-XSS-002'], []));
        self::assertCount(1, $registry->select([], ['xss']));
        self::assertSame([], $registry->select(['LSEC-SQL-001'], []));
    }
}
