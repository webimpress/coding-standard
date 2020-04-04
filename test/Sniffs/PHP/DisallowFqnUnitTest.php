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
            13 => 3,
            19 => 1,
            21 => 1,
            24 => 1,
            27 => 1,
            29 => 2,
            31 => 1,
            32 => 2,
            35 => 2,
            37 => 1,
            40 => 2,
            43 => 1,
            44 => 2,
            51 => 1,
            52 => 1,
            57 => 1,
            59 => 1,
            61 => 1,
            62 => 1,
            64 => 1,
            68 => 1,
            72 => 1,
            79 => 1,
            81 => 1,
            83 => 1,
            84 => 1,
            86 => 1,
            88 => 2,
            92 => 1,
            96 => 1,
            112 => 2,
            114 => 1,
            115 => 1,
            120 => 1,
            126 => 1,
            127 => 1,
            128 => 1,
            129 => 1,
            130 => 1,
            133 => 1,
            134 => 1,
            135 => 1,
            136 => 1,
            138 => 1,
            141 => 1,
            142 => 1,
            143 => 1,
            145 => 1,
            157 => 2,
            161 => 1,
            169 => 1,
            178 => 1,
            179 => 1,
            180 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
