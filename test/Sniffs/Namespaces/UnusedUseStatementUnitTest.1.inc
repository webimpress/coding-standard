<?php

use DateTime;
use ArrayObject as AO;

use function count;
use function array_keys as k;

use const T_WHITESPACE;
use const T_ARRAY as TA;

use function MyNamespace\{crt as foo, prt as baz};
use const OtherNamespace\{F as O, B as A};

use function X\func1, Y\func2, Z\func3;
use const X\C1, Y\C2, Z\C3;

use const ConstCaseSensitive\FOO;
use const ConstCaseSensitive\Foo;
use const ConstCaseSensitive\foo;

use function MyNamespace\my_func;
use const MyNamespace\MY_CONST;

count([new DateTime(), T_WHITESPACE]);
k([new AO(), TA]);
foo(A);
func1(func3(C2, Foo));

class MyClass {
    const MY_CONST = 1;
    public function my_func()
    {
    }
}
