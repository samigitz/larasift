<?php

declare(strict_types=1);

namespace LaraSift\Console;

enum ExitCode: int
{
    case Clean = 0;
    case Findings = 1;
    case InvalidUsage = 2;
    case IncompleteScan = 3;
}
