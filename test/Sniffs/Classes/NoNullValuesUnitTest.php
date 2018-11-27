<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Classes;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class NoNullValuesUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            6 => 1,
            7 => 1,
            9 => 1,
            11 => 1,
            13 => 1,
            23 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
