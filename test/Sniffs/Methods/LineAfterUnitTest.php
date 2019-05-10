<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Methods;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class LineAfterUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'LineAfterUnitTest.1.inc':
                return [
                    5 => 1,
                    8 => 1,
                    13 => 1,
                    14 => 2,
                    15 => 1,
                ];
            case 'LineAfterUnitTest.2.inc':
                return [
                    7 => 1,
                    10 => 1,
                    15 => 1,
                    20 => 1,
                    21 => 2,
                    22 => 1,
                    24 => 1,
                ];
            case 'LineAfterUnitTest.3.inc':
                return [
                    6 => 1,
                    9 => 1,
                    14 => 1,
                    19 => 1,
                    20 => 2,
                    21 => 1,
                ];
        }

        return [
            7 => 1,
            10 => 1,
            15 => 1,
            20 => 1,
            21 => 2,
            22 => 1,
            24 => 1,
            29 => 1,
            32 => 1,
            38 => 1,
            47 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
