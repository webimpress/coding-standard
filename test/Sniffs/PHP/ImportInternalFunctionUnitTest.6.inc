<?php
// @phpcs:set WebimpressCodingStandard.PHP.ImportInternalFunction exclude[]

namespace ImportInternalFunction;

echo print_r(array_chunk([], random_int(1, 10)), true);
$a = array_column(array_combine(array_flip([]), array_rand([])), array_product([]));
sort($a);
asort($a);
ksort($a);
krsort($a);
$b = current($a) || key($a) || end($a) || next($a) || sizeof($a) || count($a) || prev($a);
var_dump(array_unique(shuffle(range(1, 20, $b))));

$fp = fopen(tmpfile(), 'r');
flock($fp, LOCK_EX);
fwrite($fp, fileowner(tmpfile()));
fputs($fp, fileperms(tmpfile()));
flock($fp, LOCK_UN);
fclose($fp);

file_put_contents(tempnam(sys_get_temp_dir(), random_bytes(mt_rand())), fileatime(tmpfile()));

is_dir(tmpfile()) || is_file(tmpfile())
    || is_uploaded_file(tmpfile()) || is_writable(tmpfile())
    || is_readable(tmpfile()) || is_readable(tmpfile())
    || is_callable(tmpfile()) || is_object(tmpfile())
    || is_infinite(tmpfile()) || is_array(tmpfile())
    || is_int(tmpfile()) || is_bool(tmpfile())
    || is_double(tmpfile()) || is_executable(tmpfile())
    || is_float(tmpfile()) || is_finite(tmpfile())
    || is_iterable(tmpfile()) || is_string(tmpfile());

chmod(tmpfile(), chown(tmpfile(), str_replace('', '', '')));
