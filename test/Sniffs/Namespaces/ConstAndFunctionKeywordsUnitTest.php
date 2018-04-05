<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Namespaces;

use WebimpressCodingStandardTest\Sniffs\TestCase;

class ConstAndFunctionKeywordsUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            4 => 1,
            6 => 2,
            7 => 2,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
