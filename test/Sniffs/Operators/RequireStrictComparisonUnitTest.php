<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Operators;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class RequireStrictComparisonUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            6 => 1,
            9 => 2,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
