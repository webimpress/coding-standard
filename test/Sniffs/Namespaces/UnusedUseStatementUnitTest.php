<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Namespaces;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class UnusedUseStatementUnitTest extends AbstractTestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [
            6 => 1,
            11 => 1,
            13 => 1,
            19 => 1,
            20 => 1,
            21 => 1,
            32 => 1,
            33 => 1,
            34 => 1,
            35 => 1,
            36 => 1,
            37 => 1,
            38 => 1,
            39 => 1,
        ];
    }
}
