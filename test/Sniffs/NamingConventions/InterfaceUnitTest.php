<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\NamingConventions;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class InterfaceUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            7 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
