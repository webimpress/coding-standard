<?php
// @phpcs:set WebimpressCodingStandard.Formatting.ReturnType spacesBeforeColon 0
// @phpcs:set WebimpressCodingStandard.Formatting.ReturnType spacesAfterColon 1

$v = 1;

$f1 = function ($a) use ($v) : int {
    return $a * $v;
};

$f2 = function ($a, $b) : int {
    return $a ** $b;
};

switch ($v) {
    case 1:
        $x = $f1(-1 * $v);
        break;
    case (2) :
        $x = $f2(-1 * $v, $v);
        break;
    default:
        $x = $f1($v) + $f2(-1 * $v, $v);
        break;
}

class ReturnType
{
    public function method() :array
    {
        return [];
    }

    private function priv(
        $a,
        $b
    )
    : int {
        return $a * $b;
    }
}

$c = new class() {
    public function method() :
    float {
        return mt_rand();
    }
};

abstract class AbsCla
{
    abstract public function x() :bool;
}

interface MyInterface
{
    public function a():vOid;
    public function b() : Int;
    public function c() :?int;
    public function d() :Float;
    public function e() :? float;
    public function f():Object;
    public function g():?object;
    public function h():Array;
    public function i() : ?array;
    public function j():String;
    public function k():?string;
    public function l():Parent;
    public function m():?parent;
    public function n():Callable;
    public function o():?callable;
    public function p():Bool;
    public function q():?bool;
    public function r():Self;
    public function s():?self;
    public function t():Iterable;
    public function u():?iterable;

    public function v($a) :  \DateTime;
    public function w():?\DateTime;

    public function y($a, $b, $c) : \My\TestClass;
    public function z():? \ReturnType \MyType;
}

class NonFixable
{
    public function a() // comment
    : int {}

    public function b() /* comment */ : int {}

    public function c(): // comment
    int {}
    public function d(): /* comment */ int {}

    public function e() {}
}

static fn (string $a):int => $a;

abstract class PHP82 {
    abstract public function union(): Bool|String;
    abstract public function intersection(): Iterator&Countable;
    abstract public function boolFalse(): False;
    abstract public function boolTrue(): TRUE;
    abstract public function null(): NULL;
    abstract public function parenthesis(): (Iterator&Countable)|Object;
}
