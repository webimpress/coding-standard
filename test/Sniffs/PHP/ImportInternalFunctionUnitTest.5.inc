<?php
// @phpcs:set WebimpressCodingStandard.PHP.ImportInternalFunction exclude[] array_reverse,key

namespace ImportInternalFunction;

use function array_reverse;

$arr = range(1, 10);
$key = \key($arr);

function () use ($arr) {
    $keys = array_keys($arr);
    $key = key($keys);

    return myFunction($arr);
}

array_reverse($arr);
\array_reverse($arr);
