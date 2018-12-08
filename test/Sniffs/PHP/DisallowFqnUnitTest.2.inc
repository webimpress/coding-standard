<?php

// multiple namespaces

namespace A {
    use DateTime as DT;

    const CONST_A = 'a';

    function myAFunction(string $a) {
        $dt = new \DateTime();

        return $dt->diff(new DT());
    }
}

namespace B {
    new \DateTime();

    function myBFunction () {
        return \A\myAFunction(\A\CONST_A);
    }
}

namespace C {
    echo \DT::class;
}

namespace D {
    new \DateTime();
}
