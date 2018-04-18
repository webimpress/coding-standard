<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Commenting;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class FunctionCommentUnitTest extends AbstractTestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            7 => 1,
            11 => 1,
            17 => 1,
            30 => 1,
            31 => 1,
            34 => 1,
            37 => 1,
            44 => 1,
            52 => 1,
            67 => 1,
            74 => 1,
            80 => 1,
            96 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
