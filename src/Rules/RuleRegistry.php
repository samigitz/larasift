<?php

declare(strict_types=1);

namespace LaraSift\Rules;

final readonly class RuleRegistry
{
    /** @param list<Rule> $rules */
    public function __construct(private array $rules) {}

    /** @return list<Rule> */
    public function all(): array
    {
        return $this->rules;
    }

    /**
     * @param  list<string>  $ruleIds
     * @param  list<string>  $categories
     * @return list<Rule>
     */
    public function select(array $ruleIds, array $categories): array
    {
        if ($ruleIds === [] && $categories === []) {
            return $this->rules;
        }

        return array_values(array_filter(
            $this->rules,
            static function (Rule $rule) use ($ruleIds, $categories): bool {
                $metadata = $rule->metadata();

                return in_array($metadata->id, $ruleIds, true)
                    || in_array($metadata->category, $categories, true);
            },
        ));
    }
}
