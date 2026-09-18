<?php

declare(strict_types=1);

namespace LaraSift\Tests;

use LaraSift\Application;
use PHPUnit\Framework\TestCase;

final class ApplicationTest extends TestCase
{
    public function test_application_registers_its_commands(): void
    {
        $application = new Application;

        self::assertTrue($application->has('scan'));
        self::assertTrue($application->has('list-rules'));
    }
}
