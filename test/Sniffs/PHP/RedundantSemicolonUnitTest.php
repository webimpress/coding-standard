<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\PHP;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class RedundantSemicolonUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            4 => 1,
            7 => 1,
            10 => 1,
            13 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
