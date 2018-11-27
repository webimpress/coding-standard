<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Classes;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class ConstVisibilityUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            6 => 1,
            15 => 1,
            23 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
