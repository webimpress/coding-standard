<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Commenting;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class NoInlineCommentAfterCurlyCloseUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            10 => 1,
            11 => 1,
            12 => 1,
            24 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
