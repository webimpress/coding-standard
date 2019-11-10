<?php

class ConstBeforePropertyClass
{
    use MyTrait;
    use MyOtherTrait {
        func1 as func2;
    }

    /** property comment **/
    var $property = 'val';

    /** constant comment */
    const CONST_1 = 'const';
}

interface ConstBeforePropertyInterface
{
    /** method comment */
    public function method();

    /** constant comment */
    const CONST_2 = 'const';
}

$a = new class() {
    static $prop;
    const CONST_3 = 'const';
};

$b = new class() {
    /** property comment */
    public static $prop;

    /** method comment */
    public function method()
    {
    }

    /** const comment */
    public const CONST_4 = 'const';
};
