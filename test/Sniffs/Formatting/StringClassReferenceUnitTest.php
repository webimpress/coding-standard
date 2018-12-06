<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Formatting;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class StringClassReferenceUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            5 => 1,
            7 => 1,
            8 => 1,
            10 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
