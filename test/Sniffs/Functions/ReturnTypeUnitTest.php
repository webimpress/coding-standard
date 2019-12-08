<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Functions;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class ReturnTypeUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'ReturnTypeUnitTest.1.inc':
                return [
                    10 => 1,
                    12 => 1,
                    15 => 1,
                    18 => 1,
                    20 => 1,
                    22 => 1,
                    25 => 1,
                    28 => 1,
                    30 => 1,
                    32 => 1,
                    34 => 1,
                    37 => 1,
                    42 => 1,
                    47 => 1,
                    52 => 1,
                    57 => 1,
                    62 => 1,
                    67 => 1,
                    77 => 1,
                    82 => 1,
                    92 => 1,
                    97 => 1,
                    107 => 1,
                    112 => 1,
                    117 => 1, // in theory we can return here another class of the same parent type...
                    122 => 1,
                    127 => 1,
                    // 132 => 1, // There is no error, because return type is invalid
                    134 => 1,
                    137 => 1,
                    142 => 1,
                    147 => 1,
                    152 => 1,
                    167 => 1,
                    182 => 1,
                    197 => 1,
                    202 => 1,
                    207 => 1,
                    // 212 => 1, // There is no error, because return type is invalid
                    214 => 1,
                    217 => 1,
                    222 => 1,
                    227 => 1,
                    237 => 1,
                    242 => 1,
                    252 => 1,
                    257 => 1,
                    262 => 1,
                    267 => 1,
                    272 => 1,
                    // 277 => 1, // There is no error, because return type is invalid
                    279 => 1,
                    282 => 1,
                    287 => 2,
                    292 => 1,
                    297 => 1,
                    304 => 1,
                    311 => 1,
                    316 => 1,
                    321 => 1,
                    326 => 1,
                    331 => 1,
                    361 => 1,
                    366 => 1,
                    371 => 1,
                    376 => 1,
                    381 => 1,
                    386 => 1,
                ];
            case 'ReturnTypeUnitTest.2.inc':
                return [
                    8 => 2,
                    13 => 1,
                    18 => 1,
                    23 => 1,
                    28 => 1,
                    33 => 2,
                    42 => 1,
                    43 => 1,
                    44 => 1,
                    45 => 1,
                    46 => 1,
                    47 => 1,
                    48 => 1,
                    49 => 1,
                    50 => 1,
                    51 => 1,
                    52 => 1,
                    53 => 1,
                    54 => 1,
                    55 => 1,
                    56 => 1,
                    57 => 1,
                    58 => 1,
                    59 => 1,
                    60 => 1,
                    61 => 1,
                    62 => 1,
                    63 => 1,
                    64 => 1,
                    65 => 1,
                    71 => 1,
                    78 => 1,
                    85 => 1,
                    97 => 1,
                    105 => 1,
                    113 => 1,
                    117 => 1,
                    125 => 1,
                    133 => 1,
                    177 => 1,
                    185 => 1,
                    189 => 1,
                    193 => 1,
                    197 => 1,
                    201 => 1,
                    205 => 1,
                    214 => 1,
                    217 => 1,
                    266 => 1,
                    273 => 1,
                    277 => 1,
                    281 => 1,
                    285 => 1,
                    297 => 1,
                    306 => 1,
                    314 => 1,
                    319 => 1,
                    354 => 1,
                    363 => 1,
                    375 => 1,
                    384 => 1,
                    392 => 1,
                    397 => 1,
                    412 => 1,
                    420 => 1,
                    432 => 1,
                    452 => 1,
                    460 => 1,
                    468 => 1,
                    476 => 4,
                ];
            case 'ReturnTypeUnitTest.3.inc':
                return [
                    10 => 1,
                    26 => 1,
                    42 => 1,
                    58 => 1,
                    74 => 1,
                    98 => 1,
                    122 => 1,
                    146 => 1,
                    162 => 1,
                    170 => 1,
                    228 => 1,
                ];
        }

        return [
            8 => 1,
            10 => 1,
            12 => 1,
            16 => 1,
            18 => 1,
            20 => 1,
            27 => 1,
            36 => 1,
            41 => 1,
            46 => 1,
            54 => 1,
            59 => 1,
            95 => 1,
            99 => 1,
            108 => 1,
            119 => 1,
            128 => 1,
            137 => 1,
            150 => 1,
            280 => 2,
            288 => 1,
            363 => 1,
        ];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
