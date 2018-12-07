<?php

namespace MyNamespace \ Hello;

use \ArrayObject as AO;

use function Foo\Bar\fooBar;

use const Foo\Bar\FOO_BAR;

class TheClass extends \ MyNamespace \ Hello \ ParentClass implements \ArrayAccess
{
    use MyTrait;

    private $a;

    public function __construct(\DateTime $dateTime)
    {
        $this->a = new \MyNamespace\ Hello \A\ClassA();

        // class with the same name exists in the same directory (assumed: namespace)
        new \SomeNamespace\DisallowFqnUnitTest();

        // directory with the same name exists here so it assumes it has the namespace
        new \SomeNamespace\BarBaz\TestAsset();

        (function (\InClosure\Param &$inClosure) use ($dateTime) : \InClosure\ReturnType {
            // the alias is used, so it can't be imported
            new \AO();
        })(\InClosure\CONST_IN_CLOSURE, \InClosure\functionInClosure());
    }

    public function test(\ArrayObject ...$ao) : \ArrayAccess
    {
        throw new \RuntimeException();
    }

    public function catchException(\Throwable $throwable = null, \Name\Space &$space = null)
    {
        try {
            $this->test(new \ArrayObject());
        } catch (\Exception | \Error $ex) {
            throw $ex;
        }
    }

    public function doubleColon()
    {
        \MyNamespace\Hello\MyClass::class;
        \MyNamespace\Other\OtherClass::CLASS_CONST;
    }

    public function functionFromNamespace()
    {
        \MyNamespace \ Hello \ myFunction();

        \MyNamespace\Other\otherFunction();

        \Foo\Bar\fooBar();
        \AnotherNamespace\fooBar();

        \Bar\Baz\barBaz();

        switch (true) {
            case 1:
                \Name\functionInSwitch();
                break;

            case 2 * (3 + 2):
                \Name\otherFunctionInSwitch();
                break;
        }
    }

    public function constantFromNamespace(int $var)
    {
        \MyNamespace\ Hello\ MY_CONST;

        \MyNamespace\Other\OTHER_CONST;

        \Foo\Bar\FOO_BAR;
        \AnotherNamespace\FOO_BAR;

        \Bar\Baz\BAR_BAZ;

        \Name\NAME_CONST | \Name\NAME_CONST & $var;

        switch (true) {
            case 1:
                \Name\CONST_IN_SWITCH;
                break;

            case 2 * (3 + 2):
                \Name\OTHER_CONST_IN_SWITCH;
                break;
        }
    }

    public function internalFunctionAndConstant()
    {
        $arr = array_merge([], []);

        \sort($arr);

        return \T_WHITESPACE + T_ELLIPSIS;
    }
}
