<?php

abstract class LineAfter
{
    public function __construct()
    {
    }
    public function method1()
    {
    }


    public function method2()
    {
    }




    public function method3(){}
    public function method4(){} public function method5(){}
    public function method6(){}private function method7(){}

    abstract public function method8();
    abstract public function method9();

    public function implemented()
    {
    } // comment here
    public function afterComment()
    {
    } /* another comment */public function inTheSameLine()
    {
    } # comment

    public function nextWithDocBlock()
    {
    }


    /**
     * @return int
     */
    public function returnInt()
    {
        return 0;
    }/**
     * @return string
     */
    public function returnString()
    {
        return '';
    }
}
