<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\WhiteSpace;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class BraceBlankLineUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            7 => 1,
            10 => 1,
            14 => 1,
            19 => 1,
            21 => 1,
            25 => 1,
            29 => 1,
            34 => 1,
            38 => 1,
            41 => 1,
            45 => 1,
            48 => 1,
            51 => 1,
            53 => 1,
            57 => 1,
            59 => 1,
            62 => 1,
            68 => 1,
            72 => 1,
            74 => 1,
            76 => 1,
            78 => 1,
            80 => 1,
            84 => 1,
            86 => 1,
            88 => 1,
            90 => 1,
            92 => 1,
            96 => 1,
            100 => 2,
            104 => 1,
            106 => 1,
            108 => 1,
            113 => 1,
            115 => 1,
            119 => 2,
            123 => 2,
            127 => 1,
            131 => 1,
            135 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
