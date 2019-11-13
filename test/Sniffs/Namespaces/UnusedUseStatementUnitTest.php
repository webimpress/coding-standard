<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Namespaces;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class UnusedUseStatementUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'UnusedUseStatementUnitTest.1.inc':
                return [
                    3 => 1,
                    6 => 1,
                    9 => 1,
                    12 => 1,
                    13 => 1,
                    15 => 1,
                    16 => 2,
                    18 => 1,
                    20 => 1,
                    22 => 1,
                    23 => 1,
                ];
            case 'UnusedUseStatementUnitTest.2.inc':
                return [
                    7 => 1,
                    8 => 1,
                    9 => 1,
                    10 => 1,
                    11 => 1,
                    12 => 1,
                ];
            case 'UnusedUseStatementUnitTest.3.inc':
                return [
                    4 => 1,
                    5 => 1,
                    6 => 1,
                    15 => 1,
                    16 => 1,
                    17 => 1,
                    19 => 1,
                    20 => 1,
                    21 => 1,
                    23 => 1,
                    24 => 1,
                    25 => 1,
                    61 => 1,
                    62 => 1,
                ];
        }

        return [
            6 => 1,
            11 => 1,
            13 => 1,
            19 => 1,
            20 => 1,
            21 => 1,
            26 => 2,
            32 => 1,
            33 => 1,
            34 => 1,
            35 => 1,
            36 => 1,
            37 => 1,
            38 => 1,
            39 => 1,
            45 => 1,
            46 => 1,
            47 => 1,
            54 => 2,
            56 => 1,
            58 => 1,
            60 => 2,
            61 => 1,
            62 => 1,
            63 => 1,
            64 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
