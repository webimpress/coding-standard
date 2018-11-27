<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\PHP;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class InstantiatingParenthesisUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            4 => 1,
            7 => 1,
            14 => 1,
            18 => 1,
            22 => 1,
            23 => 1,
            24 => 1,
            27 => 1,
            29 => 1,
            30 => 1,
            32 => 1,
            33 => 1,
            35 => 1,
            37 => 1,
            40 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
