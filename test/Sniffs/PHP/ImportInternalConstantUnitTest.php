<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\PHP;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class ImportInternalConstantUnitTest extends AbstractTestCase
{
    public function getErrorList(string $testFile = '') : array
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
        }

        return [
            5 => 1,
            7 => 1,
            8 => 2,
            21 => 1,
            26 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
