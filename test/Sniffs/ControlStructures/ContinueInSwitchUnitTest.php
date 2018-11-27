<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\ControlStructures;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class ContinueInSwitchUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            7 => 1,
            9 => 1,
            11 => 1,
            13 => 1,
            15 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
