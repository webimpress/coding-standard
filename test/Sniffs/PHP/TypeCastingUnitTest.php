<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\PHP;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class TypeCastingUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            5 => 1,
            7 => 1,
            8 => 1,
            9 => 1,
            15 => 1,
            17 => 1,
            18 => 1,
            20 => 1,
            21 => 1,
            22 => 1,
            28 => 1,
            30 => 1,
            31 => 1,
            33 => 1,
            34 => 1,
            35 => 1,
            37 => 1,
            38 => 1,
            39 => 1,
            41 => 1,
            42 => 1,
            43 => 1,
            44 => 1,
            45 => 1,
            46 => 1,
            47 => 1,
            48 => 1,
            49 => 1,
            50 => 1,
            51 => 1,
            52 => 1,
            54 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
