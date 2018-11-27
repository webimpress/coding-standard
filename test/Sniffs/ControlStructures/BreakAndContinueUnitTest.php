<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\ControlStructures;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class BreakAndContinueUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            10 => 1,
            14 => 1,
            18 => 1,
            22 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
