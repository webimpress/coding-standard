<?php

if (1) {
    echo 1;
}

function x(
    $a,
    $b
) {
    return $a * $b;
}

$f = function (
    $a,
    $b
) {
    return $a + $b;
};

try {
    $f(1, 2);
} catch (Exception $exception
) {
    echo $exception->getMessage();
}

class MyClass
{
}

function y($a, $b) {
    if ($a
    || $b) {
        return $a;
    } elseif ($a
        && $b
        ) {
        return $b;
    } elseif ($a
       xor $b
) {
        return $a - $b;
    } elseif ($a
         || $b) {
        return $a;
    } elseif ($a
           && $b) {
        return $b;
    }

    return $a + $b;
}

while (1
|| 2) {
    continue;
}

if ($a
&& ($b
|| $c)
&& preg_match(
    '/a/',
    'b'
)
) {
    return 12;
}

if (false === strpos(
    'haystack',
    'needle'
)
) {
    echo 0;
}

if (false === $method(
    'value'
)
) {
    echo 1;
}

if ($a
!== $b
    && $c
instanceof \DateTime
) {
    echo 1;
}

if ('x' === trim(sprintf(
    'a %s',
    'b'
)
)
) {
    echo 2;
}

if (true === strpos(
    'a',
    'b'
)  ) {
    echo 3;
}

if ($a && ($b || $c
)) {
    echo 4;
}

if ($a && ($b
    || $c
)) {
    echo 5;
}

$a = 'string';
$v = $a{0};
$z = $a->{$v};

if ($a
    && ($b
        || $c
    )
) {
    echo 1;
}

if ($a
    && ($b
        || $c
    ) && ($d
        || $e
    )
) {
    echo 2;
}

if ($a
    && ($b
        || $c
    )
    && ($d
        || $e
    )
) {
    echo 3;
}

while (in_array($a, [
    $b,
    $c,
    $d,
], true)) {
}

if (in_array($a, array(
    $b,
    $c
), true)) {
} elseif (myFunc($a, anotherFunc(
    $d,
    $e
), true)) {
}

do {
} while (myFunc($a, function (
    $b,
    $c
) {
    return $b <=> $c;
}, true));

if (myFunc([
    'elem1',
    'elem2',
], function (
    $a,
    $b
) {
    return $a > $b;
}, [
    'param' => 'val'
])) {
} elseif (($a
        && $b)
    || ($c
        && $d)
    || ($e
        && $f
        && $g)
) {
}

do {
} while ($a
    && ($b
        || $c
        || $d)
);

if ($a
    && (($b
            || $c
            || $d)
        && $e
        || ($f && $g))
) {
} elseif ($a
    && ((($b
                || $c
                || $d)
            && $e)
        || ($f && $g))
) {
} elseif ($a
    && (($x && ($b
                || $c
                || $d)
            && $e)
        || ($f && $g))
) {
}

if ($a ||
    ($b && $c)
) {
}

if ($a || // comment
    ($b && $c)
) {
}

if ($a ||
    ((($b && $c)))
) {
}
