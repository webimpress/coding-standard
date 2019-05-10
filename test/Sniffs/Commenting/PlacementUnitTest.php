<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Commenting;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class PlacementUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            4 => 1,
            5 => 1,
            7 => 3,
            9 => 3,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
