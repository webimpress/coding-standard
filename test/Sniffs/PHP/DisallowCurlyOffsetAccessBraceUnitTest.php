<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\PHP;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class DisallowCurlyOffsetAccessBraceUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            7 => 1,
            8 => 2,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
