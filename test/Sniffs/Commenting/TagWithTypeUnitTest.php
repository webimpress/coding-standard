<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Sniffs\Commenting;

use WebimpressCodingStandardTest\Sniffs\AbstractTestCase;

class TagWithTypeUnitTest extends AbstractTestCase
{
    protected function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'TagWithTypeUnitTest.1.inc':
                return [
                    12 => 1,
                    17 => 1,
                    22 => 1,
                    27 => 1,
                    32 => 1,
                    47 => 2,
                    52 => 1,
                    57 => 1,
                    62 => 1,
                    67 => 1,
                    72 => 1,
                    77 => 1,
                    82 => 1,
                    87 => 1,
                    92 => 1,
                    107 => 1,
                    112 => 1,
                    117 => 1,
                    122 => 2,
                    127 => 1,
                    132 => 1,
                    137 => 1,
                    142 => 1,
                    147 => 1,
                    152 => 1,
                    157 => 1,
                    162 => 1,
                    167 => 1,
                    172 => 1,
                    177 => 1,
                    182 => 1,
                    187 => 1,
                    192 => 1,
                    202 => 1,
                    207 => 1,
                    212 => 1,
                    217 => 1,
                    222 => 1,
                    227 => 1,
                    232 => 1,
                    237 => 1,
                    242 => 1,
                    252 => 1,
                    272 => 1,
                    277 => 1,
                    282 => 1,
                    301 => 1,
                    309 => 1,
                    324 => 1,
                    325 => 1,
                ];
            case 'TagWithTypeUnitTest.2.inc':
                return [
                    12 => 1,
                    22 => 1,
                    27 => 1,
                    32 => 1,
                    47 => 2,
                    52 => 1,
                    57 => 1,
                    62 => 1,
                    67 => 1,
                    72 => 1,
                    77 => 1,
                    82 => 1,
                    87 => 1,
                    92 => 1,
                    107 => 1,
                    112 => 1,
                    117 => 1,
                    122 => 1,
                    127 => 1,
                    132 => 1,
                    137 => 1,
                    142 => 1,
                    147 => 1,
                    152 => 1,
                    157 => 1,
                    162 => 1,
                    167 => 1,
                    172 => 1,
                    177 => 1,
                    182 => 1,
                    187 => 1,
                    202 => 1,
                    207 => 1,
                    212 => 2,
                    222 => 1,
                    227 => 1,
                    232 => 1,
                    237 => 1,
                    242 => 1,
                    247 => 1,
                    252 => 1,
                    257 => 1,
                    262 => 1,
                    267 => 1,
                    277 => 1,
                    282 => 1,
                    287 => 1,
                    292 => 1,
                    297 => 2,
                    302 => 2,
                    307 => 1,
                    327 => 1,
                    332 => 1,
                    337 => 1,
                    353 => 1,
                    361 => 1,
                    376 => 1,
                    381 => 1,
                    386 => 1,
                    391 => 1,
                ];
            case 'TagWithTypeUnitTest.3.inc':
                return [
                    12 => 1,
                    22 => 1,
                    27 => 1,
                    32 => 1,
                    47 => 2,
                    52 => 1,
                    57 => 1,
                    62 => 1,
                    67 => 1,
                    72 => 1,
                    77 => 1,
                    82 => 1,
                    87 => 1,
                    92 => 1,
                    107 => 1,
                    112 => 1,
                    117 => 1,
                    122 => 2,
                    127 => 1,
                    132 => 1,
                    137 => 1,
                    142 => 1,
                    147 => 1,
                    152 => 1,
                    157 => 1,
                    162 => 1,
                    167 => 1,
                    172 => 1,
                    177 => 1,
                    182 => 1,
                    192 => 1,
                    197 => 1,
                    202 => 1,
                    207 => 1,
                    212 => 1,
                    217 => 1,
                    222 => 1,
                    227 => 1,
                    232 => 1,
                    242 => 1,
                    255 => 1,
                    258 => 1,
                    261 => 1,
                    265 => 1,
                    269 => 1,
                    272 => 1,
                    281 => 1,
                    282 => 1,
                    290 => 1,
                    295 => 1,
                    299 => 1,
                    304 => 1,
                    309 => 1,
                    314 => 1,
                ];
        }

        return [];
    }

    protected function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}
