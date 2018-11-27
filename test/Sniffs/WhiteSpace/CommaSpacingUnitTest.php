<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\WhiteSpace;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class CommaSpacingUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            5 => 1,
            7 => 1,
            10 => 2,
            12 => 1,
            14 => 2,
            28 => 2,
            30 => 2,
            34 => 2,
            38 => 2,
            41 => 1,
            44 => 1,
            48 => 2,
            53 => 2,
            54 => 3,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
