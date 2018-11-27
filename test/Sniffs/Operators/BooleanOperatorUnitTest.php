<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Operators;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class BooleanOperatorUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            6 => 1,
            9 => 1,
            12 => 1,
            15 => 1,
            16 => 1,
            19 => 1,
            27 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
