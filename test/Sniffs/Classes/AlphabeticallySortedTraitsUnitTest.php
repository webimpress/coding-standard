<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Classes;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class AlphabeticallySortedTraitsUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            12 => 1,
            36 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
