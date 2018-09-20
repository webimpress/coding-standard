<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Arrays;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class DoubleArrowUnitTest extends AbstractTestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'DoubleArrowUnitTest.1.inc':
                return [
                    4 => 1,
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
        }

        return [
            3 => 1,
            5 => 1,
            9 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
