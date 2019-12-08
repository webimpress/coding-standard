<?php
// @phpcs:set WebimpressCodingStandard.Arrays.DoubleArrow maxPadding 1
// @phpcs:set WebimpressCodingStandard.Arrays.DoubleArrow ignoreNewLineArrayArrow true

$s = ['a'    => 'b', 'c'=>'d'];
$m = [
    'a'    =>
        'b',
    'c'
        => 'd',
    'e'    => 'f',
];

$t = [
    $a ?? $b        =>   $c ?? $d,
    $e ? $f : $g    =>   $h ? $i : $j,
    static fn ($a)  =>   [$a, $a],
    ($k + $l)       =>   $m
];
