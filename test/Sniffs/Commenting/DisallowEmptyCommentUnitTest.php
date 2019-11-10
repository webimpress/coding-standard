<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Commenting;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class DisallowEmptyCommentUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            6 => 1,
            10 => 1,
            14 => 1,
            16 => 1,
            18 => 1,
            20 => 1,
            23 => 1,
            27 => 1,
            31 => 1,
            36 => 1,
            38 => 1,
            48 => 1,
            53 => 1,
            58 => 1,
            62 => 1,
            65 => 1,
            66 => 1,
            71 => 1,
            75 => 1,
            81 => 1,
            92 => 1,
            94 => 1,
            96 => 1,
            97 => 1,
            99 => 1,
            101 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
