<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\ControlStructures;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class RedundantCaseUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        return [
            8 => 1,
            18 => 1,
            24 => 1,
            38 => 1,
            39 => 1,
            47 => 1,
            54 => 1,
            55 => 1,
            62 => 1,
            66 => 1,
            67 => 1,
            68 => 1,
            78 => 1,
            79 => 1,
            85 => 1,
            86 => 1,
            87 => 1,
            93 => 1,
            98 => 1,
            105 => 1,
            114 => 1,
            121 => 1,
            122 => 1,
            129 => 1,
            130 => 1,
            150 => 1,
            152 => 1,
            156 => 1,
            161 => 1,
            163 => 1,
            168 => 1,
            170 => 1,
            178 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
