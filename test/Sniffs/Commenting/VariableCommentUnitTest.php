<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Commenting;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class VariableCommentUnitTest extends AbstractTestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'VariableCommentUnitTest.1.inc':
                return [];
            case 'VariableCommentUnitTest.2.inc':
                return [
                    9 => 1,
                    17 => 1,
                ];
        }

        return [
            4 => 1,
            9 => 1,
            11 => 1,
            29 => 1,
            34 => 1,
            43 => 1,
            51 => 2,
            54 => 2,
            59 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
