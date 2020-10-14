<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\PHP;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class ImportInternalConstantUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'ImportInternalConstantUnitTest.1.inc':
                return [
                    4 => 1,
                    5 => 1,
                    11 => 1,
                    12 => 1,
                    18 => 1,
                ];
            case 'ImportInternalConstantUnitTest.2.inc':
                return [
                    5 => 1,
                    6 => 1,
                ];
            case 'ImportInternalConstantUnitTest.3.inc':
                return [
                    6 => 1,
                ];
            case 'ImportInternalConstantUnitTest.4.inc':
                return [
                    5 => 1,
                    8 => 1,
                ];
            case 'ImportInternalConstantUnitTest.5.inc':
                return [
                    7 => 1,
                    12 => 1,
                    15 => 1,
                    17 => 1,
                ];
            case 'ImportInternalConstantUnitTest.6.inc':
                return [
                    6 => 5,
                    7 => 5,
                    8 => 5,
                    9 => 5,
                    10 => 5,
                    11 => 5,
                    12 => 5,
                    13 => 5,
                    14 => 5,
                    15 => 5,
                    16 => 5,
                ];
        }

        return [
            5 => 1,
            7 => 1,
            8 => 2,
            21 => 1,
            26 => 1,
            32 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
