<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Formatting;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class ReferenceUnitTest extends AbstractTestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            5 => 1,
            6 => 2,
            7 => 1,
            8 => 1,
            13 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
