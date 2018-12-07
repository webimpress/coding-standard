<?php

namespace MyApp\MyNamespace;

use MyApp\MyClass as Alias;
use MyApp\Other as Other;
use MyName as myName;
use MyImportedClass;

use function hello as hello /* comment */  ;
use function world as helloWorld;

use const PHP_EOL as EOL;
use const DIRECTORY_SEPARATOR as DIRECTORY_SEPARATOR;

class NewClass
{
    use ATrait, BTrait {
        ATrait::functionA /* c */ as functionA;
        sayHello as protected myProtectedHello;
    }
    use CTrait {
        hello as Hello;
    }

    public function __construct(array $arr)
    {
        foreach ($arr as $k => $v) {
            echo $k . '=>' . $v;
        }
    }
}
