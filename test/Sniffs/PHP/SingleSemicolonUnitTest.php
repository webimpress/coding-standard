<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\PHP;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class SingleSemicolonUnitTest extends AbstractTestCase
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
