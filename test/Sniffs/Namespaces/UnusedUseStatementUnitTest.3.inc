<?php

namespace First {
    use Unused\Hello;
    use Unused\Foo;
    use Unused\Baz;
    use Used\A;
    use Used\B;
    use Used\C;
    use Used\Bar;
    use Used\Car;
    use Used\Exception1;
    use Used\Exception2;

    use function Unused\first_func;
    use function Unused\bar;
    use function Unused\car;

    use const Unused\BAR;
    use const Unused\Bar;
    use const Unused\bar;
    use const Used\Baz;
    use const Unused\car;
    use const Unused\me;
    use const Unused\other;

    class FirstClass
    {
        use SomeTrait {
            car as me;
            Abc::method as hello;
            Def::other insteadof foo;
        }

        const BAR = 1;

        public function first_func(?A $a, B ...$b) : C
        {
        }

        public function bar() : string
        {
            return Bar::class;
        }

        public function car() : string
        {
            return new Car(Baz);
        }

        public function exception() : void
        {
            try {
            } catch (Exception1|Exception2|\GlobalException $ex) {
            }
        }
    }
}

namespace Second {
    use First\FirstClass;
    use FirstClass\Something;
    use Used\Extend1;
    use Used\Extend2;
    use Used\Extend3;
    use Used\Interface1;
    use Used\Interface2;
    use Used\InstanceOf1;

    use const Space\A;
    use const Space\B;

    $a = A|B|\Ns\C;

    interface I extends Extend1, Extend2 {}
    class C extends Extend3 implements Interface1, Interface2 {}

    $b = $a instanceof InstanceOf1;
}
