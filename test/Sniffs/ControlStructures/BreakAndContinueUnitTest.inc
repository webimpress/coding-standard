<?php

for ($j = 0; $j < 10; ++$j) {
    if ($j === 0) {
        continue;
    }

    for ($i = 0; $i < 10; ++$i) {
        if ($i === 0) {
            continue 1;
        }

        if ($i === 1) {
            continue   2;
        }

        if ($i === 2) {
            break 1;
        }

        if ($i === 3) {
            break
            2;
        }
    }
}
