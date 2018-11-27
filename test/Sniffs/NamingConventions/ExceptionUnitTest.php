<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\NamingConventions;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class ExceptionUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            12 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
