<?php

namespace MyNamespace\Test\Functions;

class FunctionParam
{
    /**
     * @param mixed $x
     */
    public function test(int $x) {}

    /**
     * @param mixed $x
     */
    public function test(MyObj $x) {}

    /**
     * @param mixed[] $x
     */
    public function test(MyObj $x) {}

    /**
     * @param mixed $x
     */
    public function test(\Generator $x) {}

    /**
     * @param mixed[] $x
     */
    public function test(\Generator $x) {}

    /**
     * @param int $x
     */
    public function test(\Generator $x) {}

    /**
     * @param MyObj $x
     */
    public function test(\Generator $x) {}

    /**
     * @param mixed $x
     */
    public function test(\Traversable $x) {}

    /**
     * @param mixed[] $x
     */
    public function test(\Traversable $x) {}

    /**
     * @param int $x
     */
    public function test(\Traversable $x) {}

    /**
     * @param MyObj $x
     */
    public function test(\Traversable $x) {}

    /**
     * @param mixed $x
     */
    public function test(iterable $x) {}

    /**
     * @param mixed[] $x
     */
    public function test(iterable $x) {}

    /**
     * @param int $x
     */
    public function test(iterable $x) {}

    /**
     * @param MyObj $x
     */
    public function test(iterable $x) {}

    /**
     * @param mixed $x
     */
    public function test(array $x) {}

    /**
     * @param mixed[] $x
     */
    public function test(array $x) {}

    /**
     * @param int $x
     */
    public function test(array $x) {}

    /**
     * @param MyObj $x
     */
    public function test(array $x) {}

    /**
     * @param array|callable $a
     */
    public function paramArrayWithOtherTypes($a) {}

    /**
     * @param callable|iterable $a
     */
    public function paramIterableWithOtherTypes($a) {}

    /**
     * @param int|\Traversable $a
     */
    public function paramTraversableWithOtherTypes($a) {}

    /**
     * @param float|\Generator $a
     */
    public function paramGeneratorWithOtherTypes($a) {}

    /**
     * @param
     */
    public function missingParamTypeAndName() {};

    /**
     * @param $a
     */
    public function missingParamType($a) {}

    /**
     * @param int
     */
    public function missingParamName($a) {}

    /**
     * @param int,string $a
     */
    abstract public function invalidType($a);

    /**
     * @param Other_Param $Foo
     */
    public function withUnderscore(With_Underscore $a, $foo = null) {}

    /**
     * @param Foo $a
     * @param null|Foo|Bar $b
     */
    public function objectTypeCanBeSpecified(object $a, ?object $b = null) {}

    /**
     * @param int|Foo $a
     */
    public function objectTypeCannotBeSimpleType(object $a) {}

    /**
     * @param null|Foo[] $a
     */
    public function objectTypeCannotBeArray(?object $a) {}

    /**
     * @param iterable|int[] $a
     */
    public function iterableCanBeMixedWithOtherTypes(iterable $a) {}

    /**
     * @param null|int[] $a
     * @param null|string[] $b
     * @param null|float[] $c
     * @param null|bool[] $d
     * @param null|Foo $e
     */
    public function nullIsAllowed(
        ?array $a,
        ?iterable $b,
        ?\Traversable $c,
        ?\Generator $d,
        ?object $e
    ) {}
}
