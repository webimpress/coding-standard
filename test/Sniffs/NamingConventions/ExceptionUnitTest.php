<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\NamingConventions;

use WebimpressCodingStandardTest\Sniffs\TestCase;

class ExceptionUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            12 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
