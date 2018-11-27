<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use WebimpressCodingStandard\Helper\AnnotationsTrait;

use const T_CLASS;
use const T_INTERFACE;
use const T_TRAIT;

class ClassAnnotationSniff implements Sniff
{
    use AnnotationsTrait;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
        ];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $this->processAnnotations($phpcsFile, $stackPtr);
    }
}
