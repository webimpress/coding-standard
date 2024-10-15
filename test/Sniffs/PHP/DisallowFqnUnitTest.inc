<?php

namespace MyNamespace \ Hello;

use \ArrayObject as AO;
use Foo\BarBaz;
use stdClass;

use function Foo\Bar\fooBar;

use const Foo\Bar\FOO_BAR;

class TheClass extends \ MyNamespace \ Hello \ ParentClass implements \ArrayAccess, \Countable
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

    public function missingSpace()
    {
        return new class () extends \DateTime implements \Countable
        {
            use \MyTrait {
                A::big insteadof \B;
            }

            public function checkInstance($a) : bool
            {
                return $a instanceof \Exception;
            }

            public function conditions(string $a) : ?string
            {
                switch ($a) {
                    case \DateTime::class:
                        return \DateTime::class;
                    case \Countable::class:
                        print \Countable::class;
                        echo \Countable::class;
                        return null;
                    case 'require':
                        require \DateTime::class.'.php';
                        require_once \DateTime::class.'.php';
                        include \DateTime::class.'.php';
                        include_once \DateTime::class.'.php';
                    case 'new':
                        new \DateTime();
                }

                if ($a and \DateTime::class) {
                } elseif ($a or \DateTime::class) {
                } elseif ($a xor \DateTime::class) {}

                throw \RuntimeException::something();
            }
        };
    }

    /**
     * @param \Bar[]\Foo[] $param
     */
    abstract public function invalidTypeFormatIsNotChanged($param);

    public function fnClosure()
    {
        return fn (\InFnClosure\Param1 &$param1) : \InFnClosure\ReturnType1 => $param1;
    }

    /**
     * @return \Foo\BarBaz\MyInterface
     */
    public function method1(object $a)
    {
        return $a;
    }

    /**
     * @param \stdClass $obj
     */
    public function method2($obj)
    {
        return $obj;
    }

    public function method2()
    {
        new \MyNamespace\Foo1\Bar1();
        new \MyNamespace\Foo1\Bar1\Bar2();
        new \Foo\BarBaz\Bar3();
    }

    public function method3(): \DNF\Type1|(\DNF\Type2&\DNF\Type3)
    {
    }

    public function method4(): (\DNF\Type4|\DNF\Type5)&\DNF\Type6
    {
    }

    abstract public function method5(): (\DNF\Type7|\DNF\Type8)&\DNF\Type9;

    private \DNF\Type10&(\DNF\Type11|\DNF\Type12) $prop1;
    private (\DNF\Type13&\DNF\Type14)|\DNF\Type15 $prop2;
}
