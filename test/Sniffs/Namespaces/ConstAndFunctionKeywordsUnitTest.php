<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Namespaces;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class ConstAndFunctionKeywordsUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            4 => 1,
            6 => 2,
            7 => 2,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
