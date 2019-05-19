<?php

namespace MyNamespace\Test;

class TagWithType
{
    /**
     * @var array|bool|callable|float|int|iterable|object|parent|resource|self|static|string|MyClass
     */
    public $typesOrder;

    /** @var \Property_Type */
    public $className;

    /**
     * @param \Param_Type $param
     * @return \Return_Type
     * @throws \Exception_Name
     */
    public function test(\Param_Type $param) : \Return_Type
    {
    }
}
