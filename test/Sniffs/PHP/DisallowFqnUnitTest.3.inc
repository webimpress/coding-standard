<?php

namespace MyNamespace;

use ImportedClass;

/**
 * Class MyClass
 *
 * @package MyNamespace
 * @property \MyNamespace\Date $date
 * @property \MyNamespace\Service\DateService $dateService
 * @property-read \DateTime $dateTime
 * @property-write \ArrayObject
 */
class MyClass
{
    /**
     * @var null|Something|\ArrayAccess
     */
    public $property;

    /**
     * @param self|\FooBar\Baz $param
     * @throws \Exception|\Error
     */
    public function __construct(self $param)
    {
    }

    /**
     * @param \OtherNamespace\ImportedClass $param
     * @return object|\OtherName\ReturnType
     */
    public function withReturn(\OtherNamespace\ImportedClass $param)
    {
        return $x;
    }

    /**
     * @param \DateTime[]|\ArrayObject[]
     * @return object[]|\Traversable
     */
    public function arrayTypes(array $arr) : array
    {
        return $arr;
    }
}
