<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Formatting;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class ReturnTypeUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        if ($testFile === 'ReturnTypeUnitTest.1.inc') {
            return [
                5 => 1,
                6 => 2,
                8 => 2,
                9 => 2,
                15 => 2,
                16 => 2,
                18 => 1,
                19 => 2,
            ];
        }

        return [
            7 => 1,
            11 => 1,
            29 => 2,
            38 => 1,
            44 => 2,
            52 => 2,
            57 => 2,
            58 => 2,
            59 => 2,
            60 => 3,
            61 => 2,
            62 => 2,
            63 => 1,
            64 => 2,
            65 => 1,
            66 => 2,
            67 => 1,
            68 => 2,
            69 => 1,
            70 => 2,
            71 => 1,
            72 => 2,
            73 => 1,
            74 => 2,
            75 => 1,
            76 => 2,
            77 => 1,
            79 => 2,
            80 => 1,
            82 => 1,
            83 => 2,
            88 => 1,
            91 => 1,
            93 => 1,
            95 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
