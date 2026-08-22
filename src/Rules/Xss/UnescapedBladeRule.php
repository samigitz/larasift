<?php

declare(strict_types=1);

namespace LaraSift\Rules\Xss;

use LaraSift\Finding\Confidence;
use LaraSift\Finding\Finding;
use LaraSift\Finding\Location;
use LaraSift\Finding\Severity;
use LaraSift\Rules\Rule;
use LaraSift\Rules\RuleMetadata;
use LaraSift\Scanning\ScanContext;

final class UnescapedBladeRule implements Rule
{
    public function metadata(): RuleMetadata
    {
        return new RuleMetadata(
            'LSEC-XSS-002',
            'Unescaped Blade output',
            'xss',
            Severity::Medium,
        );
    }

    public function analyze(ScanContext $context): iterable
    {
        foreach ($context->files as $file) {
            if (! str_ends_with($file, '.blade.php')) {
                continue;
            }

            $contents = file_get_contents($file);

            if ($contents === false) {
                continue;
            }

            $scannable = $this->maskIgnoredRegions($contents);
            preg_match_all('/\{!!\s*(.+?)\s*!!\}/s', $scannable, $matches, PREG_OFFSET_CAPTURE);

            foreach ($matches[0] as $index => [$match, $offset]) {
                $metadata = $this->metadata();
                $line = substr_count(substr($contents, 0, $offset), "\n") + 1;
                $lastNewline = strrpos(substr($contents, 0, $offset), "\n");
                $column = $offset - ($lastNewline === false ? -1 : $lastNewline);
                $expression = trim($matches[1][$index][0]);

                if ($this->isStaticString($expression)) {
                    continue;
                }

                yield new Finding(
                    $metadata->id,
                    $metadata->title,
                    'Dynamic content is rendered without HTML escaping.',
                    $metadata->severity,
                    Confidence::Medium,
                    new Location($context->relativePath($file), $line, $column),
                    $this->singleLine($match),
                    sprintf('Use {{ %s }} unless this value is sanitized trusted HTML.', $expression),
                );
            }
        }
    }

    private function maskIgnoredRegions(string $contents): string
    {
        return (string) preg_replace_callback(
            ['/{{--.*?--}}/s', '/@verbatim.*?@endverbatim/s'],
            static fn (array $match): string => preg_replace('/[^\n]/', ' ', $match[0]) ?? '',
            $contents,
        );
    }

    private function singleLine(string $value): string
    {
        return trim((string) preg_replace('/\s+/', ' ', $value));
    }

    private function isStaticString(string $expression): bool
    {
        if (strlen($expression) < 2) {
            return false;
        }

        $quote = $expression[0];

        return in_array($quote, ['\'', '"'], true)
            && str_ends_with($expression, $quote)
            && ! str_contains($expression, '$');
    }
}
