<?php

namespace MyNamespace\Test;

use DateTime;
use RuntimeException as Exception;
use ZTest\MyNamespace\ParentClass as FunctionParamParent;

class FunctionParam extends FunctionParamParent
{
    public function paramType(DateTime $a) {}

    /**
     * @param DateTime $a
     */
    public function paramTypeInDocs($a) {}

    public function paramNeedsSpecification($a) {}

    public function arrayTypeDoesNotNeedSpecification(array $a) {}

    /**
     * @param array $a
     */
    public function arrayTypeDoesNotNeedSpecificationInDocs($a) {}

    /**
     * @param int[] $a
     */
    public function arrayTypeSpecified(array $a) {}

    /**
     * @param string $a
     */
    public function arrayTypeSpecifiedWrongly(array $a) {}

    public function traversableDoesNotNeedSpecification(\Traversable $a) {}

    /**
     * @param \Traversable $a
     */
    public function traversableDoesNotNeedSpecificationInDocs($a) {}

    /**
     * @param \ArrayAccess|\ArrayObject $a
     */
    public function traversableSpecified(\Traversable $a) {}

    /**
     * @param string|\DateTime[] $a
     */
    public function traversableSpecifiedWrongly(\Traversable $a) {}

    public function iterableDoesNotNeedSpecification(iterable $a) {}

    /**
     * @param iterable $a
     */
    public function iterableDoesNotNeedSpecificationInDocs($a) {}

    /**
     * @param MyIterableClass $a
     */
    public function iterableSpecified(iterable $a) {}

    /**
     * @param int|DateTime[] $a
     */
    public function iterableSpecifiedWrongly(iterable $a) {}

    /**
     * @param mixed $a
     */
    public function mixedParamIsAllowed($a) {}

    /**
     * @param mixed[] $a
     */
    public function mixedArrayParamIsAllowed(array $a) {}

    public function multipleParamsOneNeedsSpecification($a, DateTime $v) {}

    public function multipleParamsAnyDoesNotNeedClarification(DateTime $a, array $b) {}

    public function multipleParamsAllNeedsSpecification($a, $b, $c) {}

    /**
     * @param int ...$a
     */
    public function variadicParamWithoutTypeHint(...$a) {}

    /**
     * @param string ...$a
     */
    public function variadicParamTagRedundant(string ...$a) {}

    /**
     * @param string[] ...$a
     */
    public function variadicParamWrongTag1(string ...$a) {}

    /**
     * @param array[] ...$a
     */
    public function variadicParamWrongTag2(array ...$a) {}

    public function invalidTypeHintCase1(String $a) {}

    public function invalidTypeHintCase2(?INT $a) {}

    /**
     * @param int $inconsistentPARAMname
     */
    public function inconsistentParamName($a, $inconsistentParamName) {}

    /**
     * @param string[] $a
     */
    public function variadicParam(...$a) {}

    public function selfAndParent(FunctionParam $a, FunctionParamParent $b) {}

    public function nullableSelfAndParent(?FunctionParam $a, ?FunctionParamParent $b) {}

    /**
     * @param DateTime $a
     * @param \DateTime $b
     * @param DateTime $c
     * @param \DateTime $d
     * @param null|DateTime $e
     * @param null|\DateTime $f
     * @param null|DateTime $g
     * @param null|\DateTime $h
     */
    public function fcqnAndImportedClass(
        DateTime $a,
        DateTime $b,
        \DateTime $c,
        \DateTime $d,
        ?DateTime $e,
        ?DateTime $f,
        ?\DateTime $g,
        ?\DateTime $h
    ) {}

    /**
     * @param int[] $a
     */
    public function missingNullInTag(?iterable $a) {}

    public function selfInsteadOfClassName(\MyNamespace\Test\FunctionParam $a) {}

    public function parentInsteadOfFCQNParentClassName(\ZTest\MyNamespace\ParentClass $b) {}

    /**
     * @param \DateTime $a Description.
     * @param null|DateTime ...$b
     */
    public function canUseImportedClasses(\DateTime $a, ?\DateTime ...$b) {}

    /**
     * @param int $a
     * @param string $a
     */
    public function duplicatedTagForTheSameParam($a) {}

    /**
     * @param float $a
     * @param false $b
     * @param object $c
     */
    public function inconsistentPrimitiveTypes(int $a, bool $b, string $c) {}

