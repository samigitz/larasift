<?php

declare(strict_types=1);

namespace LaraSift\Finding;

enum Confidence: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}
