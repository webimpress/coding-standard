<?php
namespace MyApp\MyNamespace;

class NoNullValues
{
    public $v1 = null;
    protected $v2=null;
    private $v3
        = null;

    public static $v4 =null;

    public $v5 = /* comment */ null;

    public ?\DateTime $d1 = null;
    public \DateTime $d2 = null;

    /**
     * @param mixed $x
     */
    public function __construct($x = null)
    {
        $var = null;

        $class = new class() {
            private $s1 = null;
        };

        $string = "String $var = null";
    }
}
