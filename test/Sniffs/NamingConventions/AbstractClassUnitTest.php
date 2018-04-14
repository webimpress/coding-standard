<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\NamingConventions;

use WebimpressCodingStandardTest\Sniffs\TestCase;

class AbstractClassUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            8 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
