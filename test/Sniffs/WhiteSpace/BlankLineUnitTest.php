<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\WhiteSpace;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class BlankLineUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            2 => 1,
            6 => 1,
            10 => 1,
            11 => 1,
            18 => 1,
            26 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
