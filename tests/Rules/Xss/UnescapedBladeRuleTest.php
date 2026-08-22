<?php

declare(strict_types=1);

namespace LaraSift\Tests\Rules\Xss;

use LaraSift\Rules\Xss\UnescapedBladeRule;
use LaraSift\Scanning\ScanContext;
use PHPUnit\Framework\TestCase;

final class UnescapedBladeRuleTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir().'/larasift-'.bin2hex(random_bytes(6));
        mkdir($this->directory.'/resources/views', 0777, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->directory.'/resources/views/*') ?: [] as $file) {
            unlink($file);
        }

        rmdir($this->directory.'/resources/views');
        rmdir($this->directory.'/resources');
        rmdir($this->directory);
    }

    public function test_it_reports_unescaped_blade_output_with_location(): void
    {
        $file = $this->directory.'/resources/views/profile.blade.php';
        file_put_contents($file, "<h1>Profile</h1>\n{!! \$user->bio !!}\n");

        $findings = iterator_to_array((new UnescapedBladeRule)->analyze(
            new ScanContext($this->directory, [$file]),
        ));

        self::assertCount(1, $findings);
        self::assertSame('LSEC-XSS-002', $findings[0]->ruleId);
        self::assertSame('resources/views/profile.blade.php', $findings[0]->location->path);
        self::assertSame(2, $findings[0]->location->line);
        self::assertSame(1, $findings[0]->location->column);
    }

    public function test_it_ignores_escaped_output_comments_and_verbatim_blocks(): void
    {
        $file = $this->directory.'/resources/views/safe.blade.php';
        file_put_contents($file, <<<'BLADE'
{{ $user->bio }}
{!! '<strong>Trusted static markup</strong>' !!}
{{-- {!! $commented !!} --}}
@verbatim
{!! clientTemplate !!}
@endverbatim
BLADE);

        $findings = iterator_to_array((new UnescapedBladeRule)->analyze(
            new ScanContext($this->directory, [$file]),
        ));

        self::assertSame([], $findings);
    }
}
