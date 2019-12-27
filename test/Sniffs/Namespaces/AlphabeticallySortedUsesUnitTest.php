<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Namespaces;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class AlphabeticallySortedUsesUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'AlphabeticallySortedUsesUnitTest.1.inc':
                return [
                    6 => 1,
                ];
            case 'AlphabeticallySortedUsesUnitTest.2.inc':
                return [
                    4 => 1,
                ];
        }

        return [
            5 => 1,
            18 => 1,
            19 => 1,
            20 => 1,
            32 => 1,
            33 => 1,
            37 => 2,
            52 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
