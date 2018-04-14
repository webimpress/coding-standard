<?php

abstract class Hello {}

abstract class AbstractHello {}

abstract /** comment */
  class World
{
    abstract public function hello() : void;
}
