<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Commenting;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class PlacementUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            4 => 1,
            6 => 3,
            8 => 3,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
