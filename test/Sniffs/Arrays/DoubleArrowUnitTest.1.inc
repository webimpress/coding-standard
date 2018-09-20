<?php
// @phpcs:set WebimpressCodingStandard.Arrays.DoubleArrow maxPadding 30

$s = ['a'    => 'b', 'c'=>'d'];
$m = [
    'a'    =>
        'b',
    'c'
        => 'd',
    'e'    => 'f',
];

$c = [
    'a' => 'b',
    'aa' => 'bb',
    'aaa' => 'bbb',
    'aaaa' => 'bbbb',
    'bbbbb',
    'ccccc' => 'ddddd',
    'ccc' => 'ddd',
    'c' => 'd',
    'cccccccccc' => 'cccccccccc',
];

$serializedRequest = [
    'method' => 'POST',
    'request_target' => '/foo/bar?baz=bat',
    'uri' => 'http://example.com/foo/bar?baz=bat',
    'protocol_version' => '1.1',
    'headers' => [
        'Host'         => ['example.com'],
        'Accept'       => ['application/json'],
        'X-Foo-Bar'    => [
            'Baz',
            'Bat',
        ],
    ],

    'body'   => '{"test":"value"}',
];

$x = [
    '1' => 1,
    '1_____' => 1,
    '1__' => 1,
    '2_____________________________________________________' => 2,
    '3' => 3,
    '3____' => 3,
    '3_' => 3,
    '3_______' => 3,
    '3____________________' => 3,
    '4_'
    => 'value in new line breaks aligning group',
    '5' => 5,
    '5__' => 5,
    '5_____' => 5,
    'value without index breaks aligning group',
    '6___' => 6,
    '6' => 6,

    '7_'   => 'empty line breaks alignment group',
    // comment
    '8_____' => 'comment breaks alignment group',
];
