<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Commenting;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class PropertyAnnotationUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'PropertyAnnotationUnitTest.1.inc':
                return [];
            case 'PropertyAnnotationUnitTest.2.inc':
                return [
                    9 => 1,
                    17 => 1,
                ];
        }

        return [
            8 => 1,
            9 => 1,
            15 => 1,
            24 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
