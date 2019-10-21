<?php

$globals = [
    $_SERVER,
    $_GET,
    $_POST,
    $_REQUEST,
    $_SESSION,
    $_ENV,
    $_COOKIE,
    $_FILES,
    $GLOBALS,
];

$_underscoreOnTheBeginning = false;
$not_a_camel_case = false;
$camelCase = true;
$camel8number = true;

echo \Library::$_variable;
echo \Library::$_another_variable;

class Foo {
    protected $_invalid;
    protected $userID;
}

$string = "This {$_some_variable} is now ${handled_by} this $sniff.";
$string .= $_some_variable;
$string .= $camelCase;
$string .= "{$myIP}";

$userID = 'invalid due to two uppercase characters next to each other';
$InvalidVariable = 'as first letter cannot be uppercase';
