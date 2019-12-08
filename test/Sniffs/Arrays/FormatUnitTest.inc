<?php
$a1 = [  'foo', 'bar'  ];
$a2 = [
   'foo',
 'bar',

  function() {

  },
    'elem',
  new \DateTime(),

    // comment
    [ 'a', 'b', 'c' ],
/* comment */ 'val',
    'one', 'two', 'three',
];

$a3 = [ 'one',
    'two'];

$a4 = [
    ];

$a5 = [
];

$a6 = [
    'a', // comment
    [
        'b', 'c', 'd', 'e',
    ],
    'e', ['f','g','h'],
];

function foo () {
    $arr = [
           'a', 'b',

      function () {

     },
        'c',
        // comment
        'd',
        [
            'e','f', 'g' /* comment */
        ], // comment

        new \ArrayObject(
            'a',
            'b', # comment
            [ 'c' ],
            [
                'd', 'e',
              'f']
        )
    ];
}

new \ArrayObject([
    'a','b','c'
], ['d',
    'e'
]);

$fibb = [
  1 => 1, 2 => 1,
   3 => 2, 4 => 3,
    5 => 5,
];

$arr = [

    'elem', 'elem',

];

$a = array_merge([
    ['a' => 'b'],
    ['c' => 'd'],
     ]);

$a = [
    /**
     * Comment
     */
    // 'key' => 'val',
];

$a = [
    'a',

    // b

    'c',
];

$a = [
    'a',

    /*
     multiline comment
     */

    'b',
];

$x = [
    // comment
    'a', 'b',
    'd',


    // comment 1
    'i' => 'd',
    //comment 2
    'j'

    //comment 3

    =>'r',

    'r'
    . 'x ',
    // comment 4
    'x1'

    => 'y1',
];

$a = [
    // COMMENT
];
$a = [  /**/ ];

$t = [

    $a ?? $b        =>   $c ?? $d,

    $e ? $f : $g    =>   $h ? $i : $j,

    static fn ($a)  =>   [$a, $a],

    ($k + $l)       =>   $m

];
