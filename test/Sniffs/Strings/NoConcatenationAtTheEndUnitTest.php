<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Strings;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class NoConcatenationAtTheEndUnitTest extends AbstractTestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            9 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
