<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\WhiteSpace;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class OperatorAndKeywordSpacingUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            4 => 1,
            6 => 1,
            11 => 2,
            12 => 2,
            17 => 2,
            21 => 1,
            23 => 1,
            27 => 1,
            31 => 1,
            35 => 1,
            39 => 2,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
