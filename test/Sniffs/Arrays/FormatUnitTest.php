<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Arrays;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class FormatUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            2 => 2,
            6 => 1,
            14 => 2,
            16 => 2,
            19 => 1,
            20 => 1,
            22 => 1,
            25 => 1,
            31 => 3,
            33 => 1,
            38 => 1,
            39 => 1,
            47 => 2,
            49 => 1,
            53 => 2,
            55 => 1,
            56 => 1,
            62 => 2,
            63 => 1,
            68 => 1,
            69 => 1,
            74 => 1,
            75 => 1,
            76 => 1,
            95 => 1,
            105 => 1,
            111 => 1,
            120 => 1,
            123 => 1,
            128 => 1,
            135 => 2,
            138 => 1,
            140 => 1,
            142 => 1,
            144 => 1,
            146 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
