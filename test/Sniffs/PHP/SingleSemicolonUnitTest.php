<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\PHP;

use WebimpressCodingStandardTest\Sniffs\TestCase;

class SingleSemicolonUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            5 => 3,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
