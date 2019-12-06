<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Arrays;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class DoubleArrowUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'DoubleArrowUnitTest.1.inc':
                return [
                    4 => 2,
                    6 => 1,
                    10 => 1,
                    14 => 1,
                    15 => 1,
                    16 => 1,
                    19 => 1,
                    20 => 1,
                    21 => 1,
                    26 => 1,
                    27 => 1,
                    28 => 1,
                    30 => 1,
                    31 => 1,
                    32 => 1,
                    33 => 1,
                    39 => 1,
                    43 => 1,
                    45 => 1,
                    47 => 1,
                    48 => 1,
                    49 => 1,
                    50 => 1,
                    54 => 1,
                    55 => 1,
                    59 => 1,
                    61 => 1,
                ];
            case 'DoubleArrowUnitTest.2.inc':
                return [
                    6 => 1,
                    8 => 1,
                    13 => 1,
                    15 => 1,
                    17 => 1,
                    18 => 1,
                    19 => 1,
                    20 => 1,
                    23 => 1,
                    24 => 1,
                    25 => 1,
                    28 => 1,
                    30 => 1,
                ];
        }

        return [
            5 => 2,
            7 => 1,
            11 => 1,
            15 => 1,
            16 => 1,
            18 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
