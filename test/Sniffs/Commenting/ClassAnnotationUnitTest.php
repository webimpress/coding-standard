<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Commenting;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class ClassAnnotationUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'ClassAnnotationUnitTest.1.inc':
                return [];
            case 'ClassAnnotationUnitTest.2.inc':
                return [
                    8 => 1,
                    13 => 1,
                    44 => 1,
                ];
            case 'ClassAnnotationUnitTest.3.inc':
                return [
                    4 => 1,
                ];
        }

        return [
            4 => 1,
            5 => 1,
            6 => 1,
            8 => 1,
            10 => 1,
            20 => 1,
            35 => 1,
            44 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
