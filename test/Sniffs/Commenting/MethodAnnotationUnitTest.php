<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Commenting;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class MethodAnnotationUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'MethodAnnotationUnitTest.1.inc':
                return [];
            case 'MethodAnnotationUnitTest.2.inc':
                return [
                    9 => 1,
                    19 => 1,
                ];
        }

        return [
            8 => 1,
            9 => 1,
            18 => 1,
            27 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
