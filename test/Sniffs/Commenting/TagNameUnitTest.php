<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Commenting;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class TagNameUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            4 => 1,
            5 => 1,
            6 => 1,
            7 => 1,
            8 => 1,
            12 => 1,
            16 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
