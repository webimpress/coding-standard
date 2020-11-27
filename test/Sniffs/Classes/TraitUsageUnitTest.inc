<?php
namespace MyApp\MyNamespace;

use Foo\BazTrait;
use MyProject\HelloWorld;

class Foo
{
    use BazTrait,
        \MyTrait;
    use    TooManySpaces;
    use \AnotherTrait    {
        \AnotherTrait::method as methodAlias;
    }
    use \Foo\ ATrait;
    use \BarTrait    ;
    use XTrait  , YTrait
        ;

    use \A, \B {
        \B::smallTalk insteadof \A;
      \A::bigTalk insteadof \B;
    }
    use HelloWorld
    { sayHello as protected;}
    use Hello{ sayHello as private myPrivateHello; }
    use EmptyBrackets {}


    /**
     * @param mixed $x
     */
    public function __construct($x)
    {
        $anonymousClass = new class {
            use BTrait,ATrait;
        };

        $closure = function () use ($x) {};
    }
}

class TraitsOnTop
{
    /** @var int */
    var $prop1 = 1;
    const CONST_1 = 1;
    use Trait1;
    // This is a comment
    use Trait2 {
        m1 as m2;
        m3 insteadof m3;
    }
    static protected int $var = 'value';
    use \MyNamespace\Trait3, Trait4;
}
