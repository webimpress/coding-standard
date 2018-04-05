<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\NamingConventions;

use WebimpressCodingStandardTest\Sniffs\TestCase;

class ValidVariableNameUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            15 => 1,
            16 => 1,
            28 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
