<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Functions;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class NullableTypehintUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'NullableTypehintUnitTest.1.inc':
                return [
                    7 => 1,
                    17 => 1,
                    23 => 1,
                ];
        }

        return [
            6 => 1,
            9 => 1,
            16 => 1,
            19 => 1,
            23 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
