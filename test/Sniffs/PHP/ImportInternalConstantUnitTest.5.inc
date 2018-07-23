<?php

// @phpcs:set WebimpressCodingStandard.PHP.ImportInternalConstant exclude[] E_ERROR,JSON_ERROR_SYNTAX

namespace ImportInternalConstant;

use const JSON_ERROR_SYNTAX;

$a = E_ERROR;

$b = function ($x) use ($a) {
    return \E_WARNING;
};

$c = \E_ERROR
    & JSON_ERROR_SYNTAX
    | \JSON_ERROR_SYNTAX;
