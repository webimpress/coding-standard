<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\ControlStructures;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class DefaultAsLastUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            14 => 1,
            18 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
