<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\PHP;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class DisallowFqnUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'DisallowFqnUnitTest.1.inc':
                return [
                    5 => 1,
                    7 => 1,
                    9 => 1,
                    12 => 1,
                    13 => 1,
                    14 => 1,
                    16 => 2,
                    19 => 1,
                ];
            case 'DisallowFqnUnitTest.2.inc':
                return [
                    11 => 1,
                    18 => 1,
                    21 => 2,
                    26 => 1,
                    30 => 1,
                ];
            case 'DisallowFqnUnitTest.3.inc':
                return [
                    11 => 3,
                    12 => 1,
                    13 => 1,
                    14 => 1,
                    15 => 1,
                    20 => 1,
                    25 => 1,
                    26 => 1,
                    33 => 1,
                    34 => 1,
                    36 => 1,
                    42 => 1,
                    43 => 1,
                ];
        }

        return [
            11 => 3,
            17 => 1,
            19 => 1,
            22 => 1,
            25 => 1,
            27 => 2,
            29 => 1,
            30 => 2,
            33 => 2,
            35 => 1,
            38 => 2,
            41 => 1,
            42 => 2,
            49 => 1,
            50 => 1,
            55 => 1,
            57 => 1,
            59 => 1,
            60 => 1,
            62 => 1,
            66 => 1,
            70 => 1,
            77 => 1,
            79 => 1,
            81 => 1,
            82 => 1,
            84 => 1,
            86 => 2,
            90 => 1,
            94 => 1,
            110 => 2,
            112 => 1,
            113 => 1,
            118 => 1,
            124 => 1,
            125 => 1,
            126 => 1,
            127 => 1,
            128 => 1,
            131 => 1,
            132 => 1,
            133 => 1,
            134 => 1,
            136 => 1,
            139 => 1,
            140 => 1,
            141 => 1,
            143 => 1,
            155 => 2,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
