<?php
// @phpcs:set WebimpressCodingStandard.Classes.AnonymousClassDeclaration requireParenthesis false
// @phpcs:set WebimpressCodingStandard.Classes.AnonymousClassDeclaration spacesBeforeBracket 1

$instance = new class
{
};

$instance = new class ()
{
};

$instance = new class /* comment */ () {
};

$instance = new class() implements \Countable{
};

$instance = new class extends \ArrayObject {
};

$instance = new class   extends    \ArrayObject   implements   \Countable
{
};

$instance = new class extends \ArrayObject implements
  \Countable,
     \Traversable {
};

$instance = new class(
  $param1  ,$param2,
     $param3
    ,$param4,
    $param5
) {
};

$instance = new class(
    new class($p1,$p2)implements \Countable {
    },
    array(),
    [],
    (function () {
    })(),
    $param
) extends \DateTime implements \Traversable {
};

$instance = new class
 extends
  \DateTime
implements
  \Countable
  /* comment */{
};

$instance = new class implements Foo,
  Bar,Baz,
  \Countable
  ,  \Traversable{
};

$instance = new class ($param
) {};

$instance = new class (
) {};

$instance = new class ( // comment
) {};

$instance = new class

() {
};

$instance = new class
// comment
() {
};

$instance = new class implements
\Foo\Bar\Baz,\Baz\Foo\Bar,Zoo,My\Zoo
{
};

$instance = new class extends \Foo implements Bar,Baz {
};

$instance = new class extends
Foo {
};

$instance = new class extends \Foo implements \Bar\Boo
    ,Baz {
};

$instance = new class() extends
 Foo
{
};

$instance = new class(
    (function () {
    })(),
    array(),
    [],
    $a ? ($b + 1) * $c : $y
) extends DateTime {
};
