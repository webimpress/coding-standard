<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Namespaces;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class UniqueImportUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'UniqueImportUnitTest.1.inc':
                return [
                    7 => 1,
                    11 => 1,
                    12 => 1,
                    15 => 1,
                    19 => 1,
                    23 => 1,
                ];
            case 'UniqueImportUnitTest.2.inc':
                return [
                    6 => 1,
                    10 => 1,
                    11 => 1,
                    14 => 1,
                    18 => 1,
                    22 => 1,
                ];
        }

        return [
            5 => 1,
            9 => 1,
            10 => 1,
            13 => 1,
            17 => 1,
            21 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
