<?php

switch (true) {
    case 1:
    case 2:
        echo 1;
        break;
    case 3:
    default:
        echo 2;
        break;
}

switch (true) {
    case 1:
        echo 1;
        break;
    default:
        break;
}

switch (true) {
    default:
    case 1:
        echo 1;
        break;
    case 2:
        echo 2;
        break;
    case 3:
        break;
}

switch (true) {
    case 1:
        echo 1;
        break;
    case 2:
    case 3:
        break;
    case 4:
        echo 4;
        break;
    case 5:
    case 6:
        return;
    default:
}

switch (true) {
    case 1:
        echo 1;
        break;
    case 2:
    default:
}

switch (true) {
    case 1:
        echo 1;
        break;
    case 2:
}

switch (true) {
    case 1:
    case 2:
    default:
        break;
    case 3:
        echo 3;
        break;
}

switch (true):
    case 1:
        echo 1;
    case 2:
    case 3:
endswitch;

switch (true):
    case 1:
        echo 1;
    default:
    case 2:
    case 3:
endswitch;

switch (true) {
    case 1:
        echo 1;
    case 2:
        break;
    case 3:
        echo 3;
        break;
    default:
        break (1);
}

switch (true) {
    case 1:
        echo 1;
    case 2:
        break;
    case 3:
        switch (true) {
            case 1:
                break;
            default:
                echo 'def';
        }
    default:
        break;
}

switch (true) {
    case 1:
        echo 1;
    case 2:
    default:
        break;
}

switch (true) {
    case 1:
        echo 1;
    case 2:
    default:
        break;
    case 3:
        echo 3;
        break;
}

switch (true) {
    case 1:
        echo 1;
    case 2:
        break;
    default:
        echo 3;
        return;
}

switch (true) {
    case 1:
        echo 1;
    case 2:
        break;
    default:
}

switch (true) {
    case 1:
        break;
}

switch (true) {
    case 1:
        break;
    default:
        ;
}

switch (true) {
    case 1:
        ;
    case 2:
        break;
    case 3:
        echo 3;
        break;
}

switch (true) {
    case 1:
        ;
    default:
        echo 'default';
        break;
}
