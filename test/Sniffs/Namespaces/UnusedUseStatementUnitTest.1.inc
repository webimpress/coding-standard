<?php

use DateTime;
use ArrayObject as AO;

use function count;
use function array_keys as k;

use const T_WHITESPACE;
use const T_ARRAY as TA;

count([new DateTime(), T_WHITESPACE]);
k([new AO(), TA]);
