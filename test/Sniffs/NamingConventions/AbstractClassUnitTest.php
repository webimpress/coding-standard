<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\NamingConventions;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class AbstractClassUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            8 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
