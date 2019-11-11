<?php

use MyNamespace\MyClass;
use MyNamespace\General;
use OtherNamespace\MyClass as General;

use function MyFunctions\general;
use function array_merge as f1;
use function array_diff as f1;
use function array_diff as f2;
use function
    array_filter,
    array_merge;

use const MyConsts\GENERAL;
use const MyConsts\OTHER as A;
use const MyConsts\FOO as A;
use const Other\{
    CONST1,
    CONST2 as General,
    CONST3 as GENERAL
};
use const Myconsts\OTHER;

class None {
    use MyTrait;

    private $closure;

    public function __construct(MyClass $a) {
        $this->closure = function() use($a) {};
    }
}
