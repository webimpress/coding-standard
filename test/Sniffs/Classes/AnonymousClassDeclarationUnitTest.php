<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Classes;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class AnonymousClassDeclarationUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'AnonymousClassDeclarationUnitTest.1.inc':
                return [
                    5 => 1,
                    6 => 1,
                    9 => 1,
                    10 => 1,
                    13 => 1,
                    16 => 1,
                    19 => 1,
                    22 => 5,
                    23 => 1,
                    26 => 1,
                    27 => 1,
                    28 => 2,
                    32 => 1,
                    34 => 1,
                    40 => 2,
                    50 => 1,
                    51 => 1,
                    52 => 1,
                    53 => 1,
                    54 => 1,
                    55 => 1,
                    58 => 2,
                    59 => 2,
                    60 => 2,
                    61 => 2,
                    64 => 2,
                    67 => 1,
                    68 => 1,
                    70 => 1,
                    71 => 1,
                    75 => 1,
                    79 => 1,
                    83 => 1,
                    84 => 4,
                    88 => 3,
                    91 => 1,
                    92 => 2,
                    95 => 4,
                    96 => 2,
                    100 => 1,
                ];
        }

        return [
            6 => 1,
            9 => 1,
            10 => 1,
            13 => 2,
            16 => 3,
            22 => 4,
            23 => 1,
            27 => 1,
            28 => 2,
            31 => 1,
            32 => 1,
            34 => 1,
            39 => 1,
            40 => 3,
            51 => 1,
            52 => 1,
            53 => 1,
            54 => 1,
            55 => 1,
            58 => 1,
            59 => 2,
            60 => 2,
            61 => 2,
            64 => 1,
            67 => 1,
            70 => 1,
            75 => 2,
            79 => 1,
            80 => 1,
            84 => 4,
            88 => 2,
            92 => 2,
            95 => 3,
            96 => 2,
            99 => 2,
            100 => 1,
            104 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
