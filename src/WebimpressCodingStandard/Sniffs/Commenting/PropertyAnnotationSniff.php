<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use WebimpressCodingStandard\Helper\AnnotationsTrait;

class PropertyAnnotationSniff extends AbstractVariableSniff
{
    use AnnotationsTrait;

    /**
     * @param int $stackPtr
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr) : void
    {
        $this->processAnnotations($phpcsFile, $stackPtr);
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariable(File $phpcsFile, $stackPtr) : void
    {
        // Sniff process only class member vars.
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr) : void
    {
        // Sniff process only class member vars.
    }
}
