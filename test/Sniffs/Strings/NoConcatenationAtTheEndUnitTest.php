<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Strings;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class NoConcatenationAtTheEndUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            9 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
