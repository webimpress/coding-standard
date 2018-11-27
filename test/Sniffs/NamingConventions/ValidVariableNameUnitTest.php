<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\NamingConventions;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class ValidVariableNameUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            15 => 1,
            16 => 1,
            28 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
