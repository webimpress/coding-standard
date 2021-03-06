<?php

namespace Foo {
    use const D;
    use const C;
    use B;
    use A;
    use function Y;
    use function X;

    $foo = 1;
    $closure = function ($x) use ($foo) {
    };
}

namespace Bar {
    use A;
    use function B;
    use const C;
    use D;
    use function E;
    use const F;

    class J {
        use MyTrait;
    }
}

namespace C {
    use ArrayObject;

    use DateTime;
    use function array_keys;



    use const PHP_EOL; use const PHP_VERSION_ID;
}

namespace D {
    use Psr\NamespaceSeparator;
    use PSR7Number;
    use Psr_Underscore;
    use PsrLetter;
}

namespace E {
    use Foo;

    class Bar {}

    use Baz;
}
