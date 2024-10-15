<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\WhiteSpace;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class OperatorAndKeywordSpacingUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        if ($testFile === 'OperatorAndKeywordSpacingUnitTest.1.inc') {
            return [];
        }

        return [
            6 => 1,
            8 => 1,
            13 => 2,
            14 => 2,
            19 => 2,
            23 => 1,
            25 => 1,
            29 => 1,
            33 => 1,
            37 => 1,
            41 => 2,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
