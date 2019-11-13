<?php

class PropertyBeforeMethodClass
{
    use MyTrait;
    use MyOtherTrait {
        func1 as func2;
    }

    /** method comment */
    public function method()
    {
    }

    /** constant comment */
    const CONST_1 = 'const';

    /** property comment **/
    var $property1 = 'val';
}

trait PropertyBeforeMethodTrait
{
    /** method1 comment */
    public function method1() {}

    /** method2 comment */
    public function method2() {}

    /** property2 comment */
    public ?MyName\MyClass $property2;

    /** property3 comment */
    private int $property3 = 12;
}

$a = new class() {
    /** method comment */
    public function __construct()
    {
    }

    /** property comment */
    protected static $prop;
    const CONST_3 = 'const';
};

$b = new class() {
    /** method comment */
    public function method()
    {
    }

    /** property comment */
    public static $prop;

    /** const comment */
    public const CONST_4 = 'const';
};
