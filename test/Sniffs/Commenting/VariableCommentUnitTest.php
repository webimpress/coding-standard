<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Commenting;

use WebimpressCodingStandardTest\Sniffs\TestCase;

class VariableCommentUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            4 => 1,
            9 => 1,
            11 => 1,
            29 => 1,
            34 => 1,
            43 => 1,
            51 => 2,
            54 => 2,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
