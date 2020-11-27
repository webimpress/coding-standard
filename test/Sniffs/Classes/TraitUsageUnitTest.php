<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Classes;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class TraitUsageUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            9 => 1,
            11 => 1,
            12 => 1,
            15 => 1,
            17 => 1,
            20 => 2,
            22 => 1,
            25 => 3,
            26 => 3,
            27 => 2,
            36 => 1,
            48 => 1,
            50 => 1,
            55 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
