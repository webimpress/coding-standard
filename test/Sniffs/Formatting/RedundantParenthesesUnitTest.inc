<?php

class RedundantParentheses
{
    public function method1()
    {
        $this->method2((1 + 2), 2, 3);
        $arr = [
            (1 + 2) => 14,
            (2 + 3) * 2 => (1 + (4 * 3)),
            34 % (2) => 1 / (2 * 3),
            8 - (1) => 7 * (4),
            1 * (4 - 2) => 2,
            (11) << (2) => (5) | (6 - 2),
        ];
        sort($arr);

        $i1 = new self();
        $i2 = new static();
        $i3 = new DateTime();

        $anonym = new class() extends RedundantParentheses {
        };

        $v1 = new $anonym();
        $v2 = $arr();
        $v3 = static::method4();
        $v5 = self::method4();
        $v6 = $this->{$arr}($i1);

        $i = (32 * 1);
        $k = ($i + 1) << 3;
        $j = ($k) ? ($i + 2) : ($i - 2) / 2;

        return('hello');
    }

    public function method2($x, $y, $z)
    {
        switch ($x) {
            case (true):
                break;
            case (1):
                break;
        }

        return (($x || $y) && ($z));
    }

    public function method3($x, $y, $z)
    {
        return ($x && $y) || ($z);
    }

    public static function method4()
    {
        for ($i = (1 + 2); ($i < 15); (++$i)) {
            $i /= (2.5 + 2);
            echo($i << 3);
        }

        echo ($i), ($i - 1), ($i + 1), ($i - 1) * 2;

        return (function() use ($i) {
            return ('foo');
        });
    }

    public function method5()
    {
        return (function() {
            return('bar');
        })();
    }

    public function method6()
    {
        if (isset($x)) {
            if (empty($y)) {
                unset($x);
                unset($a, $b);
            }

            exit($x);
        }

        $c = eval('function(){}');

        return (1 + 2);
    }

    private function method7($x)
    {
        while (($x)) {
            $x = abs((--$x));
            if (($x) % 19) {
                die($x);
            }
        }

        return (1 + 2) * 3;
    }

    protected function method8($x, $y)
    {
        if (! ($x || $y)) {
            if (! ($x) || ! ($x + $y)) {
                echo $x, ($y);
            }
        }

        $z = ! ($x instanceof DateTime || ($x) instanceof ArrayAccess);

        return(1 / ($z ? 2 : 3));
    }

    public function method9($x, $y)
    {
        if (false === ($a = strpos($x, $y))) {
            return (3 ^ $a);
        }

        if (($b = strpos($x, $y)) != false) {
            return [$a, ($b - 2)];
        }

        $h = (int) ($a instanceof DateTime);
        $m = (int) ($a + $b);
        $n = (float) ($b);
        $w = (bool) ($x ^ $y);
        $z = (null === ($x ?: null));
        $q = 'string'
            . (! empty($x) ? ' ' . $x : '')
            . (! empty($y) ? ' ' . $y : '');
        $r = 1 + ($x ? 2 : 3);
        $s = (! (($x instanceof DateTime) || ($x instanceof ArrayAccess)));
        $p = $x ?: ($y ?: (2 + 1));
        $o = $x ? ($y ?: 1) : ($x - 1);

        return(! ($b || $a) || ($a - $b));
    }

    public function method10($a, $b, $c)
    {
        if (($a || $z = strpos($b, $c)) === false) {
            $z = clone($a);
        }

        $x = (int) ($a++);
        $y = ! ($z instanceof DateTime);
        $z = 'string ' . (--$a);
        $w = 7 !== (++$b);

        list($var) = explode(',', '1,2,3');

        $date = (new DateTime())->modify('+1 year');

        $a = $b ? ($c ?? 1) : 0;
        $a = $b ? 0 : ($c ?? 1);

        new $arr['abc']['def']();

        $r = $a === ($b + $c) * $d;
        $r = $a === $d * ($b + $c);
        $r = $a === $b + $c;
        $r = $a === ($b + $c);
    }

    public function method11()
    {
        for (($i=0); ($i<10); ($i++)) {
            for (($j=0); ($j<10); ($j++)) {
                if (($j+3)) {
                    break(2);
                } elseif (($j*4)) {
                    continue(2);
                } elseif (($i-5)) {
                    break (1);
                } elseif (($j%6)) {
                    continue (1);
                }
            }
        }

        if ($a === ($b + 1)) {
            echo 1;
        }
    }

    public function method12()
    {
        if (! (new Invoke())()) {}
        if (! (new Invoke())->method()) {}
    }
}
