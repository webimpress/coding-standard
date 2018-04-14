<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\NamingConventions;

use WebimpressCodingStandardTest\Sniffs\TestCase;

class TraitUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            7 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
