<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\WhiteSpace;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class NamespaceSeparatorUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 5,
            5 => 2,
            8 => 2,
            9 => 1,
            11 => 2,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
