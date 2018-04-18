<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Namespaces;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class UseDoesNotStartWithBackslashUnitTest extends AbstractTestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            4 => 1,
            5 => 1,
            6 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