    /**
     * @param int $c
     */
    public function invalidParamDoc() {}

    /**
     * @param string $a
     * @param Exception $b
     */
    public function wrongType(DateTime $a, \Throwable $b) {}

    /**
     * @param string $a
     * @param array $b
     * @param \Traversable $c
     * @param iterable $d
     */
    public function someTypesNeedsSpecification($a, $b, $c, $d, \Exception $e, Exception $f) {}

    /**
     * @param DateTime $a
     */
    public function paramContainAnotherType(\DateTimeInterface $a) {}

    /**
     * @param bool $a
     * @param null|DateTime|Something $b
     * @param string|DateTime|Something $c
     * @param null|DateTime $d
     * @param null|DateTime $e
     * @param null|string $g
     */
    public function defaultValueAndNull(
        $a,
        ?DateTime $b,
        DateTime $c,
        DateTime $d,
        DateTime $e = null,
        string $f = null,
        string $g = null,
        string $h = 'null'
    ) {}

    /**
     * @param string[] $a
     * @param null|int $b
     * @param int|string $c
     */
    public function inconsistentParamTypesInTag($a, int $b, string $c) {}

    /**
     * @param int $d This is very Long
     *     description of the last param.
     * @return string Here is description
     *     of the return value.
     * @param bool $c Another param description
     *     It is also.
     *     Very long.
     *
     * @param string $a
     */
    public function wrongParamOrder($a, DateTime $b, $c, $d) {}

    public function paramDoesNotNeedTag1(string $a = null) {}

    /** */
    public function paramDoesNotNeedTag2(int $int = null) {}

    /**
     * @param null|int $a
     */
    public function paramHasWrongType1(bool $a = null) {}

    /**
     * @param string $a
     */
    public function paramHasWrongType2(int $a) {}

    public function generatorDoesNotNeedSpecification(\Generator $a) {}

    /**
     * @param \Generator $a
     */
    public function generatorDoesNotNeedSpecificationInDocs($a) {}

    private function privateMethod(CALLABLE $o, Array $p) {}

    private function fqcnParam(\RuntimeException\Hello $world) {}
}

interface MyInterface extends MyOne, MyTwo
{
    public function method1(MyInterface $self);

    public function method2(MyOne $myOne);

    public function method3(MyTwo $myTwo);

    /**
     * @param array|int[] $a1
     * @param null|array $a2 Description
     * @param null|array|int[] $a3 Description
     */
    public function typeArray(
        array $a1,
        ?array $a2,
        ?array $a3 = null
    ) : void;

    /**
     * @param iterable|int[] $i1
     * @param null|iterable $i2 Description
     * @param null|iterable|int[] $i3 Description
     */
    public function typeIterable(
        iterable $i1,
        ?iterable $i2,
        ?iterable $i3 = null
    ) : void;

    /**
     * @param \Traversable|int[] $t1
     * @param null|\Traversable $t2 Description
     * @param null|\Traversable|int[] $t3 Description
     */
    public function typeTraversable(
        \Traversable $t1,
        ?\Traversable $t2,
        ?\Traversable $t3 = null
    ) : void;

    /**
     * @param \Generator|int[] $g1
     * @param null|\Generator $g2 Description
     * @param null|\Generator|int[] $g3 Description
     */
    public function typeGenerator(
        \Generator $g1,
        ?\Generator $g2,
        ?\Generator $g3 = null
    ) : void;

    /**
     * @param object|Foo $o1
     * @param null|object $o2 Description
     * @param null|object|Foo $o3 Description
     */
    public function typeObject(
        object $o1,
        ?object $o2,
        ?object $o3 = null
    ) : void;

    /**
     * @param array $a1
     * @param null|array $a2
     * @param array|int[] $a3
     * @param object $o1
     * @param null|object $o2
     * @param object|Foo $o3
     * @param iterable $i1
     * @param null|iterable $i2
     * @param iterable|Bar $i3
     */
    public function redundantTypes(
        array $a1,
        ?array $a2,
        ?array $a3,
        object $o1,
        ?object $o2,
        ?object $o3,
        iterable $i1,
        ?iterable $i2,
        ?iterable $i3
    ) : void;

    /**
     * @param   int     $p1
     */
    public function moreThanOneSpace(int $p1) : void;
}
