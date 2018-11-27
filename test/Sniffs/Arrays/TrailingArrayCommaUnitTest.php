<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Arrays;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class TrailingArrayCommaUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            11 => 1,
            14 => 1,
            17 => 1,
            22 => 1,
            25 => 1,
            26 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
