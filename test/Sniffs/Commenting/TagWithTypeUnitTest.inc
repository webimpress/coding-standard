<?php

namespace MyNamespace\Test;

class TagWithType
{
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
